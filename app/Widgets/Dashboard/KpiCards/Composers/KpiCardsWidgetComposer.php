<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Widgets\Dashboard\KpiCards\Composers;

use FI\Modules\Invoices\Models\InvoiceAmount;
use FI\Modules\Payments\Models\PaymentInvoice;
use FI\Modules\Quotes\Models\QuoteAmount;
use FI\Support\CurrencyFormatter;
use Illuminate\Support\Facades\DB;

class KpiCardsWidgetComposer
{
    public function compose($view)
    {
        $view->with('invoicesTotalDraft', $this->getInvoicesTotalDraft())
             ->with('invoicesTotalSent', $this->getInvoicesTotalSentOrMailed())
             ->with('invoicesTotalPaid', $this->getInvoicesTotalPaid())
             ->with('invoicesTotalOverdue', $this->getInvoicesTotalOverdue())
             ->with('quotesTotalDraft', $this->getQuoteTotalDraft())
             ->with('quotesTotalSent', $this->getQuoteTotalSent())
             ->with('quotesTotalApproved', $this->getQuoteTotalApproved())
             ->with('quotesTotalRejected', $this->getQuoteTotalRejected())
             ->with('kpiCardsSettings', $this->kpiCardsSettings())
             ->with('invoiceDashboardTotalOptions', periods());
    }

    private function kpiCardsSettings()
    {
        $kpiCardsSettings = ['draft_invoices' => 'DraftInvoices', 'sent_invoices' => 'SentInvoices', 'overdue_invoices' => 'OverdueInvoices', 'payments_collected' => 'PaymentsCollectedInvoices', 'draft_quotes' => 'DraftQuotes', 'sent_quotes' => 'SentQuotes', 'rejected_quotes' => 'RejectedQuotes', 'approved_quotes' => 'ApprovedQuotes'];
        $kpiCardsDisplay  = true;
        foreach ($kpiCardsSettings as $kpiCardsSetting)
        {
            if (config('fi.dashboard' . $kpiCardsSetting) == 1)
            {
                $kpiCardsDisplay = true;
                return $kpiCardsDisplay;
            }
            else
            {
                $kpiCardsDisplay = false;
            }
        }
        return $kpiCardsDisplay;
    }


    private function getInvoicesTotalDraft()
    {
        return CurrencyFormatter::format(InvoiceAmount::join('invoices', 'invoices.id', '=', 'invoice_amounts.invoice_id')
                                                      ->whereHas('invoice', function ($q) {
                                                          $q->draft();
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
                                                      })->sum(DB::raw('total / exchange_rate')));
    }

    private function getInvoicesTotalSentOrMailed()
    {
        return CurrencyFormatter::format(InvoiceAmount::join('invoices', 'invoices.id', '=', 'invoice_amounts.invoice_id')
                                                      ->whereHas('invoice', function ($q) {
                                                          $q->sentOrMailed();
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
                                                      })->sum(DB::raw('total / exchange_rate')));
    }

