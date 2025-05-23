<?php

Route::middleware(['web'])->group(function () {
    Route::post('/transedit/setkey', '\Dialect\TransEdit\Controllers\TransEditController@setKey');

    Route::get('/transedit/locales', '\Dialect\TransEdit\Controllers\TransEditController@locales');

    Route::post('/transedit/setlocale', '\Dialect\TransEdit\Controllers\TransEditController@setCurrentLocale');

    Route::get('/js/transedit.js', function () {
        $locale = request('v') ?: transEdit()->getCurrentLocale();

        if (config('transedit.use_cache')) {
            $translations = cache()->rememberForever("transedit.js.$locale", function () use ($locale) {
                return transEdit()->getAllTranslationsForLocale($locale)->toArray();
            });
        } else {
            $translations = transEdit()->getAllTranslationsForLocale($locale)->toArray();
        }

        // Convert keys to lowercase if case_sensitive is false
        if (!config('transedit.case_sensitive', true)) {
            $translations = array_change_key_case($translations, CASE_LOWER);
        }

        $js = ('window.transEditTranslations = '.json_encode($translations, JSON_UNESCAPED_UNICODE).';');
        $js .= <<<EOD
            window.transEdit = function(key, values = []) {
                let translation = window.transEditTranslations[key.toLowerCase()] || window.transEditTranslations[key];

                if (!translation) {
                    translation = key;
                }

                try {
                    let matches = translation.match(/(\\\$\d)/g);
                    if (matches) {
                        for (let i = 0; i < matches.length; i++) {
                            let key = parseInt(matches[i].substring(1)) - 1;
                            if (key in values) {
                                translation = translation.replace(matches[i], values[key]);
                            }
                        }
                    }
                } catch (e) {
                    //
                }

                return translation;
            }
EOD;

        return response($js)->header('Content-Type', 'application/javascript');
    });
});