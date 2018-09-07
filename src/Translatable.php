<?php

namespace Aheenam\Translatable;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Collection;

trait Translatable
{
    public function allTranslations(): Collection
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
     * @param string $key
     *
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        if (! $this->isTranslatableAttribute($key) || config('app.fallback_locale') == App::getLocale()) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, App::getLocale());
    }

    public function getTranslatableAttributes(): array
    {
        /* @noinspection PhpUndefinedFieldInspection */
        return (property_exists(static::class, 'translatable') && is_array($this->translatable))
            ? $translatableAttributes = $this->translatable
            : [];
    }

    /**
     * @return Translatable
     */
    public function in(string $locale)
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

    public function removeTranslationIn(string $locale)
    {
        $this->translations()
            ->where('locale', $locale)
            ->delete();
    }

    public function removeTranslation(string $locale, string $attribute)
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
     * @return mixed
     */
    protected function getTranslation(string $key, string $locale)
    {
        return $this->translations()
            ->where('key', $key)
            ->where('locale', $locale)
            ->value('translation');
    }

    protected function hasTranslation(string $locale, string $attribute): bool
    {
        $translation = $this->translations()
            ->where('locale', $locale)
            ->where('key', $attribute)
            ->first();

        return $translation !== null;
    }

    protected function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    protected function setTranslation(string $locale, string $attribute, string $translation): void
    {
        $this->translations()->create([
            'key'               => $attribute,
            'translation'       => $translation,
            'locale'            => $locale,
        ]);
    }

    protected function setTranslationByArray(string $locale, array $translations): void
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
     * @return mixed
     */
    protected function translateAttribute(string $key, string $locale)
    {
        if (! $this->isTranslatableAttribute($key) || config('app.fallback_locale') == $locale) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, $locale);
    }

    protected function updateTranslation(string $locale, string $attribute, string $translation): void
    {
        $this->translations()
            ->where('key', $attribute)
            ->where('locale', $locale)
            ->update([
                'translation' => $translation,
            ]);
    }

    /**
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($method === 'translate' && count($arguments) === 2 && is_array($arguments[1])) {
            return call_user_func_array([$this, 'setTranslationByArray'], $arguments);
        } elseif ($method === 'translate' && count($arguments) === 2 && ! is_array($arguments[1])) {
            return call_user_func_array([$this, 'translateAttribute'], $arguments);
        }

        return call_user_func_array([$this, $method], $arguments);
    }
}
