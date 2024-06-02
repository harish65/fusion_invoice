<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\API\Requests;

use FI\Support\NumberFormatter;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class APIQuoteItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name'     => trans('fi.name'),
            'quantity' => trans('fi.quantity'),
            'price'    => trans('fi.price'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

        $request['quantity'] = NumberFormatter::unformat($request['quantity']);
        $request['price']    = NumberFormatter::unformat($request['price']);

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'quote_id' => 'required',
            'name'     => 'required_with:price,quantity',
            'quantity' => 'required_with:price,name|numeric',
            'price'    => 'required_with:name,quantity|numeric',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => (new ValidationException($validator))->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}