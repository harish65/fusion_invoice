<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Quotes\Requests;

class QuoteToInvoiceRequest extends QuoteStoreRequest
{
    public function rules()
    {
        return [
            'client_id'                 => 'required',
            'invoice_date'              => 'required',
            'document_number_scheme_id' => 'required',
        ];
    }
}