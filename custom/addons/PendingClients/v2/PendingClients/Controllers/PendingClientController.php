<?php

namespace FI\Modules\PendingClients\Controllers;

use FI\Modules\Clients\Repositories\ClientRepository;
use FI\Modules\PendingClients\Models\ClientPending;
use FI\Modules\PendingClients\PendingClientsFormatter;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class PendingClientController extends Controller
{
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function index()
    {
        $clients = ClientPending::where('client_keep', 0)
            ->where('client_delete', 0)
            ->orderBy('client_id', 'desc')
            ->get();

        return View::make('pending_clients.index')
            ->with('clients', $clients);
    }

    public function keep($id)
    {
        $client = ClientPending::find($id);

        $client->client_keep = 1;

        $client->save();

        $this->clientRepository->create(
            [
                'name'             => ucwords($client->name),
                'address'          => ucwords($client->address) . "\r\n" . ucwords($client->city) . ', ' . ucwords($client->state) . ' ' . $client->zip,
                'phone'            => PendingClientsFormatter::phone($client->phone),
                'email'            => ucwords($client->email),
                'currency_code'    => Config::get('fi.baseCurrency'),
                'invoice_template' => Config::get('fi.invoiceTemplate'),
                'quote_template'   => Config::get('fi.quoteTemplate')
            ]
        );

        return Redirect::route('pendingClients.index')
            ->with('alertSuccess', 'Pending client record successfully recorded.');
    }

    public function delete($id)
    {
        $client = ClientPending::find($id);

        $client->client_delete = 1;

        $client->save();

        return Redirect::route('pendingClients.index')
            ->with('alert', 'Pending client record successfully removed.');
    }
}