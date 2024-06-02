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

use FI\Traits\CustomFieldValidator;

class ClientUpdateRequest extends ClientStoreRequest
{
    use CustomFieldValidator;

    private $customFieldType = 'clients';

    public function rules()
    {
        $rules                   = parent::rules();
        $rules['invoice_prefix'] = 'nullable|max:5|unique:clients,invoice_prefix,' . $this->route('id');

        return $rules;
    }
}