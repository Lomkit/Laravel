<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Translate Key
    |--------------------------------------------------------------------------
    |
    | This is the key from google translate that will allow us to translate
    | the content.
    |
    */

    'google_translate_key_path' => storage_path('app/google-key.json'),

    /*
    |--------------------------------------------------------------------------
    | Lomkit locales
    |--------------------------------------------------------------------------
    |
    | This option is used to define the locales, it uses by default
    | the nova package to set locales but you can use your own.
    |
    */

    'locales' => config('nova-translatable.locales', []),

    /*
    |--------------------------------------------------------------------------
    | Lomkit statuses
    |--------------------------------------------------------------------------
    |
    | This option is used to define the statuses for automatic translations,
    | you can override this to set your own text.
    |
    */

    'statuses' => [
        'waiting_translation' => 'waiting_translation',
        'translating' => 'translating',
        'waiting_approval' => 'waiting_approval',
        'translated' => 'translated',
    ]
];