    private function getInvoicesTotalPaid()
    {
        $payments = PaymentInvoice::join('invoices', 'invoices.id', '=', 'payment_invoices.invoice_id')->whereHas('payment', function ($q) {
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


        return CurrencyFormatter::format($payments->sum(DB::raw('invoice_amount_paid / exchange_rate')));
    }

    private function getInvoicesTotalOverdue()
    {

        return CurrencyFormatter::format(InvoiceAmount::join('invoices', 'invoices.id', '=', 'invoice_amounts.invoice_id')
                                                      ->whereHas('invoice', function ($q) {
                                                          $q->overdue();
                                                          switch (config('fi.dashboardWidgetsDateOption'))
                                                          {
                                                              case 'year_to_date':
                                                                  $q->thisYearOverdue();
                                                                  break;
                                                              case 'this_quarter':
                                                                  $q->thisQuarterOverdue();
                                                                  break;
                                                              case 'this_month':
                                                                  $q->thisMonthOverdue();
                                                                  break;
                                                              case 'last_year':
                                                                  $q->lastYearOverdue();
                                                                  break;
                                                              case 'last_quarter':
                                                                  $q->lastQuarterOverdue();
                                                                  break;
                                                              case 'last_month':
                                                                  $q->lastMonthOverdue();
                                                                  break;
                                                              case 'custom_date_range':
                                                                  $q->dateRangeOverdue(config('fi.dashboardWidgetsFromDate'), config('fi.dashboardWidgetsToDate'));
                                                                  break;
                                                              case 'today':
                                                                  $q->todayOverdue();
                                                                  break;
                                                              case 'yesterday':
                                                                  $q->yesterdayOverdue();
                                                                  break;
                                                              case 'last_7_days':
                                                                  $q->last7DaysOverdue();
                                                                  break;
                                                              case 'last_30_days':
                                                                  $q->last30DaysOverdue();
                                                                  break;
                                                              case 'first_quarter':
                                                                  $q->firstQuarterOverdue();
                                                                  break;
                                                              case 'second_quarter':
                                                                  $q->secondQuarterOverdue();
                                                                  break;
                                                              case 'third_quarter':
                                                                  $q->thirdQuarterOverdue();
                                                                  break;
                                                              case 'fourth_quarter':
                                                                  $q->fourthQuarterOverdue();
                                                                  break;
                                                          }
                                                      })->sum(DB::raw('balance / exchange_rate')));
    }

    private function getQuoteTotalDraft()
    {
        return CurrencyFormatter::format(QuoteAmount::join('quotes', 'quotes.id', '=', 'quote_amounts.quote_id')
                                                    ->whereHas('quote', function ($q) {
                                                        $q->draft();
                                                        $q->where('invoice_id', 0);
                                                        switch (config('fi.dashboardWidgetsDateOption'))
                                                        {
                                                            case 'year_to_date':
                                                                $q->thisYear();
                                                                break;
                                                            case 'this_month':
                                                                $q->thisMonth();
                                                                break;
                                                            case 'last_month':
                                                                $q->lastMonth();
                                                                break;
                                                            case 'this_quarter':
                                                                $q->thisQuarter();
                                                                break;
                                                            case 'last_year':
                                                                $q->lastYear();
                                                                break;
                                                            case 'last_quarter':
                                                                $q->lastQuarter();
                                                                break;
                                                            case 'custom_date_range':
                                                                $q->dateRange(config('fi.dashboardWidgetsFromDate'), config('fi.dashboardWidgetsToDate'));
                                                                break;
                                                        }
                                                    })->sum(DB::raw('total / exchange_rate')));
    }

    private function getQuoteTotalSent()
    {
        return CurrencyFormatter::format(QuoteAmount::join('quotes', 'quotes.id', '=', 'quote_amounts.quote_id')
                                                    ->whereHas('quote', function ($q) {
                                                        $q->sent();
                                                        $q->where('invoice_id', 0);
                                                        switch (config('fi.dashboardWidgetsDateOption'))
                                                        {
                                                            case 'year_to_date':
                                                                $q->thisYear();
                                                                break;
                                                            case 'this_month':
                                                                $q->thisMonth();
                                                                break;
                                                            case 'last_month':
                                                                $q->lastMonth();
                                                                break;
                                                            case 'this_quarter':
                                                                $q->thisQuarter();
                                                                break;
                                                            case 'last_year':
                                                                $q->lastYear();
                                                                break;
                                                            case 'last_quarter':
                                                                $q->lastQuarter();
                                                                break;
                                                            case 'custom_date_range':
                                                                $q->dateRange(config('fi.dashboardWidgetsFromDate'), config('fi.dashboardWidgetsToDate'));
                                                                break;
                                                        }
                                                    })->sum(DB::raw('total / exchange_rate')));
    }

    private function getQuoteTotalApproved()
    {
        return CurrencyFormatter::format(QuoteAmount::join('quotes', 'quotes.id', '=', 'quote_amounts.quote_id')
                                                    ->whereHas('quote', function ($q) {
                                                        $q->approved();
                                                        switch (config('fi.dashboardWidgetsDateOption'))
                                                        {
                                                            case 'year_to_date':
                                                                $q->thisYear();
                                                                break;
                                                            case 'this_month':
                                                                $q->thisMonth();
                                                                break;
                                                            case 'last_month':
                                                                $q->lastMonth();
                                                                break;
                                                            case 'this_quarter':
                                                                $q->thisQuarter();
                                                                break;
                                                            case 'last_year':
                                                                $q->lastYear();
                                                                break;
                                                            case 'last_quarter':
                                                                $q->lastQuarter();
                                                                break;
                                                            case 'custom_date_range':
                                                                $q->dateRange(config('fi.dashboardWidgetsFromDate'), config('fi.dashboardWidgetsToDate'));
                                                                break;
                                                        }
                                                    })->sum(DB::raw('total / exchange_rate')));
    }

    private function getQuoteTotalRejected()
    {
        return CurrencyFormatter::format(QuoteAmount::join('quotes', 'quotes.id', '=', 'quote_amounts.quote_id')
                                                    ->whereHas('quote', function ($q) {
                                                        $q->rejected();
                                                        $q->where('invoice_id', 0);
                                                        switch (config('fi.dashboardWidgetsDateOption'))
                                                        {
                                                            case 'year_to_date':
                                                                $q->thisYear();
                                                                break;
                                                            case 'this_month':
                                                                $q->thisMonth();
                                                                break;
                                                            case 'last_month':
                                                                $q->lastMonth();
                                                                break;
                                                            case 'last_year':
                                                                $q->lastYear();
                                                                break;
                                                            case 'last_quarter':
                                                                $q->lastQuarter();
                                                                break;
                                                            case 'this_quarter':
                                                                $q->thisQuarter();
                                                                break;
                                                            case 'custom_date_range':
                                                                $q->dateRange(config('fi.dashboardWidgetsFromDate'), config('fi.dashboardWidgetsToDate'));
                                                                break;
                                                        }
                                                    })->sum(DB::raw('total / exchange_rate')));
    }
}