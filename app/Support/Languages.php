<?php 

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

class Languages
{
    /**
     * Provide a list of the available language translations.
     *
     * @return array
     */
    static function listLanguages()
    {
        $directories       = Directory::listContents(base_path('resources/lang'));
        $fullNameLanguages = [
            'cs'    => 'Czech',
            'da'    => 'Danish',
            'de'    => 'German',
            'en'    => 'English',
            'es'    => 'Spanish',
            'fr'    => 'French',
            'fr_ca' => 'French Canadian',
            'it'    => 'Italian',
            'lt'    => 'Lithuanian',
            'nl'    => 'Dutch',
            'sv'    => 'Swedish',
            'tr'    => 'Turkish',
        ];
        $languages         = [];

        foreach ($directories as $directory)
        {
            foreach ($fullNameLanguages as $key => $value)
            {

                if ($key == $directory)
                {
                    $languages[$directory] = ($value) ? strtoupper($directory) . ' - ' . ucfirst($value) : $directory;
                    break;
                }
                else
                {
                    $languages[$directory] = $directory;
                }
            }
        }

        return $languages;
    }
}