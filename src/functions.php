<?php

if (! function_exists('transEdit')) {
	/**
	 * Get an instance of TransEdit
	 * @return \Dialect\TransEdit\TransEdit
	 */
	function transEdit($key = null,$val = null, $locale = 'default') {
		$app = app('transedit');
		if($key && !$val){
			return $app->locale($locale)->key($key);
		}elseif($key && $val){
			return $app->locale($locale)->key($key, $val);
		}
		return $app;
	}

}