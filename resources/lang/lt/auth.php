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

    'failed' => 'Šie įgaliojimai neatitinka mūsų įrašų.',
    'throttle' => 'Per daug bandymų prisijungti. Bandykite dar kartą po :seconds sekundžiu.',

];


return \FI\Support\TranslationOverride::override(__FILE__, $translations);
