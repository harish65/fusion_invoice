<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Setup\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KeyVerificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'key.required' => trans('fi.key_required'),
            'key.min'      => trans('fi.key_length_invalid'),
        ];
    }

    public function rules()
    {
        return [
            'key' => 'required|min:32',
        ];
    }
}