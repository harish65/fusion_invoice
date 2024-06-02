<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Import\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'import_type' => 'required',
            'import_file' => 'required|mimes:txt',
        ];
    }

    public function messages()
    {
        return [
            'import_file.mimes' => trans('fi.import-file-type-error'),
        ];
    }
}