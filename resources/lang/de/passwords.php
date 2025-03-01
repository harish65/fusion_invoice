<?php

$translations = [

    /*
    |--------------------------------------------------------------------------
    | Password Reminder Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'password' => 'Passw&ouml;rter m&uuml;ssen aus mindestens sechs Zeichen bestehen und mit der Wiederholung &uuml;bereinstimmen.',
    'user' => "Es wurde kein Nutzer mit dieser E-Mail Adresse gefunden.",
    'token' => 'Der Passwort Zur&uuml;cksetzungs-Token ist ung&uuml;ltig.',
    'sent' => 'Es wurde ein E-Mail Zur&uuml;cksetzungslink verschickt!',
    'reset' => 'Das Passwort wurde zur&uuml;ckgesetzt!',

];

return \FI\Support\TranslationOverride::override(__FILE__, $translations);
