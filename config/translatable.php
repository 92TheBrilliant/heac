<?php

return [
    /*
     * If a translation has not been set for a given locale, use this locale instead.
     */
    'fallback_locale' => 'en',

    /*
     * The locales that are supported by the application.
     */
    'locales' => [
        'en' => 'English',
        'ar' => 'العربية',
    ],

    /*
     * The locale to use if the current locale is not in the `locales` array.
     */
    'locale_separator' => '-',

    /*
     * If you want to use country specific locales (like en-US, en-GB, etc.)
     * set this to true.
     */
    'use_property_fallback' => true,

    /*
     * If you want to use the fallback locale when a translation is empty,
     * set this to true.
     */
    'fallback_any_locale' => true,
];
