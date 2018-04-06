<?php

namespace Dialect\TransEdit;

use Dialect\TransEdit\Models\Key;
use Illuminate\Support\HtmlString;
use Dialect\TransEdit\Models\Locale;
use Dialect\TransEdit\Models\Translation;

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

    public function key($key, $val = null)
    {
        if ($val) {
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

        return $this;
    }

    public function getKey($key, $locale = null)
    {
        $translation = $this->getTranslationFromKey($key, $locale);

        if (! $translation && config('transedit.fallback_locale')) {
            $translation = $this->getTranslationFromKey($key, config('transedit.fallback_locale'));
        }
        $value = $translation ? $translation->value : $key;
        if ($this->editMode) {
            return $this->returnVueComponent($key, $value);
        }

        return $value;
    }

    protected function getTranslationFromKey($key, $locale)
    {
        return $translation = Translation::whereHas('locale', function ($query) use ($locale) {
            return $query->where('name', $locale ? $locale : $this->locale);
        })->whereHas('key', function ($query) use ($key) {
            return $query->where('name', $key);
        })->first();
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

    public function getAllTranslationsForLocale($locale){
    	$locale = Locale::where('name', $locale)->first();

    	if(!$locale) return collect();
    	$keys = Key::all();

    	$translations = collect();
    	foreach($keys as $key){
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
