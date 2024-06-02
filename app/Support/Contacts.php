<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

use Collective\Html\FormFacade;
use FI\Modules\Clients\Models\Client;

class Contacts
{
    private $client;
    private $user;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->user   = auth()->user();
    }

    public function contactDropdownTo($defaultUser = true)
    {
        $allContacts      = $this->getAllContacts($defaultUser);
        $selectedContacts = $this->getSelectedContactsTo();

        return FormFacade::select('to', $allContacts, $selectedContacts, ['id' => 'to', 'multiple' => 'multiple', 'class' => 'form-control form-control-sm']);
    }

    public function contactDropdownCc()
    {
        $allContacts      = $this->getAllContacts();
        $selectedContacts = $this->getSelectedContactsCc();

        return FormFacade::select('cc', $allContacts, $selectedContacts, ['id' => 'cc', 'multiple' => 'multiple', 'class' => 'form-control form-control-sm']);
    }

    public function contactDropdownBcc()
    {
        $allContacts      = $this->getAllContacts();
        $selectedContacts = $this->getSelectedContactsBcc();

        return FormFacade::select('bcc', $allContacts, $selectedContacts, ['id' => 'bcc', 'multiple' => 'multiple', 'class' => 'form-control form-control-sm']);
    }

    public function getSelectedContactsTo()
    {
        if ($this->client->email_default == 'use_parent_email' && $this->client->parent_client_id)
        {
            return collect([$this->client->parent->email]);
        }
        elseif ($this->client->email_default == 'use_third_party_bill_payer_email' && $this->client->invoices_paid_by && $this->client->invoices_paid_by_email)
        {
            return collect([$this->client->invoices_paid_by_email]);
        }
        else
        {
            return $this->client->contacts->where('default_to', 1)->pluck('email')->prepend($this->client->email);
        }
    }

    public function getContactsTo()
    {
        return $this->client->contacts->pluck('name', 'email');
    }

    public function getSelectedContactsCc()
    {
        $contacts = $this->client->contacts
            ->where('default_cc', 1)
            ->pluck('email')
            ->toArray();

        if (config('fi.mailDefaultCc'))
        {
            $contacts = array_merge($contacts, [config('fi.mailDefaultCc')]);
        }

        return $contacts;
    }

    public function getSelectedContactsBcc()
    {
        $contacts = $this->client->contacts
            ->where('default_bcc', 1)
            ->pluck('email')
            ->toArray();

        if (config('fi.mailDefaultBcc'))
        {
            $contacts = array_merge($contacts, [config('fi.mailDefaultBcc')]);
        }

        return $contacts;
    }

    public function getAllContacts($defaultUser = true)
    {
        $contacts = [];
        if ($this->client->email)
        {
            $contacts[$this->client->email] = $this->getFormattedContact($this->client->name, $this->client->email);
        }
        if ($this->client->email_default == 'use_parent_email' && $this->client->parent_client_id)
        {
            $parentClient                   = $this->client->parent;
            $contacts[$parentClient->email] = $this->getFormattedContact($parentClient->name, $parentClient->email);
        }
        if ($this->client->email_default == 'use_third_party_bill_payer_email' && $this->client->invoices_paid_by && $this->client->invoices_paid_by_email)
        {
            $contacts[$this->client->invoices_paid_by_email] = $this->getFormattedContact($this->client->invoices_paid_by_name, $this->client->invoices_paid_by_email);
        }

        foreach ($this->client->contacts->pluck('name', 'email') as $email => $name)
        {
            $contacts[$email] = $this->getFormattedContact($name, $email);
        }

        if ($defaultUser == true)
        {
            $contacts[$this->user->email] = $this->getFormattedContact($this->user->name, $this->user->email);
        }

        if (config('fi.mailDefaultCc'))
        {
            $contacts[config('fi.mailDefaultCc')] = config('fi.mailDefaultCc');
        }

        if (config('fi.mailDefaultBcc'))
        {
            $contacts[config('fi.mailDefaultBcc')] = config('fi.mailDefaultBcc');
        }

        return $contacts;
    }

    private function getFormattedContact($name, $email)
    {
        return $name . ' <' . $email . '>';
    }
}
