<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Seeder\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DataSeederRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'module'              => 'required',
            'number_of_seed_data' => 'required|numeric|between:1,25',
        ];
    }
}