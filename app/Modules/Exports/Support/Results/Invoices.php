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

use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\Invoices\Models\Invoice;

class Invoices implements SourceInterface
{
    public function getResults($params = [])
    {
        $customFields = CustomFieldsParser::getFields('invoices');
        $invoice      = Invoice::select('invoices.number', 'invoices.created_at', 'invoices.updated_at', 'invoices.invoice_date',
            'invoices.due_at', 'invoices.terms', 'invoices.footer', 'invoices.url_key', 'invoices.currency_code',
            'invoices.exchange_rate', 'invoices.template', 'invoices.summary', 'document_number_schemes.name AS group', 'clients.name AS client_name',
            'clients.email AS client_email', 'clients.address AS client_address', 'clients.city AS client_city',
            'clients.state AS client_state', 'clients.zip AS client_zip', 'clients.country AS client_country',
            'users.name AS user_name', 'users.email AS user_email',
            'company_profiles.company AS company', 'company_profiles.address AS company_address',
            'company_profiles.city AS company_city', 'company_profiles.state AS company_state',
            'company_profiles.zip AS company_zip', 'company_profiles.country AS company_country',
            'invoice_amounts.subtotal', 'invoice_amounts.tax', 'invoice_amounts.total',
            'invoice_amounts.paid', 'invoice_amounts.balance')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('document_number_schemes', 'document_number_schemes.id', '=', 'invoices.document_number_scheme_id')
            ->join('users', 'users.id', '=', 'invoices.user_id')
            ->join('company_profiles', 'company_profiles.id', '=', 'invoices.company_profile_id')
            ->leftJoin('invoices_custom', 'invoices.id', '=', 'invoices_custom.invoice_id')
            ->orderBy('number');
        foreach ($customFields as $customField)
        {
            $invoice->addSelect("invoices_custom." . $customField->column_name . " AS " . $customField->field_label);
        }
        return $invoice->get()->toArray();
    }
}