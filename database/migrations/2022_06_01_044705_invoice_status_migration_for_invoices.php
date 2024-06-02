<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FI\Modules\Invoices\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class InvoiceStatusMigrationForInvoices extends Migration
{
    public function up()
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $output = new ConsoleOutput();
        try
        {
            DB::transaction(function ()
            {
                $mailQueues = DB::table('mail_queue')->select('mail_queue.id', 'mail_queue.mailable_id', 'mail_queue.created_at')
                                ->join('invoices', function ($join)
                                {
                                    $join->on('invoices.id', '=', 'mail_queue.mailable_id');
                                })
                                ->where('mail_queue.mailable_type', 'FI\Modules\Invoices\Models\Invoice')
                                ->where('mail_queue.sent', 1)
                                ->where('invoices.date_emailed', '=', null)
                                ->orderBy('mail_queue.created_at', 'DESC')
                                ->get();

                foreach ($mailQueues as $mailQueue)
                {
                    $invoice = Invoice::whereId($mailQueue->mailable_id)->first();
                    $invoice->update(['date_emailed' => $mailQueue->created_at]);
                }

                $transitions = DB::table('transitions')->select('transitions.id', 'transitions.transitionable_id', 'transitions.created_at')
                                 ->join('invoices', function ($join)
                                 {
                                     $join->on('invoices.id', '=', 'transitions.transitionable_id');
                                 })
                                 ->where('transitions.transitionable_type', '=', 'FI\Modules\Invoices\Models\Invoice')
                                 ->where('transitions.action_type', '=', 'status_changed')
                                 ->where('transitions.current_value', '=', 'mailed')
                                 ->orderBy('transitions.created_at', 'DESC')
                                 ->get();

                foreach ($transitions as $transition)
                {
                    $invoice = Invoice::whereId($transition->transitionable_id)->first();
                    $invoice->update(['date_mailed' => $transition->created_at]);
                }

                $invoices = Invoice::whereNotIn('status', ['canceled', 'sent', 'draft'])->get();

                foreach ($invoices as $invoice)
                {
                    switch ($invoice->status)
                    {
                        case 'mailed':
                            $invoice->update(['status' => 'sent']);
                            break;
                        default:
                            if ($invoice->date_mailed != '' && $invoice->date_emailed != '')
                            {
                                $invoice->update(['status' => 'sent']);
                            }
                            else
                            {
                                $invoice->update(['status' => 'draft']);
                            }
                    }
                }

                DB::statement("ALTER TABLE `invoices` CHANGE `status` `status` ENUM('draft','sent','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;");

            });
        }
        catch (\Exception $e)
        {
            $output->writeln($e->getMessage());
        }
    }
}
