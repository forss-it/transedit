<?php
namespace Dialect\TransEdit\Controllers;
use Dialect\TransEdit\Models\Locale;

use Illuminate\Routing\Controller as BaseController;

class TransEditController extends BaseController {

	public function setKey(){
		transEdit()->locale(request('locale'))->setKey(request('key'), request('val'));

		return transEdit(request('key'));
	}

	public function setCurrentLocale(){
		if(!transEdit()->localeExists(request('locale'))){
			session(['transedit-current-locale' => request('locale')]);
		}
		else{
			session(['transedit-current-locale' => null]);
		}

		return redirect()->back();
	}

	public function locales(){
		return Locale::all();
	}

}