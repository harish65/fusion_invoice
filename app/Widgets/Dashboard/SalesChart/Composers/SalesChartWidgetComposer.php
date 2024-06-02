<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Widgets\Dashboard\SalesChart\Composers;

use Carbon\Carbon;
use FI\Modules\Invoices\Models\InvoiceAmount;
use FI\Modules\Payments\Models\PaymentInvoice;
use FI\Modules\Settings\Models\Setting;
use Illuminate\Support\Facades\DB;

class SalesChartWidgetComposer
{
    public function compose($view)
    {
        $view->with('chartDate', $this->prepareChartDate());
    }

    private function getInvoicesTotalSentOrMailed()
    {
        $chartInvoiceData             = '';
        $chartInvoiceDataSentOrMailed = InvoiceAmount::join('invoices', 'invoices.id', '=', 'invoice_amounts.invoice_id')
                                                     ->select('invoices.invoice_date', DB::raw('count(invoices.id) as count'), DB::raw('sum(invoice_amounts.total/invoices.exchange_rate) as data'), DB::raw('WEEK(invoices.invoice_date) as week'), DB::raw("DATE_FORMAT(invoices.invoice_date ,'%d') AS onlyDate"), DB::raw('QUARTER(invoices.invoice_date) AS quarter'))
                                                     ->whereHas('invoice', function ($q)
                                                     {
                                                         $q->statusIn(config('fi.widgetSalesChartSetting') == 1 ? ['draft', 'sent'] : ['sent']);
                                                         switch (config('fi.dashboardWidgetsDateOption'))
                                                         {
                                                             case 'year_to_date':
                                                                 $q->thisYear();
                                                                 break;
                                                             case 'this_quarter':
                                                                 $q->thisQuarter();
                                                                 break;
                                                             case 'this_month':
                                                                 $q->thisMonth();
                                                                 break;
                                                             case 'last_year':
                                                                 $q->lastYear();
                                                                 break;
                                                             case 'last_quarter':
                                                                 $q->lastQuarter();
                                                                 break;
                                                             case 'last_month':
                                                                 $q->lastMonth();
                                                                 break;
                                                             case 'custom_date_range':
                                                                 $q->dateRange(config('fi.dashboardWidgetsFromDate'), config('fi.dashboardWidgetsToDate'));
                                                                 break;
                                                             case 'today':
                                                                 $q->today();
                                                                 break;
                                                             case 'yesterday':
                                                                 $q->yesterday();
                                                                 break;
                                                             case 'last_7_days':
                                                                 $q->last7Days();
                                                                 break;
                                                             case 'last_30_days':
                                                                 $q->last30Days();
                                                                 break;
                                                             case 'first_quarter':
                                                                 $q->firstQuarter();
                                                                 break;
                                                             case 'second_quarter':
                                                                 $q->secondQuarter();
                                                                 break;
                                                             case 'third_quarter':
                                                                 $q->thirdQuarter();
                                                                 break;
                                                             case 'fourth_quarter':
                                                                 $q->fourthQuarter();
                                                                 break;

                                                         }
                                                     });

        switch (config('fi.dashboardWidgetsDateOption'))
        {
            case 'all_time':
                $chartInvoiceData = $chartInvoiceDataSentOrMailed;
                break;
            case in_array(config('fi.dashboardWidgetsDateOption'), ['today', 'yesterday', 'last_7_days', 'last_30_days', 'this_month', 'last_month']):
                $chartInvoiceData = $chartInvoiceDataSentOrMailed->groupBy('invoices.invoice_date')->get()->toArray();
                break;
            case in_array(config('fi.dashboardWidgetsDateOption'), ['last_year', 'year_to_date']):
                $chartInvoiceData = $chartInvoiceDataSentOrMailed->groupBy(DB::raw('MONTH(invoices.invoice_date)'))->get()->toArray();
                break;
            case in_array(config('fi.dashboardWidgetsDateOption'), ['first_quarter', 'second_quarter', 'last_quarter', 'third_quarter', 'fourth_quarter', 'this_quarter']):
                $chartInvoiceData = $chartInvoiceDataSentOrMailed->groupBy(DB::raw('CONCAT(YEAR(invoices.invoice_date)'), DB::raw('WEEK(invoices.invoice_date))'))->get()->toArray();
                break;
            case in_array(config('fi.dashboardWidgetsDateOption'), ['4', 'custom_date_range']):
                $days             = Carbon::createFromDate(config('fi.dashboardWidgetsFromDate'))->diffInDays(config('fi.dashboardWidgetsToDate'));
                $chartInvoiceData = $this->getInvoiceDataWithCustomDataRange($days, $chartInvoiceDataSentOrMailed);
                break;
        }
        return $chartInvoiceData;
    }

