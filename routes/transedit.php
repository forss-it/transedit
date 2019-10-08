<?php

Route::post('/transedit/setkey', '\Dialect\TransEdit\Controllers\TransEditController@setKey');

Route::get('/transedit/locales', '\Dialect\TransEdit\Controllers\TransEditController@locales');

Route::post('/transedit/setlocale', '\Dialect\TransEdit\Controllers\TransEditController@setCurrentLocale');

Route::get('/js/transedit.js', function () {
    if(config('transedit.use_cache')) {
        $translations = cache()->rememberForever('transedit.js', function() {
            return transEdit()->getAllTranslationsForLocale(request('v') ?: transEdit()->getCurrentLocale())->toArray();
        });
    } else {
        $translations = transEdit()->getAllTranslationsForLocale(request('v') ?: transEdit()->getCurrentLocale())->toArray();
    }

    $js = ('window.transEditTranslations = '.json_encode($translations).';');
    $js .= 'window.transEdit = function(key){ var translation = window.transEditTranslations[key]; if(!translation){ return key; } return translation; }';

    return response($js)->header('Content-Type', 'application/javascript');
});
