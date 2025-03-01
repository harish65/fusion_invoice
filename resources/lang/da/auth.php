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

    'failed' => 'Disse legitimationsoplysninger stemmer ikke overens med vores optegnelser.',
    'throttle' => 'For mange loginforsøg. Prøv igen om: sekunder sekunder.',

];


return \FI\Support\TranslationOverride::override(__FILE__, $translations);