    private function getPaidInvoiceWithTotal()
    {
        $chartPaymentData                   = '';
        $chartPaymentDataSingleOrPrePayment = PaymentInvoice::join('invoices', 'invoices.id', '=', 'payment_invoices.invoice_id')
                                                            ->join('payments', 'payments.id', '=', 'payment_invoices.payment_id')
                                                            ->select('invoices.invoice_date', 'payments.paid_at', DB::raw('count(invoices.id) as count'), DB::raw('sum(payment_invoices.invoice_amount_paid / invoices.exchange_rate) as data'), DB::raw('WEEK(invoices.invoice_date) as week'), DB::raw("DATE_FORMAT(invoices.invoice_date ,'%d') AS onlyDate"), DB::raw('QUARTER(invoices.invoice_date) AS quarter'))
                                                            ->whereHas('payment', function ($q)
                                                            {
                                                                $q->whereIn('type', ['single', 'pre-payment']);
                                                                switch (config('fi.dashboardWidgetsDateOption'))
                                                                {
                                                                    case 'year_to_date':
                                                                        $q->thisYear();
                                                                        break;
                                                                    case 'this_quarter':
                                                                        $q->thisQuarter();
                                                                        break;
                                                                    case 'this_month':
                                                                        $q->thisMonth();
                                                                        break;
                                                                    case 'last_year':
                                                                        $q->lastYear();
                                                                        break;
                                                                    case 'last_quarter':
                                                                        $q->lastQuarter();
                                                                        break;
                                                                    case 'last_month':
                                                                        $q->lastMonth();
                                                                        break;
                                                                    case 'custom_date_range':
                                                                        $q->dateRange(config('fi.dashboardWidgetsFromDate'), config('fi.dashboardWidgetsToDate'));
                                                                        break;
                                                                    case 'today':
                                                                        $q->today();
                                                                        break;
                                                                    case 'yesterday':
                                                                        $q->yesterday();
                                                                        break;
                                                                    case 'last_7_days':
                                                                        $q->last7Days();
                                                                        break;
                                                                    case 'last_30_days':
                                                                        $q->last30Days();
                                                                        break;
                                                                    case 'first_quarter':
                                                                        $q->firstQuarter();
                                                                        break;
                                                                    case 'second_quarter':
                                                                        $q->secondQuarter();
                                                                        break;
                                                                    case 'third_quarter':
                                                                        $q->thirdQuarter();
                                                                        break;
                                                                    case 'fourth_quarter':
                                                                        $q->fourthQuarter();
                                                                        break;
                                                                }
                                                            });

        switch (config('fi.dashboardWidgetsDateOption'))
        {
            case 'all_time':
                $chartPaymentData = $chartPaymentDataSingleOrPrePayment;
                break;
            case in_array(config('fi.dashboardWidgetsDateOption'), ['today', 'yesterday', 'last_7_days', 'last_30_days', 'this_month', 'last_month']):
                $chartPaymentData = $chartPaymentDataSingleOrPrePayment->groupBy('payments.paid_at')->get()->toArray();
                break;
            case in_array(config('fi.dashboardWidgetsDateOption'), ['2', 'last_year', 'year_to_date']):
                $chartPaymentData = $chartPaymentDataSingleOrPrePayment->groupBy(DB::raw('MONTH(payments.paid_at)'))->get()->toArray();
                break;
            case in_array(config('fi.dashboardWidgetsDateOption'), ['this_quarter', 'last_quarter', 'first_quarter', 'third_quarter', 'second_quarter', 'fourth_quarter']):
                $chartPaymentData = $chartPaymentDataSingleOrPrePayment->groupBy(DB::raw('CONCAT(YEAR(payments.paid_at)'), DB::raw('WEEK(payments.paid_at))'))->get()->toArray();
                break;
            case in_array(config('fi.dashboardWidgetsDateOption'), ['4', 'custom_date_range']):
                $days             = Carbon::createFromDate(config('fi.dashboardWidgetsFromDate'))->diffInDays(config('fi.dashboardWidgetsToDate'));
                $chartPaymentData = $this->getPaymentDataWithCustomDateRange($days, $chartPaymentDataSingleOrPrePayment);
                break;
        }
        return $chartPaymentData;
    }

