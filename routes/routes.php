<?php

Route::post('/transedit/setkey', '\Dialect\TransEdit\Controllers\TransEditController@setKey');
Route::get('/transedit/locales', '\Dialect\TransEdit\Controllers\TransEditController@locales');