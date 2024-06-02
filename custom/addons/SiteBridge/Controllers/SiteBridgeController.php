<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\SiteBridge\Controllers;

use Addons\SiteBridge\Models\SBClient;
use Addons\SiteBridge\Models\SBInvoice;
use Addons\SiteBridge\Models\SBInvoiceItems;
use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Modules\Payments\Models\Payment;
use FI\Modules\Payments\Models\PaymentInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SiteBridgeController extends Controller
{
    public function index()
    {
        return view('site_bridge.index');
    }

    public function import()
    {
        ini_set('max_execution_time', 300);

        config(['app.debug' => true]);

        $this->createConfig();

        $database = request('sb_database');

        try
        {
            DB::beginTransaction();

            Log::channel('site_bridge')->info("Sitebridge started");
            $sbClientsBefore = DB::select("select count(*) as numClients from sb_clients");

            DB::statement("insert into sb_clients (site_user_id, name, email, created_at, updated_at) 
            select id, concat(first_name, ' ', last_name), email, created_at, updated_at 
            from " . $database . '.users where id in (select user_id from ' . $database . '.purchase_header) 
            and id not in (select site_user_id from sb_clients)');

            $sbClientsAfter   = DB::select("select count(*) as numClients from sb_clients");
            $sbClientsCreated = $sbClientsAfter[0]->numClients - $sbClientsBefore[0]->numClients;

            Log::channel('site_bridge')->info("Sitebridge sbClient creation completed. " . $sbClientsCreated . " sbClient records created");
            DB::commit();

            // client records must exist for invoices to be posted. 


            DB::beginTransaction();

            // At this point new client entries into sb_clients will have an empty value for fi_client_id

            // We can't create an FI invoice from a license entry, since a license is now issued once and renewals will reference that same license.
            // The FI invoice must be created based on the purchase_header and purchase_detail lines.

            // Also, using ID not as the table's primary key is confusing. Surprised this was done originally, but might as well correct it now.

            // Get all records from the ficom purchase_header table that have not been pulled into sitebridge invoices already. 
            Log::channel('site_bridge')->info("Sitebridge fetching new purchases");

            $userPurchases = DB::select("select * from " . $database . '.purchase_header
                                where purchase_header.purchase_date >= "2023-03-28" and 
                                      purchase_header.id not in (select site_invoice_id from sb_invoices) 
                                order by purchase_header.id desc limit 100');

            Log::channel('site_bridge')->info("  Found " . count($userPurchases) . " purchases to pull into FI invoices");

            foreach ($userPurchases as $userInvoice)
            {
                $SBInvoice = SBInvoice::create([
                    'site_invoice_id' => $userInvoice->id,
                    'created_at'      => $userInvoice->created_at,
                    'updated_at'      => $userInvoice->updated_at,
                    'site_user_id'    => $userInvoice->user_id,
                    'total'           => $userInvoice->price_total,
                ]);

                $invoiceLines = DB::select("select purchase_detail.*, 
                    user_licenses.last_purchase_detail_id, user_licenses.download_id, user_licenses.product_id, user_licenses.expires_on, user_licenses.license_key, 
                    user_license_addons.user_licenses_id, user_license_addons.last_purchase_detail_id, user_license_addons.download_id, user_license_addons.product_id,                     
                    products.title, 
                    downloads.version 
                    from " . $database . '.purchase_detail 
                    join ' . $database . '.user_licenses on user_licenses.last_purchase_detail_id = purchase_detail.id 
                    left outer join ' . $database . '.user_license_addons on user_license_addons.last_purchase_detail_id = purchase_detail.id 
                    join ' . $database . '.downloads on downloads.id = user_licenses.download_id
                    left outer join ' . $database . '.downloads as downloads2 on downloads2.id = user_license_addons.download_id
                    join ' . $database . '.products on products.id = purchase_detail.product_id 
                    join ' . $database . '.users on users.id = user_licenses.user_id 
                    where purchase_detail.purchase_header_id = "' . $userInvoice->id . '" and purchase_detail.purchase_header_id in 
                    (select purchase_header.id from ' . $database . '.purchase_header
                    where purchase_header.id not in (select id from sb_invoices)) 
                    order by purchase_detail.id desc');

                foreach ($invoiceLines as $invoiceLine)
                {
                    $itemDesc = $invoiceLine->title;

                    if ($invoiceLine->addon == 0)
                    {
                        $itemDesc = ($itemDesc == $invoiceLine->description) ? '' : $invoiceLine->title . ' ';
                        $itemDesc = $itemDesc . $invoiceLine->description .
                            ' v' . $invoiceLine->version . "\r\n" .
                            ' key: ' . $invoiceLine->license_key . "\r\n" .
                            ' Support Expires: ' . $invoiceLine->expires_on;
                    }

                    SBInvoiceItems::create([
                        'sb_invoice_id'        => $SBInvoice->id,
                        'created_at'           => $invoiceLine->created_at,
                        'updated_at'           => $invoiceLine->updated_at,
                        'site_invoice_item_id' => $invoiceLine->id,
                        'item_name'            => $invoiceLine->title . ' ' . $invoiceLine->version,
                        'item_description'     => $itemDesc,
                        'license_key'          => $invoiceLine->license_key,
                        'price'                => $invoiceLine->price,
                    ]);
                }
            }
            DB::commit();
            Log::channel('site_bridge')->info("Sitebridge invoices and detail lines created");

            // Now all of the NEW entries sitebridge's clients, Invoices and Invoice Line Items have been written.

            // DB::beginTransaction();

            Log::channel('site_bridge')->info("Sitebridge Writing new SB clients to FI clients table");
            // Write new SB clients (users) to FI's clients table.
            foreach (SBClient::where('fi_client_id', 0)->get() as $sbClient)
            {
                $client = Client::create([
                    'name'       => $sbClient->name . ' ' . $sbClient->company,
                    'email'      => $sbClient->email,
                    'created_at' => $sbClient->created_at,
                    'updated_at' => $sbClient->updated_at,
                ]);

                $sbClient->fi_client_id = $client->id;
                $sbClient->save();
            }

            //Fetch all sitebridge invoices that don't yet have an assigned FI invoice ID.
            $sbInvoices = SBInvoice::select('sb_invoices.*', 'sb_clients.fi_client_id', 'sb_clients.email as client_email')
                ->with('sbInvoiceItems')
                ->join('sb_clients', 'sb_clients.site_user_id', '=', 'sb_invoices.site_user_id')
                ->where('sb_invoices.fi_invoice_id', 0)->orderBy('sb_invoices.id', 'DESC')->get();

            Log::channel('site_bridge')->info('Sitebridge found ' . count($sbInvoices) . ' sb_invoice records that need to been turned into live FI invoices.');

            foreach ($sbInvoices as $sbInvoice)
            {
                // Create the live FI Invoice
                $invoice = Invoice::create([
                    'client_id'    => $sbInvoice->fi_client_id,
                    'invoice_date' => $sbInvoice->created_at,
                    'due_at'       => $sbInvoice->created_at,
                    'number'       => $sbInvoice->site_invoice_id,
                ]);

                $sbInvoiceDetail = SBInvoice::find($sbInvoice->id);

                $sbInvoiceDetail->fi_client_id  = $invoice->client_id;
                $sbInvoiceDetail->fi_invoice_id = $invoice->id;
                $sbInvoiceDetail->save();

                Log::channel('site_bridge')->info("  FI invoice created: ID = " . $invoice->id . ' for client ID: ' . $invoice->client_id);

                $invoiceTotal = $sbInvoice->total;

                Log::channel('site_bridge')->info("    Creating FI invoice line items for Invoice_id: " . $invoice->id);

                $sbInvoiceLineItems = SBInvoiceItems::select('sb_invoice_items.*')
                    ->where('sb_invoice_id', $sbInvoice->id)->get();

                // foreach ($sbInvoiceTemp->sbInvoiceItems as $sbInvoiceLine)

                foreach ($sbInvoiceLineItems as $sbInvoiceLine)
                {
                    InvoiceItem::create([
                        'invoice_id'  => $invoice->id,
                        'name'        => $sbInvoiceLine->item_name,
                        'description' => $sbInvoiceLine->item_description,
                        'quantity'    => 1,
                        'price'       => $sbInvoiceLine->price,
                    ]);
                    Log::channel('site_bridge')->info("    Sitebridge FI invoice line item created: name = " . $sbInvoiceLine->item_name);
                }

                $payment = Payment::create([
                    'user_id'           => $invoice->user_id,
                    'client_id'         => $invoice->client_id,
                    'type'              => 'single',
                    'payment_method_id' => 3,
                    'paid_at'           => $sbInvoice->created_at,
                    'amount'            => $invoiceTotal,
                ]);
                Log::channel('site_bridge')->info("    Sitebridge FI invoice payment created: client_id = " . $invoice->client_id);

                PaymentInvoice::create([
                    'payment_id'          => $payment->id,
                    'invoice_id'          => $invoice->id,
                    'invoice_amount_paid' => $invoiceTotal,
                    'convenience_charges' => 0,
                ]);
                Log::channel('site_bridge')->info("    Sitebridge FI invoice paymentInvoice created: invoice_id = " . $invoice->id);
            }

            Log::channel('site_bridge')->info("Sitebridge Ended");

            return redirect()->route('siteBridge.index')
                ->with('alertSuccess', 'Finished');
        }
        catch (\Exception $e)
        {
            Log::channel('site_bridge')->error("Sitebridge process failed. " . $e->getMessage());

            DB::rollBack();

            return redirect()->route('siteBridge.index')
                ->withErrors($e->getMessage());
        }
    }

    private function createConfig()
    {
        config(['database.connections.' . request('sb_database') => [
            'host'      => 'localhost',
            'database'  => request('sb_database'),
            'username'  => request('sb_username'),
            'password'  => request('sb_password'),
            'driver'    => 'mysql',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'strict'    => false,
        ]]);
    }
}