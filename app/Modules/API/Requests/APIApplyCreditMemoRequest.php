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

use FI\Modules\Invoices\Models\Invoice;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class APIApplyCreditMemoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'invoice_id'     => 'required|integer|exists:invoices,id,type,invoice',
            'credit_memo_id' => 'required|integer|exists:invoices,id,type,credit_memo',
            'amount'         => ['required', 'numeric',
                function ($attribute, $value, $fail) {
                    $invoice    = Invoice::find($this->input('invoice_id'));
                    $creditMemo = Invoice::find($this->input('credit_memo_id'));
                    if ($invoice == null || $creditMemo == null)
                    {
                        return true;
                    }
                    if ($value <= $invoice->amount->balance)
                    {
                        if (abs($creditMemo->amount->balance) < $value)
                        {
                            $fail(trans('fi.payment_amount_mismatch'));
                        }
                    }
                    else
                    {
                        $fail(trans('fi.entered_amount_less_than_invoice_amount'));
                    }
                },
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => (new ValidationException($validator))->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}