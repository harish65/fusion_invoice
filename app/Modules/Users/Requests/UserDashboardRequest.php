<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserDashboardRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'userId' => trans('fi.users'),
        ];
    }

    public function rules()
    {
        return [
            'userId'   => 'required',
            'name'     => 'required',
            "skin"     => 'required',
            'password' => 'confirmed|min:6',
        ];
    }
}