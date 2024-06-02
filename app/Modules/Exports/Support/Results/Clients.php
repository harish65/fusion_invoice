<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Support\Results;

use FI\Modules\Clients\Models\Client;
use FI\Modules\CustomFields\Support\CustomFieldsParser;

class Clients implements SourceInterface
{
    public function getResults($params = [])
    {
        $customFields = CustomFieldsParser::getFields('clients');
        $client       = Client::orderBy('name')
            ->select('clients.*')
            ->leftJoin('clients_custom', 'clients.id', '=', 'clients_custom.client_id');
        foreach ($customFields as $customField)
        {
            $client->addSelect("clients_custom." . $customField->column_name . " AS " . $customField->field_label);
        }

        return $client->get()->toArray();
    }
}