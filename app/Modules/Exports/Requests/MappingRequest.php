<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MappingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type'        => 'required',
            'name'        => 'required',
            'format'      => 'in:CSV,JSON,XLS,XML',
            'description' => 'required|array',
        ];
    }
}