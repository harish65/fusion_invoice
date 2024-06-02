<?php

namespace Addons\Conversions\Support;

abstract class AbstractConverter
{
    protected $conversionId;

    abstract public function getClients();

    abstract public function getQuotes();

    abstract public function getQuoteItems();

    abstract public function getInvoices();

    abstract public function getInvoiceItems();

    abstract public function getPayments();

    abstract public function getInvoiceComparison();

    abstract public function getQuoteComparison();

    abstract public function getPaymentComparison();

    public function convert()
    {
        $this->writeRecords($this->getClients(), 'clients.csv');

        $this->writeRecords($this->getInvoices(), 'invoices.csv');

        $this->writeRecords($this->getInvoiceItems(), 'invoice_items.csv');

        $this->writeRecords($this->getQuotes(), 'quotes.csv');

        $this->writeRecords($this->getQuoteItems(), 'quote_items.csv');

        $this->writeRecords($this->getPayments(), 'payments.csv');
    }

    public function compare()
    {
        return [
            'invoices' => $this->getInvoiceComparison(),
            'quotes' => $this->getQuoteComparison()
        ];
    }

    private function writeRecords($records, $filename)
    {
        if (!is_dir(storage_path('conversions')))
        {
            mkdir(storage_path('conversions'));
        }

        $writer = fopen(storage_path('conversions/' . $filename), 'w');

        if (isset($records[0]))
        {
            fputcsv($writer, array_keys((array)$records[0]));
        }

        foreach ($records as $record)
        {
            $record = (array)$record;

            fputcsv($writer, $record);
        }

        fclose($writer);
    }
}