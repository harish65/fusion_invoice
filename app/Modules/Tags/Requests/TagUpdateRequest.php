<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Tags\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'tag_name_update' => trans('fi.tag_new_name'),
            'tag_id'          => trans('fi.tag_name'),
        ];
    }

    public function rules()
    {
        return [
            'tag_id'          => 'required',
            'tag_name_update' => 'required',
        ];
    }
}