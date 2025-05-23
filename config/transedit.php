<?php

return [
    'default_locale' => env('TRANSEDIT_DEFAULT_LOCALE', 'en'),
    //set to null to disable fallback locale
    'fallback_locale' => env('TRANSEDIT_FALLBACK_LOCALE', 'en'),

    // Enable to cache all keys
    'use_cache' => env('TRANSEDIT_CACHE', true),
    'case_sensitive' => env('TRANSEDIT_CASE_SENSITIVE', true),
];
