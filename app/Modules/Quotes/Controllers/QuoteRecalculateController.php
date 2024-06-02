<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Quotes\Controllers;

use Exception;
use FI\Http\Controllers\Controller;
use FI\Modules\Quotes\Support\QuoteCalculate;

class QuoteRecalculateController extends Controller
{
    private $quoteCalculate;

    public function __construct(QuoteCalculate $quoteCalculate)
    {
        $this->quoteCalculate = $quoteCalculate;
    }

    public function recalculate()
    {
        try
        {
            if (!config('app.demo'))
            {
                set_time_limit(0);

                $this->quoteCalculate->calculateAll();
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('fi.functionality_not_available_on_demo')], 401);
            }
        }
        catch (Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => trans('fi.recalculation_complete'),
        ], 200);
    }
}