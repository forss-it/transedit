<?php
namespace Dialect\TransEdit\Controllers;
use Dialect\TransEdit\Models\Locale;
use Illuminate\Routing\Controller;

class TransEditController extends Controller{

	public function setKey(){
		if(!session('transedit-edit-mode-in')){
			abort(403);
		}
		transEdit()->locale(request('locale'))->setKey(request('key'), request('val'));

		return transEdit(request('key'));
	}

	public function locales(){
		return Locale::all();
	}

}