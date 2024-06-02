<?php

$translations = [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'password' => 'Slaptažodis turi susidaryti mažiausiai iš 6 simbolių ir atitikti reikalavimus.',
    'reset' => 'Jusu slaptažodis buvo nustatytas iš naujo!',
    'sent' => 'Mes atsiunteme jusu slaptažodžio nustatymo nuoroda el. Paštu!',
    'token' => 'Slaptažodžio atstatymas nesėkmingas.',
    'user' => "Vartuotojas su šiuo el-pašto adresu neegzistuoja.",

];

return \FI\Support\TranslationOverride::override(__FILE__, $translations);
