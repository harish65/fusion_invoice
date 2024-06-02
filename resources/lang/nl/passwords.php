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

    'password' => 'Wachtwoorden moeten minimaal zes karakters bevatten en overeenkomen met het bevestigings wachtwoord.',
	'reset' => 'Uw wachtwoord werd heringesteld!',
	'sent' => 'De link voor uw wachtwoord herinstelling werd ge-emailed!',
	'token' => 'Het token voor uw wachtwoord herinstelling is ongeldig.',
    'user' => "We kunnen geen gebruiker vinden dat e-mailadres.",
    
];

return \FI\Support\TranslationOverride::override(__FILE__, $translations);
