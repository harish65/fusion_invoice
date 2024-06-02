<?php

namespace Addons\Conversions\Controllers;

use Addons\Conversions\Support\ConversionFactory;
use FI\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ConversionController extends Controller
{
    public function index()
    {
        return view('conversions.index')
            ->with('drivers', ConversionFactory::getDrivers())
            ->with('conversions');
    }

    public function create()
    {
        $this->createConfig();

        try
        {
            $converter = ConversionFactory::create(request('driver'));

            $converter->convert();
        }
        catch (\Exception $e)
        {
            return response()->json(['errors' => [[$e->getMessage()]]], 400);
        }
    }

    public function compare()
    {
        $this->createConfig();

        $converter = ConversionFactory::create(request('driver'));

        return view('conversions._compare')
            ->with('comparisons', $converter->compare());
    }

    public function truncateTables()
    {
        DB::table('clients')->truncate();
        DB::table('clients_custom')->truncate();
        DB::table('invoices')->truncate();
        DB::table('invoices_custom')->truncate();
        DB::table('invoice_amounts')->truncate();
        DB::table('invoice_items')->truncate();
        DB::table('invoice_item_amounts')->truncate();
        DB::table('payments')->truncate();
        DB::table('payments_custom')->truncate();
        DB::table('quotes')->truncate();
        DB::table('quotes_custom')->truncate();
        DB::table('quote_amounts')->truncate();
        DB::table('quote_items')->truncate();
        DB::table('quote_item_amounts')->truncate();
        DB::table('expenses')->truncate();
        DB::table('expense_categories')->truncate();
        DB::table('expense_vendors')->truncate();
    }

    private function createConfig()
    {
        config(['database.connections.' . request('driver') => [
            'host'      => request('hostname'),
            'database'  => request('database'),
            'username'  => request('username'),
            'password'  => request('password'),
            'prefix'    => request('prefix'),
            'driver'    => 'mysql',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'strict'    => false
        ]]);
    }
}