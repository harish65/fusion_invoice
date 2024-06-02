<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Import\Importers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

abstract class AbstractImporter
{
    protected $messages;
    protected $max_records = 10000;
    protected $file;
    protected $errors = false;

    public function __construct()
    {
        $this->messages = new MessageBag;

        ini_set('auto_detect_line_endings', true);
    }

    /**
     * List the fields available for import.
     *
     * @return array
     */
    abstract public function getFields();

    /**
     * Returns validation rules for file mapping validation.
     *
     * @return array
     */
    abstract public function getMapRules();

    /**
     * Return an instance of the validator.
     *
     * @param array $input
     * @return Validator;
     */
    abstract public function getValidator($input);

    abstract public function importData($input);

    /**
     * Perform validation on file map.
     *
     * @param array $input
     * @return boolean
     */
    public function validateMap($input)
    {

        $validator = Validator::make($input, $this->getMapRules());

        /** @noinspection PhpUndefinedMethodInspection */
        if ($validator->fails())
        {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->messages->merge($validator->messages()->all());

            return false;
        }

        return true;
    }

    /**
     * Validate individual records being imported.
     *
     * @param $input
     * @param int $row
     * @return bool
     */
    public function validateRecord($input, $row = 0)
    {
        $validator = $this->getValidator($input);
        /** @noinspection PhpUndefinedMethodInspection */
        $customMessages = [];
        if ($validator->fails())
        {
            $allErrors = array_unique($validator->errors()->all());

            foreach ($allErrors as $message)
            {
                $customMessages[] = trans('fi.csv_row_number', ['row' => $row]) . ": " . $message;
            }
            $this->messages->merge($customMessages);

            return false;
        }

        return true;
    }

    /**
     * Read the column names from the import file.
     *
     * @param string $file
     * @return array
     */
    public function getFileFields($file)
    {
        $f = fopen($file, 'r');

        $line = fgets($f);

        fclose($f);

        $fields = array_map('strtolower', str_getcsv($line));

        return array_merge(['' => ''], $fields);
    }

    /**
     * Get the file handle.
     *
     * @param string $file
     * @return resource
     */
    public function getFileHandle($file)
    {
        return fopen($file, 'r');
    }

    public function errors()
    {
        return $this->messages;
    }

    public function validateTotalRecords()
    {
        $fp = file($this->file, FILE_SKIP_EMPTY_LINES);
        $rtnStr = "ok";

        if (count($fp) > (($this->max_records) + 1))
        {
            $rtnStr = trans('fi.max_import_records', ['total_records' => $this->max_records]);
        } 
        elseif (count($fp) < 2 )
        {
            $rtnStr = trans('fi.no_import_records');
        }
        return $rtnStr;
    }
}