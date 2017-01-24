<?php

namespace Aheenam\Translatable;

use Illuminate\Support\Facades\App;

trait Translatable {


    /**
     * @param $key
     * @return mixed
     */
    public function getAttributeValue($key )
    {

        if (!$this->isTranslatableAttribute($key)) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, App::getLocale());

    }

    /**
     * returns the translation of a key for a given key/locale pair
     *
     * @param $key
     * @param $locale
     * @return mixed
     */
    public function translateAttribute( $key, $locale )
    {
        return $this->getTranslation($key, $locale);
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
     * returns if given key is translatable
     *
     * @param $key
     * @return bool
     */
    protected function isTranslatableAttribute($key)
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

}