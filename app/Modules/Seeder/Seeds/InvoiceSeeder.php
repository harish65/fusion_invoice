<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Seeder\Seeds;

use Carbon\Carbon;
use Faker;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\DocumentNumberSchemes\Models\DocumentNumberScheme;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceItem;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    protected $count;

    protected $faker;

    public function __construct($count = 0)
    {
        $this->count = $count;
        $this->faker = Faker\Factory::create();
    }

    public function run()
    {
        $invoices = [];
        for ($i = 0; $i < $this->count; $i++)
        {
            $toInvoice = Invoice::create([
                'invoice_date'              => Carbon::now(),
                'status'                    => 'draft',
                'type'                      => 'invoice',
                'user_id'                   => auth()->id(),
                'client_id'                 => Client::inRandomOrder()->first()->id,
                'document_number_scheme_id' => DocumentNumberScheme::inRandomOrder()->first()->id,
                'number'                    => 'INV' . rand(5, 5000),
                'url_key'                   => $this->faker->password(32),
                'currency_code'             => config('fi.baseCurrency'),
                'exchange_rate'             => 1.00000,
                'template'                  => 'default.blade.php',
                'summary'                   => $this->faker->text,
                'viewed'                    => 0,
                'discount'                  => 0.00,
                'company_profile_id'        => CompanyProfile::inRandomOrder()->first()->id,
                'recurring_invoice_id'      => 0,
                'total_convenience_charges' => 0.00,
                'terms'                     => $this->faker->text,
                'footer'                    => $this->faker->text,
                'date_emailed'              => null,
                'date_mailed'               => null,
            ]);

            $invoices[] = $toInvoice;

            InvoiceItem::create([
                'invoice_id'     => $toInvoice->id,
                'name'           => $this->faker->text(15),
                'quantity'       => rand(1, 20),
                'tax_rate_id'    => 0.00,
                'tax_rate_2_id'  => 0.00,
                'description'    => $this->faker->text,
                'original_price' => 0.00,
                'discount_type'  => null,
                'discount'       => 0.00,
                'price'          => rand(10, 30),
                'previous_price' => 0.00,
            ]);
        }
        return $invoices;
    }
}
