<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Reports\Reports;

use FI\Modules\Expenses\Models\Expense;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;

class ExpenseListReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null, $categoryId = null, $vendorId = null, $sort_option = null, $group_option = null)
    {
        $results = [
            'from_date'           => DateFormatter::format($fromDate),
            'to_date'             => DateFormatter::format($toDate),
            'tax_total'           => '0',
            'total'               => '0',
            'formatted_tax_total' => '0',
            'formatted_total'     => '0',
            'records'             => [],
        ];

        $expenses = Expense::defaultQuery()
            ->where('expense_date', '>=', $fromDate)
            ->where('expense_date', '<=', $toDate);


        if ($companyProfileId)
        {
            $expenses->where('expenses.company_profile_id', $companyProfileId);
        }

        if ($categoryId)
        {
            $expenses->where('expenses.category_id', $categoryId);
        }

        if ($vendorId)
        {
            $expenses->where('expenses.vendor_id', $vendorId);
        }
        if ($sort_option)
        {
            if ($sort_option == 'amount')
            {
                $expenses->orderBy('amount', 'asc');
            }
            elseif ($sort_option == 'expense_date')
            {
                $expenses->orderBy('expense_date', 'asc')->orderBy('id', 'desc');
            }
            elseif ($sort_option == 'category')
            {
                $expenses->orderBy('category_name', 'asc');
            }
            elseif ($sort_option == 'vendor')
            {
                $expenses->orderBy('vendor_name', 'asc');
            }
        }
        else
        {
            $expenses->orderBy('expense_date', 'desc')->orderBy('id', 'desc');
        }
        $expenses = $expenses->get();
        foreach ($expenses as $expense)
        {
            $group = 'none';
            if ($group_option == 'category')
            {
                $group                                    = $expense->category_id;
                $results['records'][$group]['group_name'] = $expense->category_name;
            }
            if ($group_option == 'vendor')
            {
                $group                                    = $expense->vendor_id;
                $results['records'][$group]['group_name'] = $expense->vendor_name;
            }
            if ($group_option == 'client')
            {
                $group                                    = $expense->client_id;
                $results['records'][$group]['group_name'] = $expense->client_name;
            }
            $results['records'][$group]['items'][] = [
                'date'            => $expense->formatted_expense_date,
                'amount'          => $expense->formatted_amount,
                'tax'             => $expense->formatted_tax,
                'total'           => $expense->total,
                'formatted_total' => $expense->formatted_total,
                'category'        => $expense->category_name,
                'vendor'          => $expense->vendor_name,
                'client'          => $expense->client_name,
                'billed'          => $expense->has_been_billed,
            ];
            if (isset($results['records'][$group]['totals']))
            {
                $results['records'][$group]['totals']['subtotal'] += $expense->amount;
                $results['records'][$group]['totals']['tax'] += $expense->tax;
                $results['records'][$group]['totals']['total'] += $expense->total;
            }
            else
            {
                $results['records'][$group]['totals']['subtotal'] = $expense->amount;
                $results['records'][$group]['totals']['tax']      = $expense->tax;
                $results['records'][$group]['totals']['total']    = $expense->total;
            }
            $results['records'][$group]['totals']['formatted_subtotal'] = CurrencyFormatter::format($results['records'][$group]['totals']['total']);
            $results['records'][$group]['totals']['formatted_tax']      = CurrencyFormatter::format($results['records'][$group]['totals']['tax']);
            $results['tax_total'] += $expense->tax;
            $results['total'] += $expense->total;
            $results['formatted_tax_total'] = CurrencyFormatter::format($results['tax_total']);
            $results['formatted_total']     = CurrencyFormatter::format($results['total']);
        }
        return $results;
    }
}