<?php

namespace Aheenam\Translatable;

use Illuminate\Support\Facades\App;

trait Translatable
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function allTranslations()
    {
        $translations = collect([]);

        $attributes = $this->getAttributes();

        $locales = $this->translations()->get()->groupBy('locale')->keys();

        foreach ($locales as $locale) {
            $translation = collect([]);

            foreach ($attributes as $attribute => $value) {
                if ($this->isTranslatableAttribute($attribute) && $this->hasTranslation($locale, $attribute)) {
                    $translation->put($attribute, $this->getTranslation($attribute, $locale));
                } else {
                    $translation->put($attribute, parent::getAttributeValue($attribute));
                }
            }

            $translations->put($locale, $translation);
        }

        return $translations;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        if (!$this->isTranslatableAttribute($key) || config('app.fallback_locale') == App::getLocale()) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, App::getLocale());
    }

    /**
     * returns all attributes that are translatable.
     *
     * @return array
     */
    public function getTranslatableAttributes()
    {
        /* @noinspection PhpUndefinedFieldInspection */
        return (property_exists(static::class, 'translatable') && is_array($this->translatable))
            ? $translatableAttributes = $this->translatable
            : [];
    }

    /**
     * @param $locale
     *
     * @return Translatable
     */
    public function in($locale)
    {
        $translatedModel = new self();

        foreach ($this->getAttributes() as $attribute => $value) {
            if ($this->isTranslatableAttribute($attribute)) {
                if ($this->hasTranslation($locale, $attribute)) {
                    $translatedModel->setAttribute($attribute, $this->getTranslation($attribute, $locale));
                } else {
                    $translatedModel->setAttribute($attribute, $this->getAttribute($attribute));
                }
            } else {
                $translatedModel->setAttribute($attribute, $this->getAttribute($attribute));
            }
        }

        return $translatedModel;
    }

    /**
     * @param $locale
     */
    public function removeTranslationIn($locale)
    {
        $this->translations()
            ->where('locale', $locale)
            ->delete();
    }

    /**
     * @param $locale
     * @param $attribute
     */
    public function removeTranslation($locale, $attribute)
    {
        $this->translations()
            ->where('locale', $locale)
            ->where('key', $attribute)
            ->delete();
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * returns the translation of a key for a given key/locale pair.
     *
     * @param $key
     * @param $locale
     *
     * @return mixed
     */
    protected function getTranslation($key, $locale)
    {
        return $this->translations()
            ->where('key', $key)
            ->where('locale', $locale)
            ->value('translation');
    }

    /**
     * @param $locale
     * @param $attribute
     *
     * @return bool
     */
    protected function hasTranslation($locale, $attribute)
    {
        $translation = $this->translations()
            ->where('locale', $locale)
            ->where('key', $attribute)
            ->first();

        return $translation !== null;
    }

    /**
     * returns if given key is translatable.
     *
     * @param $key
     *
     * @return bool
     */
    protected function isTranslatableAttribute($key)
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    /**
     * @param $locale
     * @param $attribute
     * @param $translation
     *
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
     * @param $locale
     * @param $translations
     *
     * @return void
     */
    protected function setTranslationByArray($locale, $translations)
    {
        foreach ($translations as $attribute => $translation) {
            if ($this->isTranslatableAttribute($attribute)) {
                $storedTranslation = $this->translations()
                    ->where('locale', $locale)
                    ->where('key', $attribute)
                    ->first();

                if ($storedTranslation) {
                    $this->updateTranslation($locale, $attribute, $translation);
                } else {
                    $this->setTranslation($locale, $attribute, $translation);
                }
            }
        }
    }

    /**
     * returns the translation of a key for a given key/locale pair.
     *
     * @param $key
     * @param $locale
     *
     * @return mixed
     */
    protected function translateAttribute($key, $locale)
    {
        if (!$this->isTranslatableAttribute($key) || config('app.fallback_locale') == $locale) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, $locale);
    }

    /**
     * @param $locale
     * @param $attribute
     * @param $translation
     *
     * @return void
     */
    protected function updateTranslation($locale, $attribute, $translation)
    {
        $this->translations()
            ->where('key', $attribute)
            ->where('locale', $locale)
            ->update([
                'translation' => $translation,
            ]);
    }

    /**
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($method === 'translate' && count($arguments) === 2 && is_array($arguments[1])) {
            return call_user_func_array([$this, 'setTranslationByArray'], $arguments);
        } elseif ($method === 'translate' && count($arguments) === 2 && !is_array($arguments[1])) {
            return call_user_func_array([$this, 'translateAttribute'], $arguments);
        }

        return call_user_func_array([$this, $method], $arguments);
    }
}
