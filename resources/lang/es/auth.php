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

    'failed' => 'Estas credenciales no coinciden en nuestros registros.',
    'throttle' => 'Demasiados intentos. Por favor vuelve a intentarlo después de :seconds segundos.',

];

return \FI\Support\TranslationOverride::override(__FILE__, $translations);
