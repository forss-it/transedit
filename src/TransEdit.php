<?php

namespace Dialect\TransEdit;

use Dialect\TransEdit\Models\Key;
use Dialect\TransEdit\Models\Locale;
use Dialect\TransEdit\Models\Translation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class TransEdit
{
    protected $locale;
    protected $editMode;

    public function __construct($editMode = false)
    {
        $this->setLocaleToCurrent();
        $this->editMode = $editMode ? $editMode : session('transedit-edit-mode-on');
    }

    public function locale($locale = 'current')
    {
        if (! $locale || $locale === 'current') {
            $this->setLocaleToCurrent();
        } else {
            $this->locale = $locale;
        }

        return $this;
    }

    public function setCurrentLocale($locale)
    {
        if (transEdit()->localeExists($locale)) {
            session(['transedit-current-locale' => $locale]);
        } else {
            session(['transedit-current-locale' => null]);
        }
        $this->setLocaleToCurrent();

        return $this;
    }

    public function localeExists($locale)
    {
        return Locale::where('name', $locale)->first() != null;
    }

    public function setLocaleLanguageName($locale, $language)
    {
        $localeModel = Locale::firstOrCreate([
            'name' => $locale,
        ]);
        $localeModel->update([
            'language' => $language,
        ]);

        return $this;
    }

    private function setLocaleToCurrent()
    {
        $this->locale = session('transedit-current-locale', config('transedit.default_locale', 'en'));
    }

    public function getCurrentLocale()
    {
        return $this->locale;
    }

    public function replaceVariables($key, array $values)
    {
        $value = $this->getKey($key);

        // Create a case-insensitive version of $values for named variables
        $valuesInsensitive = array_change_key_case($values, CASE_LOWER);

        // Find all matches of variables: $ followed by digits or word characters
        preg_match_all('/\$(\d+|\w+)/', $value, $matches);

        foreach ($matches[0] as $i => $fullMatch) {
            $var = $matches[1][$i]; // Variable name (number or word)

            if (ctype_digit($var)) {
                // Unnamed variable (e.g., $1, $2)
                $index = ((int) $var) - 1;
                if (array_key_exists($index, $values)) {
                    $value = str_replace($fullMatch, $values[$index], $value);
                }
            } else {
                // Named variable (e.g., $NAME, $PLATFORM)
                $varLower = strtolower($var);
                if (array_key_exists($varLower, $valuesInsensitive)) {
                    $value = str_replace($fullMatch, $valuesInsensitive[$varLower], $value);
                }
            }
        }

        return $value;
    }

    public function key($key, $val = null)
    {
        if (is_array($val)) {
            return $this->replaceVariables($key, $val);
        } elseif ($val) {
            return $this->setKey($key, $val);
        }

        return $this->getKey($key);
    }

    public function setKey($key, $val)
    {
        $localeModel = Locale::firstOrCreate([
            'name' => $this->locale,
        ]);
        $keyModel = Key::firstOrCreate([
            'name' => $key,
        ]);

        Translation::firstOrCreate([
            'locale_id' => $localeModel->id,
            'key_id' => $keyModel->id,
        ])->update([
            'value' => $val,
        ]);

        if (config('transedit.use_cache')) {
            Cache::put("{$this->locale}.{$key}", $val);
        }

        return $this;
    }

    public function getKey($key, $locale = null)
    {
        $translation = $this->getTranslationFromKey($key, $locale);

        if (! $translation && config('transedit.fallback_locale')) {
            $translation = $this->getTranslationFromKey($key, config('transedit.fallback_locale'));
        }
        $value = $translation ? $translation : $key;
        if ($this->editMode) {
            return $this->returnVueComponent($key, $value);
        }

        return $value;
    }

    protected function getTranslationFromKey($key, $locale)
    {
        if (config('transedit.case_sensitive') && ! is_null($key)) {
            $key = strtolower($key);
        }
        $locale = $locale ?: $this->getCurrentLocale();

        if (config('transedit.use_cache') && Cache::has("{$locale}.{$key}")) {
            return Cache::get("{$locale}.{$key}");
        }

        $translation = Translation::whereHas('locale', function ($query) use ($locale) {
            return $query->where('name', $locale ? $locale : $this->locale);
        })->whereHas('key', function ($query) use ($key) {
            return $query->where('name', $key);
        })->first();

        if (config('transedit.use_cache')) {
            Cache::forever("{$locale}.{$key}", $translation && $translation->value ? $translation->value : false);
        }

        return $translation ? $translation->value : $key;
    }

    protected function returnVueComponent($key, $val)
    {
        return new HtmlString('<transedit tekey="'.htmlentities($key).'" teval="'.htmlentities($val).'"></transedit>');
    }

    public function getAllTranslationsForKey($key)
    {
        $result = collect();
        $locales = Locale::all();
        foreach ($locales as $locale) {
            $result->put($locale->name, $this->getKey($key, $locale->name));
        }

        return $result;
    }

    public function getAllTranslationsForLocale($locale)
    {
        $locale = Locale::where('name', $locale)->first();

        if (! $locale) {
            return collect();
        }
        $keys = Key::all();

        $translations = collect();
        foreach ($keys as $key) {
            $translations->put($key->name, $this->getKey($key->name, $locale->name));
        }

        return $translations;
    }

    public function enableEditMode()
    {
        session(['transedit-edit-mode-on' => true]);
        $this->editMode = true;
    }

    public function disableEditMode()
    {
        session(['transedit-edit-mode-on' => false]);
        $this->editMode = false;
    }

    public function editModeIsEnabled()
    {
        return $this->editMode;
    }
}
