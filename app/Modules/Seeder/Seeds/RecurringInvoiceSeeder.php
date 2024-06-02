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
use FI\Modules\RecurringInvoices\Models\RecurringInvoice;
use FI\Modules\RecurringInvoices\Models\RecurringInvoiceItem;
use Illuminate\Database\Seeder;

class RecurringInvoiceSeeder extends Seeder
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
        $recurringInvoices = [];
        for ($i = 0; $i < $this->count; $i++)
        {
            $toRiInvoice = RecurringInvoice::create([
                'user_id'                   => auth()->id(),
                'client_id'                 => Client::inRandomOrder()->first()->id,
                'document_number_scheme_id' => DocumentNumberScheme::inRandomOrder()->first()->id,
                'company_profile_id'        => CompanyProfile::inRandomOrder()->first()->id,
                'terms'                     => $this->faker->text,
                'footer'                    => $this->faker->text,
                'currency_code'             => config('fi.baseCurrency'),
                'exchange_rate'             => 1.00000,
                'template'                  => 'default.blade.php',
                'summary'                   => $this->faker->text,
                'discount'                  => 0.00,
                'recurring_frequency'       => 1,
                'recurring_period'          => 3,
                'next_date'                 => Carbon::now()->addDays(2),
                'stop_date'                 => Carbon::now()->addMonth(),
            ]);

            $recurringInvoices[] = $toRiInvoice;

            RecurringInvoiceItem::create([
                'recurring_invoice_id' => $toRiInvoice->id,
                'name'                 => $this->faker->text(10),
                'quantity'             => rand(1, 20),
                'tax_rate_id'          => 0.00,
                'tax_rate_2_id'        => 0.00,
                'description'          => $this->faker->text,
                'price'                => rand(10, 30)
            ]);
        }
        return $recurringInvoices;
    }
}
