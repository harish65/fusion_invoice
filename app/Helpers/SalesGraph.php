<?php

use Carbon\Carbon;

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function salesGraphData($daysAndMode, $chartPaymentData, $chartInvoiceData)
{
    $preparedInvoiceData = $preparedPaymentData = $labels = [];
    $days                = $daysAndMode['days'];
    $customMode          = $daysAndMode['customMode'];
    $dateFormat          = config('fi.dateFormat');
    $currentDate         = Carbon::now()->format('Y-m-d');

    if ($days == 31 or $days == 7 or $days == 1 or ((config('fi.dashboardWidgetsDateOption') == 'custom_date_range') && isset($customMode) && $customMode == 'days'))
    {
        if (in_array(config('fi.dashboardWidgetsDateOption'), ['today', 'yesterday', 'this_month', 'last_month', 'last_30_days', 'custom_date_range', 'last_7_days']))
        {
            if (config('fi.dashboardWidgetsDateOption') == 'today')
            {
                $monthStartDate = Carbon::now()->format('Y-m-d');
                $days           = 1;
            }
            if (config('fi.dashboardWidgetsDateOption') == 'yesterday')
            {
                $monthStartDate = Carbon::now()->subDays(1)->format('Y-m-d');
                $days           = 1;

            }
            if (config('fi.dashboardWidgetsDateOption') == 'custom_date_range')
            {
                $days           = Carbon::createFromDate(config('fi.dashboardWidgetsFromDate'))->subDays(1)->diffInDays(config('fi.dashboardWidgetsToDate'));
                $monthStartDate = Carbon::createFromDate(config('fi.dashboardWidgetsFromDate'))->format('Y-m-d');
                $endCustom      = Carbon::createFromDate(config('fi.dashboardWidgetsToDate'))->format('Y-m-d');
            }
            if (config('fi.dashboardWidgetsDateOption') == 'last_7_days')
            {
                $monthStartDate = Carbon::now()->subDays(6)->format('Y-m-d');
                $days           = 7;
            }
            if (config('fi.dashboardWidgetsDateOption') == 'last_30_days')
            {
                $monthStartDate = Carbon::now()->subDays(29)->format('Y-m-d');
                $days           = 30;
            }
            if (config('fi.dashboardWidgetsDateOption') == 'last_month')
            {
                $monthStartDate = Carbon::createFromDate($currentDate)->subMonth()->firstOfMonth()->format('Y-m-d');
                $days           = Carbon::createFromDate($monthStartDate)->daysInMonth;
            }
            if (config('fi.dashboardWidgetsDateOption') == 'this_month')
            {
                $monthStartDate = Carbon::createFromDate($currentDate)->firstOfMonth()->format('Y-m-d');
                $days           = Carbon::createFromDate($currentDate)->daysInMonth;
            }

            $invoiceMonth = $payAtMonth = [];

            for ($i = 0; $i < $days; $i++)
            {
                $invoiceMonth[$i] = Carbon::createFromDate($monthStartDate)->addDays($i)->format('Y-m-d');
                $payAtMonth[$i]   = Carbon::createFromDate($monthStartDate)->addDays($i)->format('Y-m-d');
                $labels[$i]       = Carbon::createFromDate($monthStartDate)->addDays($i)->format($dateFormat);
            }

            $preparedInvoiceData = [];
            foreach ($invoiceMonth as $value)
            {
                if (isset($chartInvoiceData[0]['invoice_date']))
                {

                    foreach ($chartInvoiceData as $chartDataValue)
                    {
                        if (intval($chartDataValue['invoice_date']) == intval($value))
                        {
                            $preparedInvoiceData[$chartDataValue['invoice_date']] = intval($chartDataValue['data']);
                        }
                        if (!isset($preparedInvoiceData[$value]))
                        {
                            $preparedInvoiceData[$value] = 0;
                        }
                    }
                }
                else
                {
                    $preparedInvoiceData[$value] = 0;
                }
            }
            ksort($preparedInvoiceData);
        }

        $preparedPaymentData = [];

        foreach ($payAtMonth as $value)
        {
            if (isset($chartPaymentData[0]['paid_at']))
            {
                foreach ($chartPaymentData as $chartDataValue)
                {
                    if (intval($chartDataValue['paid_at']) == intval($value))
                    {
                        $preparedPaymentData[$chartDataValue['paid_at']] = intval($chartDataValue['data']);
                    }
                    if (!isset($preparedPaymentData[$value]))
                    {
                        $preparedPaymentData[$value] = 0;
                    }
                }
            }
            else
            {
                $preparedPaymentData[$value] = 0;

            }
        }
        ksort($preparedPaymentData);
    }

    if ($days == 365 or ((config('fi.dashboardWidgetsDateOption') == 'custom_date_range') && isset($customMode) && $customMode == 'month'))
    {
        if (in_array(config('fi.dashboardWidgetsDateOption'), ['custom_date_range', 'year_to_date', 'last_year']))
        {
            if (config('fi.dashboardWidgetsDateOption') == 'custom_date_range')
            {
                $startCustom             = Carbon::createFromDate(config('fi.dashboardWidgetsFromDate'))->format('Y-m-d');
                $endCustom               = Carbon::createFromDate(config('fi.dashboardWidgetsToDate'))->format('Y-m-d');
                $thisYearFirstMonthOfDay = Carbon::createFromDate($startCustom)->startOfMonth()->format('Y-m-d');
                $thisYearEndMonthOfDay   = Carbon::createFromDate($endCustom)->endOfMonth()->format('Y-m-d');
                $days                    = Carbon::createFromDate($thisYearFirstMonthOfDay)->diffInMonths($thisYearEndMonthOfDay);
            }
            if (config('fi.dashboardWidgetsDateOption') == 'last_year')
            {
                $currentDate             = Carbon::now()->subYear(1)->format('Y-m-d');
                $thisYearFirstMonthOfDay = Carbon::createFromDate($currentDate)->firstOfYear()->format('Y-m-d');
                $thisYearEndMonthOfDay   = Carbon::createFromDate($currentDate)->endOfYear()->format('m');
                $days                    = intval($thisYearEndMonthOfDay);
            }
            if (config('fi.dashboardWidgetsDateOption') == 'year_to_date')
            {
                $thisYearFirstMonthOfDay = Carbon::createFromDate($currentDate)->firstOfYear()->format('Y-m-d');
                $thisYearEndMonthOfDay   = Carbon::createFromDate($currentDate)->endOfYear()->format('m');
                $days                    = intval($thisYearEndMonthOfDay);
            }

            $invoiceThisYear = $payAtThisYear = [];

            for ($i = 0; $i < $days; $i++)
            {
                $invoiceThisYear[$i] = (Carbon::createFromDate($thisYearFirstMonthOfDay)->addMonth($i)->format('Y-m-d'));
                $payAtThisYear[$i]   = (Carbon::createFromDate($thisYearFirstMonthOfDay)->addMonth($i)->format('Y-m-d'));
                $labels[$i]          = Carbon::createFromDate(Carbon::createFromDate($thisYearFirstMonthOfDay)->addMonth($i))->endOfMonth()->format($dateFormat);
            }

            $preparedInvoiceData = [];

            foreach ($invoiceThisYear as $value)
            {
                if (isset($chartInvoiceData[0]['invoice_date']))
                {

                    foreach ($chartInvoiceData as $chartDataValue)
                    {
                        if (intval(Carbon::createFromDate($chartDataValue['invoice_date'])->format('m')) == intval(Carbon::createFromDate($value)->format('m')))
                        {
                            $preparedInvoiceData[$value] = intval($chartDataValue['data']);
                        }
                        if (!isset($preparedInvoiceData[$value]))
                        {
                            $preparedInvoiceData[$value] = 0;
                        }
                    }
                }
                else
                {
                    $preparedInvoiceData[$value] = 0;
                }
                ksort($preparedInvoiceData);
            }

            $preparedPaymentData = [];

            foreach ($payAtThisYear as $value)
            {
                if (isset($chartPaymentData[0]['paid_at']))
                {

                    foreach ($chartPaymentData as $chartDataValue)
                    {
                        if (intval(Carbon::createFromDate($chartDataValue['paid_at'])->format('m')) == intval(Carbon::createFromDate($value)->format('m')))
                        {
                            $preparedPaymentData[$value] = intval($chartDataValue['data']);
                        }
                        if (!isset($preparedPaymentData[$value]))
                        {
                            $preparedPaymentData[$value] = 0;
                        }
                    }
                }
                else
                {
                    $preparedPaymentData[$value] = 0;
                }
                ksort($preparedPaymentData);
            }
        }
    }

    if ($days == 93 or ((config('fi.dashboardWidgetsDateOption') == 'custom_date_range') && isset($customMode) && $customMode == 'week'))
    {
        if (in_array(config('fi.dashboardWidgetsDateOption'), ['custom_date_range', 'this_quarter', 'fourth_quarter', 'last_quarter', 'first_quarter', 'third_quarter', 'second_quarter']))
        {
            $currentDateQuarter = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));

            if (config('fi.dashboardWidgetsDateOption') == 'custom_date_range')
            {
                $firstOfQuarter     = Carbon::createFromDate(config('fi.dashboardWidgetsFromDate'))->startOfweek()->format('Y-m-d');
                $endOfQuarter       = Carbon::createFromDate(config('fi.dashboardWidgetsToDate'))->endOfWeek()->format('Y-m-d');
                $endOfQuarterAddDay = Carbon::createFromDate($endOfQuarter)->addDay()->format('Y-m-d');
            }
            if (config('fi.dashboardWidgetsDateOption') == 'first_quarter')
            {
                $firstOfQuarter = Carbon::createFromDate($currentDateQuarter)->addQuarter(0)->startOf('quarter')->format('Y-m-d');
                $endOfQuarter   = Carbon::createFromDate($currentDateQuarter)->addQuarter(0)->endOf('quarter')->format('Y-m-d');
            }
            if (config('fi.dashboardWidgetsDateOption') == 'second_quarter')
            {
                $firstOfQuarter = Carbon::createFromDate($currentDateQuarter)->addQuarter(1)->startOf('quarter')->format('Y-m-d');
                $endOfQuarter   = Carbon::createFromDate($currentDateQuarter)->addQuarter(1)->endOf('quarter')->format('Y-m-d');
            }
            if (config('fi.dashboardWidgetsDateOption') == 'third_quarter')
            {
                $firstOfQuarter = Carbon::createFromDate($currentDateQuarter)->addQuarter(2)->startOf('quarter')->format('Y-m-d');
                $endOfQuarter   = Carbon::createFromDate($currentDateQuarter)->addQuarter(2)->endOf('quarter')->format('Y-m-d');
            }
            if (config('fi.dashboardWidgetsDateOption') == 'last_quarter')
            {
                $firstOfQuarter = Carbon::now()->subQuarters(1)->firstOfQuarter()->format('Y-m-d');
                $endOfQuarter   = Carbon::now()->subQuarters(1)->lastOfQuarter()->format('Y-m-d');
            }
            if (config('fi.dashboardWidgetsDateOption') == 'fourth_quarter')
            {
                $firstOfQuarter = Carbon::createFromDate($currentDateQuarter)->addQuarter(3)->startOf('quarter')->format('Y-m-d');
                $endOfQuarter   = Carbon::createFromDate($currentDateQuarter)->addQuarter(3)->endOf('quarter')->format('Y-m-d');
            }
            if (config('fi.dashboardWidgetsDateOption') == 'this_quarter')
            {
                $firstOfQuarter = Carbon::now()->firstOfQuarter()->format('Y-m-d');
                $endOfQuarter   = Carbon::now()->endOfQuarter()->format('Y-m-d');
            }

            $days           = Carbon::createFromDate($firstOfQuarter)->diffInWeeks($endOfQuarter);
            $invoiceQuarter = $payAtQuarter = [];

            for ($i = 0; $i <= $days; $i++)
            {
                if ($i == 0)
                {
                    $invoiceQuarter[$i] = (Carbon::createFromDate($firstOfQuarter)->addWeek($i)->format('Y-m-d'));
                    $payAtQuarter[$i]   = (Carbon::createFromDate($firstOfQuarter)->addWeek($i)->format('Y-m-d'));
                }
                else
                {
                    $invoiceQuarter[$i] = (Carbon::createFromDate($firstOfQuarter)->addWeek($i)->subDay()->format('Y-m-d'));
                    $payAtQuarter[$i]   = (Carbon::createFromDate($firstOfQuarter)->addWeek($i)->subDay()->format('Y-m-d'));
                }
            }
            if (config('fi.dashboardWidgetsDateOption') == 'custom_date_range')
            {
                array_push($invoiceQuarter, $endOfQuarterAddDay);
                array_push($payAtQuarter, $endOfQuarterAddDay);
            }
            else
            {
                array_push($invoiceQuarter, $endOfQuarter);
                array_push($payAtQuarter, $endOfQuarter);
            }
            for ($i = 0; $i <= $days; $i++)
            {
                if ($i == $days)
                {
                    $labels[$i] = Carbon::createFromDate($invoiceQuarter[$i])->format($dateFormat) . ' to ' . Carbon::createFromDate($invoiceQuarter[$i + 1])->format($dateFormat);
                }
                else
                {
                    $labels[$i] = Carbon::createFromDate($invoiceQuarter[$i])->format($dateFormat) . ' to ' . Carbon::createFromDate($invoiceQuarter[$i + 1])->subDay(1)->format($dateFormat);
                }
            }

            $weekAndQuarterData  = weekAndQuarter($invoiceQuarter, $chartInvoiceData, $payAtQuarter, $chartPaymentData);
            $preparedInvoiceData = $weekAndQuarterData['newInvoiceArray'];
            $preparedPaymentData = $weekAndQuarterData['newPaymentArray'];
        }
    }

    if (config('fi.dashboardWidgetsDateOption') == 'custom_date_range')
    {
        $startCustom = Carbon::createFromDate(config('fi.dashboardWidgetsFromDate'))->firstOfQuarter()->format('Y-m-d');
        $endCustom   = Carbon::createFromDate(config('fi.dashboardWidgetsToDate'))->endOfQuarter()->format('Y-m-d');
        if (isset($customMode) && $customMode == 'quarter')
        {
            $days           = Carbon::createFromDate($startCustom)->diffInQuarters($endCustom);
            $invoiceQuarter = $payAtQuarter = $labels = [];

            for ($i = 0; $i <= $days; $i++)
            {
                $invoiceQuarter[$i] = (Carbon::createFromDate($startCustom)->addQuarter($i)->format('Y-m-d'));
                $payAtQuarter[$i]   = (Carbon::createFromDate($startCustom)->addQuarter($i)->format('Y-m-d'));
                $labels[$i]         = (Carbon::createFromDate($startCustom)->addQuarter($i)->format($dateFormat) . ' To ' . Carbon::createFromDate($startCustom)->addQuarter($i)->endOfQuarter()->format($dateFormat));
            }

            array_push($invoiceQuarter, $endCustom);
            array_push($payAtQuarter, $endCustom);

            $weekAndQuarterData  = weekAndQuarter($invoiceQuarter, $chartInvoiceData, $payAtQuarter, $chartPaymentData);
            $preparedInvoiceData = $weekAndQuarterData['newInvoiceArray'];
            $preparedPaymentData = $weekAndQuarterData['newPaymentArray'];
        }
    }

    $chartDataInvoiceAccumulateData = $chartDataPaymentAccumulateData = $chartDataInvoiceArray = $chartDataPaymentArray = [];

    foreach ($preparedInvoiceData as $k => $value)
    {
        $chartDataInvoiceArray[] = $value;
    }
    foreach ($preparedPaymentData as $value)
    {
        $chartDataPaymentArray[] = $value;
    }
    if (config('fi.accumulateTotals') == 1)
    {
        foreach ($chartDataInvoiceArray as $key => $value)
        {
            if ($key == 0)
            {
                $chartDataInvoiceAccumulateData[] = $value;
            }
            else
                $chartDataInvoiceAccumulateData[] = $chartDataInvoiceAccumulateData[$key - 1] + $value;
        }
        foreach ($chartDataPaymentArray as $key => $value)
        {
            if ($key == 0)
            {
                $chartDataPaymentAccumulateData[] = $value;
            }
            else
                $chartDataPaymentAccumulateData[] = $chartDataPaymentAccumulateData[$key - 1] + $value;
        }
        $chartDataInvoiceArray = $chartDataInvoiceAccumulateData;
        $chartDataPaymentArray = $chartDataPaymentAccumulateData;
    }

    return ['chartDataInvoiceArray' => $chartDataInvoiceArray, 'chartDataPaymentArray' => $chartDataPaymentArray, 'labels' => $labels];
}

