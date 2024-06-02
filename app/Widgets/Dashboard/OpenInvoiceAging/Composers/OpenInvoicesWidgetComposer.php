<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Widgets\Dashboard\OpenInvoiceAging\Composers;

use Carbon\Carbon;
use FI\Modules\Invoices\Models\Invoice;
use FI\Support\CurrencyFormatter;
use Illuminate\Support\Facades\DB;

class OpenInvoicesWidgetComposer
{
    public function compose($view)
    {
        $view->with('openInvoiceAging', $this->getOpenInvoiceAging());
    }

    private function getOpenInvoiceAging()
    {
        try
        {
            $openInvoiceAgingData = $this->getOpenInvoiceAgingData();
            $bgColor = ['current'          => 'open-ar-current',
                        'oneToThirty'      => 'open-ar-30',
                        'thirtyOneToSixty' => 'open-ar-60',
                        'sixtyOneToNinety' => 'open-ar-90',
                        'ninetyOnePlus'    => 'open-ar-91 ',
                        'total'            => 'open-ar-tot'];
            foreach ($openInvoiceAgingData as $key => $value)
            {
                $openInvoiceAgingData[$key] = [
                    'bg-color' => $bgColor[$key],
                    'data'     => CurrencyFormatter::format($value)
                ];
            }
            return ['success' => true, 'openInvoiceAging' => $openInvoiceAgingData];
        }
        catch (\Exception $e)
        {
            return ['success' => false, 'message' => trans('fi.open_invoice_aging_data_fetch_error')];
        }
    }


    private function getOpenInvoiceAgingData()
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->format('Y-m-d'));
        $status      = (config('fi.widgetOpenInvoiceAging') != null) ? config('fi.widgetOpenInvoiceAging') : "'sent'";

        $invoice = Invoice::select(
            DB::raw("(SELECT IFNULL(SUM(invoice_amounts.balance),0) FROM invoices as current_invoice
                    inner join invoice_amounts on invoice_amounts.invoice_id = current_invoice.id
                    WHERE date(current_invoice.due_at) >= date('" . $currentDate . "')
                    AND current_invoice.status in (" . $status . ") AND current_invoice.type = 'invoice')
                     as current"),
            DB::raw("(SELECT IFNULL(SUM(invoice_amounts.balance),0) FROM invoices as current_invoice 
                    inner join invoice_amounts on invoice_amounts.invoice_id = current_invoice.id
                    WHERE ( date(current_invoice.due_at) BETWEEN  date('" . Carbon::createFromDate($currentDate)->subDay(30)->format('Y-m-d') . "') AND date('" . Carbon::createFromDate($currentDate)->subDay(1)->format('Y-m-d') . "'))
                    AND current_invoice.status in (" . $status . ") AND current_invoice.type = 'invoice' )
                    as oneToThirty"),
            DB::raw("(SELECT IFNULL(SUM(invoice_amounts.balance),0) FROM invoices as current_invoice
                    inner join invoice_amounts on invoice_amounts.invoice_id = current_invoice.id
                    WHERE ( date(current_invoice.due_at) BETWEEN  date('" . Carbon::createFromDate($currentDate)->subDay(60)->format('Y-m-d') . "') AND date('" . Carbon::createFromDate($currentDate)->subDay(31)->format('Y-m-d') . "'))
                    AND current_invoice.status in (" . $status . ") AND current_invoice.type = 'invoice' )
                    as thirtyOneToSixty"),
            DB::raw("(SELECT IFNULL(SUM(invoice_amounts.balance),0) FROM invoices as current_invoice
                    inner join invoice_amounts on invoice_amounts.invoice_id = current_invoice.id
                    WHERE  ( date(current_invoice.due_at) BETWEEN  date('" . Carbon::createFromDate($currentDate)->subDay(90)->format('Y-m-d') . "') AND date('" . Carbon::createFromDate($currentDate)->subDay(61)->format('Y-m-d') . "'))
                    AND current_invoice.status in (" . $status . ") AND current_invoice.type = 'invoice')
                    as sixtyOneToNinety"),
            DB::raw("(SELECT IFNULL(SUM(invoice_amounts.balance),0) FROM invoices as current_invoice
                    inner join invoice_amounts on invoice_amounts.invoice_id = current_invoice.id
                    WHERE date(current_invoice.due_at) <= date('" . Carbon::createFromDate($currentDate)->subDay(91)->format('Y-m-d') . "')
                    AND current_invoice.status in (" . $status . ") AND current_invoice.type = 'invoice')
                     as ninetyOnePlus"),
            DB::raw("(SELECT IFNULL(SUM(invoice_amounts.balance),0) FROM invoices as current_invoice
                    inner join invoice_amounts on invoice_amounts.invoice_id = current_invoice.id
                    WHERE current_invoice.status in (" . $status . ") AND current_invoice.type = 'invoice')
                     as total")
        )->whereType('invoice')->groupBy('invoices.type')->first();

        $invoiceDefault = ['current' => 0, 'oneToThirty' => 0, 'thirtyOneToSixty' => 0, 'sixtyOneToNinety' => 0, 'ninetyOnePlus' => 0, 'total' => 0];
        return ($invoice != null) ? $invoice->toArray() : $invoiceDefault;
    }
}