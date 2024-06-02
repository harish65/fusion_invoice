<?php

namespace Addons\PendingClients;

class Setup
{
    public $properties = [
        // This is the name of the module.
        'name'            => 'Pending Clients',

        // The name of the author.
        'author_name'     => 'Wesley Reynolds',

        // The URL to the author.
        'author_url'      => '',

        // The viewfolder.viewname to the navigation menu file, if exists.
        // This file must have a .blade.php extension.
        'navigation_menu' => 'pending_clients._navigation',

        // The viewfolder.viewname to the navigation report file, if exists.
        // This file must have a .blade.php extension.
        'navigation_reports' => '',

        // The viewfolder.viewname to the system menu view file, if exists.
        // This file must have a .blade.php extension.
        'system_menu'        => ''
    ];

}