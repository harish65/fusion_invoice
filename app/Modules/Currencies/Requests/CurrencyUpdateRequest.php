<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Currencies\Requests;

class CurrencyUpdateRequest extends CurrencyStoreRequest
{
    public function rules()
    {
        return [
            'name'      => 'required',
            'code'      => 'required|unique:currencies,code,' . $this->route('id'),
            'symbol'    => 'required',
            'placement' => 'required',
            'decimal'   => 'required',
            'thousands' => 'required',
        ];
    }

}