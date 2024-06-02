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
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Expenses\Models\Expense;
use FI\Modules\Expenses\Models\ExpenseCategory;
use FI\Modules\Expenses\Models\ExpenseVendor;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
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
        $expenses = [];

        for ($i = 0; $i < $this->count; $i++)
        {
            $expenseCategory = ExpenseCategory::create([
                'name' => $this->faker->text(5),
            ]);

            $expenseVendor = ExpenseVendor::create([
                'name'        => $this->faker->name,
                'email'       => $this->faker->email,
                'mobile'      => $this->faker->phoneNumber,
                'category_id' => $expenseCategory->id,
            ]);

            $toExpense  = Expense::create([
                'expense_date'       => Carbon::now()->format('Y-m-d'),
                'user_id'            => auth()->id(),
                'category_id'        => $expenseCategory->id,
                'vendor_id'          => $expenseVendor->id,
                'invoice_id'         => null,
                'description'        => $this->faker->text,
                'amount'             => $this->faker->randomNumber(),
                'company_profile_id' => CompanyProfile::inRandomOrder()->first()->id,
            ]);
            $expenses[] = $toExpense;

        }
        return $expenses;
    }
}
