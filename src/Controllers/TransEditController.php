<?php
namespace Dialect\TransEdit\Controllers;
use Dialect\TransEdit\Models\Locale;

use Illuminate\Routing\Controller as BaseController;

class TransEditController extends BaseController {

	public function setKey(){
		transEdit()->locale(request('locale'))->setKey(request('key'), request('val'));

		return transEdit(request('key'));
	}

	public function locales(){
		return Locale::all();
	}

}