    private function prepareChartDate()
    {

        $chartInvoiceData = $this->getInvoicesTotalSentOrMailed();
        $chartPaymentData = $this->getPaidInvoiceWithTotal();

        if (config('fi.dashboardWidgetsDateOption') == 'all_time')
        {
            $invoiceData = $chartInvoiceData->orderBy('invoices.invoice_date')->groupBy('invoices.invoice_date')->get()->toArray();
            $paymentData = $chartPaymentData->orderBy('payments.paid_at')->groupBy('payments.paid_at')->get()->toArray();

            $paymentFirstData = isset($paymentData[0]['paid_at']) ? $paymentData[0]['paid_at'] : null;
            $paymentEndData   = isset($paymentData[0]['paid_at']) ? end($paymentData)['paid_at'] : null;
            $dayDiff          = $invoiceAndPaymentMinDate = $invoiceAndPaymentMaxDate = 0;

            if ($invoiceData)
            {
                $invoiceAndPaymentMinDate = Carbon::createFromDate($invoiceData[0]['invoice_date'])->minimum($paymentFirstData)->format('Y-m-d');
                if (isset(end($invoiceData)['invoice_date']) && isset($paymentEndData))
                {
                    $invoiceAndPaymentMinDate = Carbon::createFromDate($invoiceData[0]['invoice_date'])->minimum($paymentFirstData)->format('Y-m-d');
                }
                if (isset(end($invoiceData)['invoice_date']) && !isset($paymentEndData))
                {
                    $invoiceAndPaymentMaxDate = end($invoiceData)['invoice_date'];
                }
                if (isset($paymentEndData) && !isset(end($invoiceData)['invoice_date']))
                {
                    $invoiceAndPaymentMaxDate = $paymentEndData;
                }
                if (isset($paymentEndData) && isset(end($invoiceData)['invoice_date']))
                {
                    $invoiceAndPaymentMaxDate = Carbon::createFromDate($invoiceData[0]['invoice_date'])->maximum($paymentEndData)->format('Y-m-d');
                }
                $dayDiff = Carbon::createFromDate($invoiceAndPaymentMinDate)->diffInDays($invoiceAndPaymentMaxDate);
            }

            if (isset($paymentData))
            {
                $chartPaymentData = $this->getPaymentDataWithCustomDateRange($dayDiff, $this->getPaidInvoiceWithTotal());
            }

            if ($invoiceData)
            {
                $chartInvoiceData = $this->getInvoiceDataWithCustomDataRange($dayDiff, $this->getInvoicesTotalSentOrMailed());
                Setting::saveByKey('dashboardWidgetsToDate', $invoiceAndPaymentMaxDate);
                Setting::saveByKey('dashboardWidgetsFromDate', $invoiceAndPaymentMinDate);
                Setting::saveByKey('dashboardWidgetsDateOption', 'custom_date_range');
            }
        }

        $daysAndMode = $this->getDays();

        return salesGraphData($daysAndMode, $chartPaymentData, $chartInvoiceData);
    }

