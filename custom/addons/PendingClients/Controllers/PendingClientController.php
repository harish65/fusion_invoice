<?php

namespace Addons\PendingClients\Controllers;

use Addons\PendingClients\Models\ClientPending;
use Addons\PendingClients\PendingClientsFormatter;
use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Repositories\ClientRepository;

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

        return view('pending_clients.index')
            ->with('clients', $clients);
    }

    public function keep($id)
    {
        $client = ClientPending::find($id);

        $client->client_keep = 1;

        $client->save();

        $this->clientRepository->create(
            [
                'name'    => ucwords($client->name),
                'address' => ucwords($client->address),
                'phone'   => PendingClientsFormatter::phone($client->phone),
                'email'   => ucwords($client->email),
                'city'    => ucwords($client->city),
                'state'   => ucwords($client->state),
                'zip'     => $client->zip
            ]
        );

        return redirect()->route('pendingClients.index')
            ->with('alertSuccess', 'Pending client record successfully recorded.');
    }

    public function delete($id)
    {
        $client = ClientPending::find($id);

        $client->client_delete = 1;

        $client->save();

        return redirect()->route('pendingClients.index')
            ->with('alert', 'Pending client record successfully removed.');
    }
}