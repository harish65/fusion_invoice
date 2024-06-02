<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Functions;

class Setup
{
    public $properties = [
        // This is the name of the module.
        'name'            => 'Functions',

        // The name of the author.
        'author_name'     => 'Jesse Terry',

        // The URL to the author.
        'author_url'      => 'https://www.fusioninvoice.com',

        // The viewfolder.viewname to the navigation menu file, if exists.
        // This file must have a .blade.php extension.
        'navigation_menu' => '',

        // The viewfolder.viewname to the system menu view file, if exists.
        // This file must have a .blade.php extension.
        'system_menu'     => ''
    ];
}