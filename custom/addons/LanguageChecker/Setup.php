<?php

namespace Addons\LanguageChecker;

class Setup
{
    public $properties = [
        // This is the name of the module.
        'name'            => 'Language Checker',

        // The name of the author.
        'author_name'     => 'Jesse Terry',

        // The URL to the author.
        'author_url'      => 'https://www.fusioninvoice.com',

        // The viewfolder.viewname to the navigation menu file, if exists.
        // This file must have a .blade.php extension.
        'navigation_menu' => '',

        // The viewfolder.viewname to the navigation report file, if exists.
        // This file must have a .blade.php extension.
        'navigation_reports' => '',

        // The viewfolder.viewname to the system menu view file, if exists.
        // This file must have a .blade.php extension.
        'system_menu'        => 'languagechecker._system'
    ];

}