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
use Illuminate\Validation\Rule;

class MappingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->request->get('id');
        return [
            'type'        => 'required',
            'name'        => [
                'required',
                ($id) ? Rule::unique('import_mappings')->ignore($id, 'id') : Rule::unique('import_mappings'),
            ],
            'description' => 'required|array',
        ];
    }
}