    private function getDays()
    {
        $days       = 0;
        $customMode = '';

        switch (config('fi.dashboardWidgetsDateOption'))
        {
            case in_array(config('fi.dashboardWidgetsDateOption'), ['year_to_date', 'last_year']):
                $days = 365;
                break;
            case  in_array(config('fi.dashboardWidgetsDateOption'), ['this_quarter', 'last_quarter', 'first_quarter', 'third_quarter', 'second_quarter', 'fourth_quarter']):
                $days = 93;
                break;
            case in_array(config('fi.dashboardWidgetsDateOption'), ['this_month', 'last_month', 'last_30_days']):
                $days = 31;
                break;
            case 'custom_date_range':
                $days = Carbon::createFromDate(config('fi.dashboardWidgetsFromDate'))->diffInDays(config('fi.dashboardWidgetsToDate'));
                if ($days <= 31)
                {
                    $customMode = 'days';
                }
                elseif ($days <= 95 && $days > 31)
                {
                    $customMode = 'week';
                }
                elseif ($days <= 730 && $days > 95)
                {
                    $customMode = 'month';
                }
                elseif ($days > 730)
                {
                    $customMode = 'quarter';
                }

                break;
            case in_array(config('fi.dashboardWidgetsDateOption'), ['today', 'yesterday']):
                $days = 1;
                break;
            case 'last_7_days':
                $days = 7;
                break;
        }
        return ['days' => $days, 'customMode' => $customMode];
    }

    private function getPaymentDataWithCustomDateRange($days, $chartPaymentDataSingleOrPrePayment)
    {
        $chartPaymentData = '';

        if ($days <= 31)
        {
            $chartPaymentData = $chartPaymentDataSingleOrPrePayment->groupBy('payments.paid_at')->get()->toArray();
        }
        elseif ($days <= 95 && $days > 31)
        {
            $chartPaymentData = $chartPaymentDataSingleOrPrePayment->groupBy(DB::raw('CONCAT(YEAR(payments.paid_at)'), DB::raw('WEEK(payments.paid_at))'))->get()->toArray();
        }
        elseif ($days <= 730 && $days > 95)
        {
            $chartPaymentData = $chartPaymentDataSingleOrPrePayment->groupBy(DB::raw('MONTH(payments.paid_at)'))->get()->toArray();
        }
        elseif ($days > 730)
        {
            $chartPaymentData = $chartPaymentDataSingleOrPrePayment->groupBy(DB::raw('QUARTER(payments.paid_at)'))->get()->toArray();
        }
        return $chartPaymentData;
    }

    private function getInvoiceDataWithCustomDataRange($days, $chartInvoiceDataSentOrMailed)
    {
        $customInvoiceData = '';

        if ($days <= 31)
        {
            $customInvoiceData = $chartInvoiceDataSentOrMailed->groupBy('invoices.invoice_date')->get()->toArray();
        }
        elseif ($days <= 95 && $days > 31)
        {
            $customInvoiceData = $chartInvoiceDataSentOrMailed->groupBy(DB::raw('CONCAT(YEAR(invoices.invoice_date)'), DB::raw('WEEK(invoices.invoice_date))'))->get()->toArray();
        }
        elseif ($days <= 730 && $days > 95)
        {
            $customInvoiceData = $chartInvoiceDataSentOrMailed->groupBy(DB::raw('MONTH(invoices.invoice_date)'))->get()->toArray();
        }
        elseif ($days > 730)
        {
            $customInvoiceData = $chartInvoiceDataSentOrMailed->groupBy(DB::raw('QUARTER(invoices.invoice_date)'))->get()->toArray();
        }

        return $customInvoiceData;
    }
}
