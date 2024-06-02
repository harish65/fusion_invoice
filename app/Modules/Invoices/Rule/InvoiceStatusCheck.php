<?php

namespace FI\Modules\Invoices\Rule;

use FI\Modules\Invoices\Models\InvoiceAmount;
use Illuminate\Contracts\Validation\Rule;

class InvoiceStatusCheck implements Rule
{

    public int $invoiceId;

    public function __construct($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function passes($attribute, $value)
    {
        if ($value == 'canceled')
        {
            $paid = InvoiceAmount::whereinvoiceId($this->invoiceId)->first()->paid;

            if (intval($paid) != 0)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }

    public function message()
    {
        return trans('fi.paid_invoice_canceled_error');
    }
}