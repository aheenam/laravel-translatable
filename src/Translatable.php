<?php

namespace Aheenam\Translatable;

use Illuminate\Support\Facades\App;

trait Translatable {


    /**
     * @param $key
     * @return mixed
     */
    public function getAttributeValue( $key )
    {

        if (!$this->isTranslatableAttribute($key) || config('app.fallback_locale') == App::getLocale() ) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, App::getLocale());

    }


    /**
     * @param $locale
     * @return Translatable
     */
    public function in( $locale )
    {

        $translatedModel = new self();

        foreach ( $this->getAttributes() as $attribute => $value ) {

            if ( $this->isTranslatableAttribute( $attribute ) ) {
                $translatedModel->setAttribute($attribute, $this->getTranslation($attribute, $locale));
            } else {
                $translatedModel->setAttribute($attribute, $this->getAttribute($attribute));
            }

        }

        return $translatedModel;

    }

    /**
     * returns the translation of a key for a given key/locale pair
     *
     * @param $key
     * @param $locale
     * @return mixed
     */
    protected function getTranslation( $key, $locale )
    {
        return $this->translations()
            ->where('key', $key)
            ->where('locale', $locale)
            ->value('translation');
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * returns all attributes that are translatable
     *
     * @return array
     */
    public function getTranslatableAttributes()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return ( property_exists(static::class, 'translatable') && is_array($this->translatable) )
            ? $translatableAttributes = $this->translatable
            : [];
    }



    /**
     * returns the translation of a key for a given key/locale pair
     *
     * @param $key
     * @param $locale
     * @return mixed
     */
    protected function translateAttribute( $key, $locale )
    {
        return $this->getTranslation($key, $locale);
    }



    /**
     * @param $locale
     * @param $translations
     * @return void
     */
    protected function setTranslationByArray ( $locale, $translations )
    {
        foreach ( $translations as $attribute => $translation ) {
            if ( $this->isTranslatableAttribute($attribute) ) {
                $this->setTranslation($locale, $attribute, $translation);
            }
        }
    }



    /**
     * @param $locale
     * @param $attribute
     * @param $translation
     * @return void
     */
    protected function setTranslation($locale, $attribute, $translation)
    {
        $this->translations()->create([
            'key'               => $attribute,
            'translation'       => $translation,
            'locale'            => $locale,
        ]);
    }

    /**
     * returns if given key is translatable
     *
     * @param $key
     * @return bool
     */
    protected function isTranslatableAttribute($key)
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    /**
     * @param $method
     * @param $arguments
     * @return bool|mixed
     */
    public function __call($method, $arguments)
    {

        if ( $method === 'translate' && count($arguments) === 2 && is_array($arguments[1]) ) {
            return call_user_func_array([$this, 'setTranslationByArray'], $arguments);
        }

        return false;

    }

}