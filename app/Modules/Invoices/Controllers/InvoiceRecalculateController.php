<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Controllers;

use Exception;
use FI\Http\Controllers\Controller;
use FI\Modules\Invoices\Support\InvoiceCalculate;

class InvoiceRecalculateController extends Controller
{
    private $invoiceCalculate;

    public function __construct(InvoiceCalculate $invoiceCalculate)
    {
        $this->invoiceCalculate = $invoiceCalculate;
    }

    public function recalculate()
    {

        try
        {
            if (!config('app.demo'))
            {
                set_time_limit(0);

                $this->invoiceCalculate->calculateAll();
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('fi.functionality_not_available_on_demo')], 401);
            }
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return response()->json(['success' => true, 'message' => trans('fi.recalculation_complete')], 200);
    }
}