<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Clients\Requests;

use FI\Modules\Clients\Models\Client;
use FI\Modules\Users\Models\User;
use FI\Traits\CustomFieldValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ClientStoreRequest extends FormRequest
{
    use CustomFieldValidator;

    private $customFieldType = 'clients';

    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name'  => trans('fi.name'),
            'email' => trans('fi.email'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();
        $request['email'] = $this->input('client_email', $this->input('email', ''));

        unset($request['client_email']);

        if (isset($request['client_id']))
        {
            if ($request['allow_child_accounts'] == 0 && Client::getChildClients($request['client_id'])->count() > 0)
            {
                $request['allow_child_accounts'] = 1;
                Log::info('Client id: ' . $this->clientId . '->allow_child_accounts reverted to Yes because child records existed.');
            }

            if ($request['third_party_bill_payer'] == 0 && Client::getThirdPartyBillPayers($request['client_id'])->count() > 0)
            {
                $request['third_party_bill_payer'] = 1;
                Log::info('Client id: ' . $this->clientId . '->third_party_bill_payer reverted to Yes because child records existed.');
            }
        }

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'name'                   => 'required',
            'email'                  => 'required_if:allow_client_center_login,1|email',
            'invoice_prefix'         => 'nullable|max:5',
            'allow_child_accounts'   => 'nullable',
            'third_party_bill_payer' => 'nullable',
            'type'                   => 'required|in:lead,prospect,customer,affiliate,other',
            'password'               => Rule::requiredIf(function () {
                    $allowClient_center_login = $this->input('allow_client_center_login', 0);
                    $user                     = User::whereUserType('client')->whereClientId($this->client_id)->first();
                    $oldPassword              = ($user != null) ? $user->password : '';
                    return $allowClient_center_login == 1 && $oldPassword == '' ? true : false;
                }) . '|confirmed',
        ];
    }

}