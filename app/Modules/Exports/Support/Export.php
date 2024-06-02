<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Support;

use Sonata\Exporter\Handler;
use Sonata\Exporter\Source\ArraySourceIterator;

class Export
{
    protected $exportType;

    protected $fileName;

    protected $storagePath;

    protected $writerType;

    protected $filterFields;

    public function __construct($exportType, $writerType = 'CSV', $filterFields = [])
    {
        $this->exportType  = $exportType;
        $this->storagePath = storage_path('app');
        if ($writerType == 'CSV')
        {
            $this->writerType = 'CsvWriter';
        }
        elseif ($writerType == 'JSON')
        {
            $this->writerType = 'JsonWriter';
        }
        elseif ($writerType == 'XLS')
        {
            $this->writerType = 'XlsWriter';
        }
        elseif ($writerType == 'XML')
        {
            $this->writerType = 'XmlWriter';
        }
        else
        {
            $this->writerType = $writerType;
        }
        $this->filterFields = $filterFields;
    }

    public function writeFile()
    {
        $resultsClass = 'FI\Modules\Exports\Support\Results\\' . $this->exportType;
        $writerClass  = 'Sonata\Exporter\Writer\\' . $this->writerType;

        $fileExtension  = strtolower(str_replace('Writer', '', $this->writerType));
        $this->fileName = $this->exportType . 'Export.' . $fileExtension;

        if (file_exists($this->storagePath . '/' . $this->fileName))
        {
            unlink($this->storagePath . '/' . $this->fileName);
        }

        $rClass = new $resultsClass;
        $result = $records = $rClass->getResults();

        if (!empty($this->filterFields))
        {
            foreach ($records as $key => $record)
            {
                $filter       = $this->filterFields;
                $filtered     = array_filter(
                    $record,
                    function ($key) use ($filter)
                    {
                        return in_array($key, $filter);
                    },
                    ARRAY_FILTER_USE_KEY
                );
                $result[$key] = $filtered;
            }
        }
        $source = new ArraySourceIterator($result);

        $writer = new $writerClass($this->storagePath . '/' . $this->fileName);

        Handler::create($source, $writer)->export();
    }

    public function getDownloadPath()
    {
        return $this->storagePath . '/' . $this->fileName;
    }
}