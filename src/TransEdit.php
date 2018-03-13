<?php
namespace Dialect\TransEdit;

use Dialect\TransEdit\Models\Key;
use Dialect\TransEdit\Models\Locale;
use Dialect\TransEdit\Models\Translation;
use Illuminate\Support\HtmlString;

class TransEdit{
	protected $locale;
	protected $editMode;
	function __construct($editMode = false) {

		$this->setLocaleToCurrent();
		$this->editMode = $editMode ? $editMode : session('transedit-edit-mode-on');

	}

	public function locale($locale = 'current'){
		if(!$locale || $locale === 'current'){
			$this->setLocaleToCurrent();
		}
		else{
			$this->locale = $locale;
		}

		return $this;
	}

	public function localeExists($locale){
		return $locale::where('name', $locale)->first() != null;
	}

	private function setLocaleToCurrent(){
		$this->locale = session('transedit-current-locale', config('transedit.default_locale', 'en'));
	}

	public function key($key, $val = null){
		if($val){
			return $this->setKey($key, $val);
		}

		return $this->getKey($key);
	}

	public function setKey($key, $val){
		$localeModel = Locale::firstOrCreate([
			'name' => $this->locale
		]);
		$keyModel = Key::firstOrCreate([
			'name' => $key
		]);

		Translation::firstOrCreate([
			'locale_id' => $localeModel->id,
			'key_id' => $keyModel->id
		])->update([
			'value' => $val
		]);

		return $this;
	}

	public function getKey($key){

		$translation = Translation::whereHas('locale', function($query){
			return $query->where('name', $this->locale);
		})->whereHas('key', function($query) use($key){
			return $query->where('name', $key);
		})->first();
		$value = $translation ? $translation->value : $key;
		if($this->editMode){
			return $this->returnVueComponent($key, $value);
		}

		return $value;
	}

	protected function returnVueComponent($key, $val){

		return new HtmlString('<transedit tekey="'.htmlentities($key).'" teval="'.htmlentities($val).'"></transedit>');
	}




}