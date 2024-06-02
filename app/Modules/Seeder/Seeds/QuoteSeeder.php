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
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\Quotes\Models\QuoteItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuoteSeeder extends Seeder
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
        $quotes = [];
        for ($i = 0; $i < $this->count; $i++)
        {
            $toQuote  = Quote::create([
                'user_id'                   => auth()->id(),
                'client_id'                 => Client::inRandomOrder()->first()->id,
                'document_number_scheme_id' => DocumentNumberScheme::inRandomOrder()->first()->id,
                'currency_code'             => config('fi.baseCurrency'),
                'exchange_rate'             => 1.00000,
                'template'                  => 'default.blade.php',
                'summary'                   => $this->faker->text,
                'discount'                  => 0.00,
                'url_key'                   => Str::random(32),
                'company_profile_id'        => CompanyProfile::inRandomOrder()->first()->id,
                'quote_date'                => Carbon::now(),
                'terms'                     => $this->faker->text,
                'footer'                    => $this->faker->text,
                'number'                    => 'QUE' . rand(5, 5000),
            ]);
            $quotes[] = $toQuote;

            QuoteItem::create([
                'quote_id'      => $toQuote->id,
                'name'          => $this->faker->text(10),
                'display_order' => 1,
                'quantity'      => rand(1, 20),
                'tax_rate_id'   => 0.00,
                'tax_rate_2_id' => 0.00,
                'description'   => $this->faker->text,
                'price'         => rand(10, 30),
            ]);
        }
        return $quotes;
    }
}
