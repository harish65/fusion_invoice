<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'price_formula'               => 'Pricing Formulas',
    'item_price_formulas'         => 'Pricing Formulas',
    'item_price_formula'          => 'Pricing Formula',
    'item_price_formula_form'     => 'Pricing Formula',
    'formula'                     => 'Formula',
    'select_formula'              => 'Select Formula',
    'invoice_price_formula_notes' => '<b><u>Formula Examples:</u></b> <br> <b>Quantity based 20%, 25% and 40% discount on line item subtotal:</b> <span>{quantity} {gt} 10 && {quantity} {lt} 20 = {price} * 0.80,{quantity} {gt} 20 && {quantity} {lt} 50 = {price} * 0.75,{quantity} {gt} 50 = {price} * 0.60</span> <i class="fa fa-clipboard copy-to-clipboard clickable" title="Copy To Clipboard"></i><br> <b>Days > 5 then 10% discount:</b> <span>{quantity} {gt} 10 && {days} {gt} 5 = {price} * 0.9</span> <i class="fa fa-clipboard copy-to-clipboard clickable" title="Copy To Clipboard"></i><br> <b>Custom variables are {quantity}, {days}, {price}, {gt}, {gte}, {lt}, {lte}</b> ',
    'copied'                      => 'Formula copied to clipboard',
    'item_price_formula_create'   => 'Pricing Formula Create',
];
