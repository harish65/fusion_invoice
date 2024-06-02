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

use Faker;
use FI\Modules\Clients\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
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
        $clients = [];
        $type    = ['lead', 'prospect', 'customer', 'affiliate', 'other'];
        for ($i = 0; $i < $this->count; $i++)
        {
            $seedClients = Client::create([
                'user_id'                => auth()->user()->id,
                'name'                   => $this->faker->company(),
                'phone'                  => $this->faker->phoneNumber(),
                'email'                  => $this->faker->email(),
                'type'                   => $type[shuffle($type)],
                'address'                => $this->faker->streetAddress() . PHP_EOL . $this->faker->city() . ', ' . $this->faker->stateAbbr() . '  ' . $this->faker->postCode(),
                'allow_child_accounts'   => rand(0, 1),
                'third_party_bill_payer' => rand(0, 1),
            ]);
            $clients[]   = $seedClients;
        }
        return $clients;
    }
}
