<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Support\Results;

use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\Payments\Models\PaymentInvoice;

class Payments implements SourceInterface
{
    public function getResults($params = [])
    {
        $customFields = CustomFieldsParser::getFields('payments');
        $payments     = PaymentInvoice::select('invoices.number', 'payments.paid_at', 'payment_invoices.invoice_amount_paid',
            'payment_methods.name AS payment_method', 'payments.note')
            ->leftJoin('payments', 'payment_invoices.payment_id', '=', 'payments.id')
            ->leftJoin('invoices', 'payment_invoices.invoice_id', '=', 'invoices.id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'payments.payment_method_id')
            ->leftJoin('payments_custom', 'payments.id', '=', 'payments_custom.payment_id')
            ->orderBy('invoices.number');
        foreach ($customFields as $customField)
        {
            $payments->addSelect("payments_custom." . $customField->column_name . " AS " . $customField->field_label);
        }

        return $payments->get()->toArray();
    }
}