function weekAndQuarter($invoiceQuarter, $chartInvoiceData, $payAtQuarter, $chartPaymentData)
{
    $preparedInvoiceData = [];

    foreach ($invoiceQuarter as $index => $value)
    {
        if (isset($chartInvoiceData[0]['invoice_date']))
        {
            foreach ($chartInvoiceData as $chartDataValue)
            {
                if ((Carbon::createFromDate($value)->format('Y-m-d') <= Carbon::createFromDate($chartDataValue['invoice_date'])->format('Y-m-d')) && (Carbon::createFromDate($chartDataValue['invoice_date'])->format('Y-m-d') <= Carbon::createFromDate($invoiceQuarter[$index + 1])->subDay()->format('Y-m-d')))
                {
                    $preparedInvoiceData[$value] = intval($chartDataValue['data']);
                }
                if (!isset($preparedInvoiceData[$value]))
                {
                    $preparedInvoiceData[$value] = 0;
                }
            }
        }
        else
        {
            $preparedInvoiceData[$value] = 0;
        }

        ksort($preparedInvoiceData);
    }

    $preparedPaymentData = [];

    foreach ($payAtQuarter as $index => $value)
    {
        if (isset($chartPaymentData[0]['paid_at']))
        {
            foreach ($chartPaymentData as $chartDataValue)
            {
                if ((Carbon::createFromDate($value)->format('Y-m-d') <= Carbon::createFromDate($chartDataValue['paid_at'])->format('Y-m-d')) && (Carbon::createFromDate($chartDataValue['paid_at'])->format('Y-m-d') <= Carbon::createFromDate($payAtQuarter[$index + 1])->subDay()->format('Y-m-d')))
                {
                    $preparedPaymentData[$value] = intval($chartDataValue['data']);
                }
                if (!isset($preparedPaymentData[$value]))
                {
                    $preparedPaymentData[$value] = 0;
                }
            }
        }
        else
        {
            $preparedPaymentData[$value] = 0;
        }

        ksort($preparedPaymentData);
    }

    return ['newInvoiceArray' => $preparedInvoiceData, 'newPaymentArray' => $preparedPaymentData];
}