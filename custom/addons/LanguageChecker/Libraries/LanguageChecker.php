<?php

namespace Addons\LanguageChecker\Libraries;

use Symfony\Component\Finder\Finder;

class LanguageChecker
{
    private $invalidStrings = 0;

    public function getValidStrings($language)
    {
        foreach (require base_path('resources/lang/' . $language . '/fi.php') as $key => $string)
        {
            $strings[$key] = [
                'key'    => $key,
                'string' => $string,
                'count'  => 0,
            ];
        }

        $finder = new Finder();

        $finder->files()->in([base_path('app'), base_path('database/migrations')]);

        foreach ($finder as $file)
        {
            foreach (array_keys($strings) as $key)
            {
                $strings[$key]['count'] += substr_count(file_get_contents($file), $key);
            }
        }

        $strings = collect($strings);

        $this->invalidStrings = $strings->where('count', 0);

        return $strings->filter(function($item)
        {
            return $item['count'] > 0;
        });
    }

    public function getInvalidStrings()
    {
        return $this->invalidStrings;
    }
}