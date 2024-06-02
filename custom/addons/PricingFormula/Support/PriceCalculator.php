<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PricingFormula\Support;

use FI\Modules\ItemLookups\Models\ItemLookup;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class PriceCalculator
{
    public static function calculate($id, $quantity, $custom)
    {

        $itemLookup                  = ItemLookup::with('formula')->find($id);
        $itemLookup->formula_applied = false;
        if ($itemLookup->formula_id != null)
        {
            $formulas = explode(',', $itemLookup->formula->formula);
            $price    = $itemLookup->price;
            $days     = (isset($custom['days']) && $custom['days'] != '') ? $custom['days'] : 0;
            $quantity = ($quantity != '' ? $quantity : (($itemLookup->quantity != null) ? $itemLookup->quantity : 0));
            $lt       = ' < ';
            $lte      = ' <= ';
            $gt       = ' > ';
            $gte      = ' >= ';

            foreach ($formulas as $key => $formula)
            {
                $formulaAndDiscount = explode('=', $formula);
                $expressionLanguage = new ExpressionLanguage();
                $actualFormula      = $expressionLanguage->evaluate(str_replace(['{quantity}', '{price}', '{days}', '{lt}', '{lte}', '{gt}', '{gte}'], [$quantity, $price, $days, $lt, $lte, $gt, $gte], $formulaAndDiscount[0]));

                if ($actualFormula)
                {

                    if (str_contains($formulaAndDiscount[1], '*'))
                    {
                        $itemLookup->price = $expressionLanguage->evaluate(str_replace(['{quantity}', '{price}', '{days}'], [$quantity, $price, $days], $formulaAndDiscount[1]));
                    }
                    else
                    {
                        $itemLookup->price = $formulaAndDiscount[1];
                    }

                    $itemLookup->formula_applied = true;

                    return $itemLookup;
                }
            }
        }

        return $itemLookup;

    }

}