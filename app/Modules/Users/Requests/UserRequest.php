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

class UserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'userId'       => trans('fi.users'),
            'userParentId' => trans('fi.current_user_id'),
        ];
    }

    public function rules()
    {
        return [
            'userId'       => 'required',
            'userParentId' => 'required',
        ];
    }
}