<?php

$translations = [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Deze gegevens komen niet overeen met onze records.',
    'throttle' => 'Teveel inlog pogingen. Probeer opnieuw in :seconds seconden.',

];

return \FI\Support\TranslationOverride::override(__FILE__, $translations);