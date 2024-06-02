<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Tasks\Controllers;

use Addons\Commission\Models\InvoiceItemCommission;
use Carbon\Carbon;
use FI\Http\Controllers\Controller;
use FI\Jobs\GenerateTaskDueNotification;
use FI\Jobs\GenerateTimelineHistory;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\Invoices\Events\InvoiceCreatedRecurring;
use FI\Modules\Invoices\Events\OverdueNoticeEmailed;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Modules\MailQueue\Support\MailQueue;
use FI\Modules\RecurringInvoices\Models\RecurringInvoice;
use FI\Modules\RecurringInvoices\Requests\RecurringInvoiceCreateLiveInvoiceRequest;
use FI\Modules\Tags\Models\Tag;
use FI\Modules\TaskList\Models\Task;
use FI\Modules\Tasks\Events\AddNotification;
use FI\Modules\Users\Models\User;
use FI\Support\Contacts;
use FI\Support\DateFormatter;
use FI\Support\Parser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    private $mailQueue;

    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function run()
    {

        try
        {
            if (!config('app.demo'))
            {
                $cronStartInfo = trans('fi.cron_start', ['datetime' => Carbon::now()->format(config('fi.dateFormat') . ' h:i:s')]);

                $this->recurTasks();

                $this->recurInvoices();

                $this->queueOverdueInvoices();

                $this->queueUpcomingInvoices();

                $this->taskDueNotification();

                if (isset(auth()->user()->id))
                {
                    return redirect()->route('dashboard.index')
                        ->with('alertSuccess', trans('fi.daily_task_ran'));
                }
                else
                {
                    echo trans('fi.daily_task_ran');
                    exit;
                }
            }
            else
            {
                return redirect()->back()->withErrors(trans('fi.functionality_not_available_on_demo'));
            }
        }
        catch (\Exception $e)
        {
            event(new AddNotification(
                [
                    'message'  => $cronStartInfo . "<br>" .
                        trans('fi.cron_error', ['datetime' => Carbon::now()->format(config('fi.dateFormat') . ' h:i:s')]) . "<br>" .
                        $e->getMessage(),
                    'datetime' => Carbon::now()->format(config('fi.dateFormat') . ' h:i:s'),
                    'type'     => 'error',
                ], 'cron_failed'));
        }

    }

    private function queueUpcomingInvoices()
    {
        $days = config('fi.upcomingPaymentNoticeFrequency');

        if ($days)
        {
            $days = explode(',', $days);

            foreach ($days as $daysFromNow)
            {
                $daysFromNow = trim($daysFromNow);

                if (is_numeric($daysFromNow))
                {
                    $daysFromNow = intval($daysFromNow);

                    $date = Carbon::now()->addDays($daysFromNow)->format('Y-m-d');

                    $invoices = Invoice::with('client')
                        ->where('status', '=', 'sent')
                        ->whereHas('amount', function ($query)
                        {
                            $query->where('balance', '>', '0');
                        })
                        ->where('due_at', $date)
                        ->whereType('invoice')
                        ->get();

                    Log::info('FI::MailQueue - Invoices found due ' . $daysFromNow . ' days from now on ' . $date . ': ' . $invoices->count());

                    foreach ($invoices as $invoice)
                    {
                        $parser = new Parser($invoice);

                        $contacts = new Contacts($invoice->client);

                        $contactTo = $contacts->getSelectedContactsTo();

                        $mail = $this->mailQueue->create($invoice, [
                            'to'             => $contactTo,
                            'cc'             => [config('fi.mailDefaultCc')],
                            'bcc'            => [config('fi.mailDefaultBcc')],
                            'subject'        => $parser->parse('upcomingPaymentNoticeEmailSubject'),
                            'body'           => (config('fi.upcomingPaymentNoticeUseCustomTemplate') == 'custom_mail_template') ? config('fi.upcomingPaymentNoticeCustomMailTemplate') : $parser->parse('upcomingPaymentNoticeEmailBody'),
                            'attach_pdf'     => config('fi.attachPdf'),
                            'attach_invoice' => config('fi.upcomingPaymentNoticeAttachInvoice') ? config('fi.upcomingPaymentNoticeAttachInvoice') : 0,
                        ]);

                        $this->mailQueue->send($mail->id);
                    }
                }
                else
                {
                    Log::info('FI::MailQueue - Upcoming payment due indicator: ' . $daysFromNow);
                }
            }
        }
    }

    private function queueOverdueInvoices()
    {
        $days = config('fi.overdueInvoiceReminderFrequency');

        if ($days)
        {
            $days = explode(',', $days);

            foreach ($days as $daysAgo)
            {
                $daysAgo = trim($daysAgo);

                if (is_numeric($daysAgo))
                {
                    $daysAgo = intval($daysAgo);

                    $date = Carbon::now()->subDays($daysAgo)->format('Y-m-d');

                    $invoices = Invoice::with('client')
                        ->where('status', '=', 'sent')
                        ->whereHas('amount', function ($query)
                        {
                            $query->where('balance', '>', '0');
                        })
                        ->whereHas('client', function ($query)
                        {
                            $query->whereActive(1);
                        })
                        ->where('due_at', $date)
                        ->whereType('invoice')
                        ->get();


                    Log::info('FI::MailQueue - Invoices found due ' . $daysAgo . ' days ago on ' . $date . ': ' . $invoices->count());

                    foreach ($invoices as $invoice)
                    {
                        $parser = new Parser($invoice);

                        $contacts = new Contacts($invoice->client);

                        $contactTo = $contacts->getSelectedContactsTo();

                        $mail = $this->mailQueue->create($invoice, [
                            'to'             => $contactTo,
                            'cc'             => [config('fi.mailDefaultCc')],
                            'bcc'            => [config('fi.mailDefaultBcc')],
                            'subject'        => $parser->parse('overdueInvoiceEmailSubject'),
                            'body'           => $parser->parse('overdueInvoiceEmailBody'),
                            'attach_pdf'     => config('fi.attachPdf'),
                            'attach_invoice' => config('fi.overdueAttachInvoice') ? config('fi.overdueAttachInvoice') : 0,
                        ]);

                        $this->mailQueue->send($mail->id);

                        event(new OverdueNoticeEmailed($invoice, $mail));
                    }
                }
                else
                {
                    Log::info('FI::MailQueue - Invalid overdue indicator: ' . $daysAgo);
                }
            }
        }
    }

    private function recurTasks()
    {
        $tasks  = Task::select('tasks.*')->recurNow()->activeClientTasks()->get();
        $userId = User::whereUserType('system')->first()->id;
        foreach ($tasks as $task)
        {

            $taskData = [
                'user_id'             => $userId,
                'title'               => $task->title,
                'description'         => $task->description,
                'due_date'            => $task->due_date,
                'assignee_id'         => $task->assignee_id,
                'client_id'           => $task->client_id,
                'is_complete'         => $task->is_complete,
                'task_section_id'     => $task->task_section_id,
                'sequence'            => $task->sequence,
                'is_recurring'        => $task->is_recurring,
                'recurring_frequency' => $task->recurring_frequency,
                'recurring_period'    => $task->recurring_period,
                'completion_note'     => $task->completion_note,
            ];

            DB::beginTransaction();

            Task::create($taskData);

            if ($task->due_date == '0000-00-00' or ($task->due_date !== '0000-00-00' and ($task->next_date < $task->due_date)))
            {
                $nextDate = DateFormatter::incrementDate(substr($task->next_date, 0, 10), $task->recurring_period, $task->recurring_frequency);
            }
            else
            {
                $nextDate = '0000-00-00';
            }

            if ($nextDate <= $task->due_date)
            {
                $task->next_date = $nextDate;
                $task->save();
                DB::commit();
            }
            else
            {
                DB::rollback();
            }

        }
        return count($tasks);
    }

    private function recurInvoices()
    {

        $recurringInvoices = RecurringInvoice::recurNow()->activeClient()->get();

        $retStr = '';

        foreach ($recurringInvoices as $recurringInvoice)
        {
            if ($recurringInvoice->id == $recurringInvoice->client_id)
            {
                $newLine = '*** RI ID: ' . $recurringInvoice->id . '  ClientID: ' . $recurringInvoice->client_id;
            }
            else
            {
                $newLine = 'RI ID: ' . $recurringInvoice->id . '  ClientID: ' . $recurringInvoice->client_id;
            }
            $retStr = $retStr . '\n' . $newLine;
        }

        $userId                 = User::whereUserType('system')->first()->id;
        $recurringInvoiceLastId = 0;

        foreach ($recurringInvoices as $recurringInvoice)
        {
            if ($recurringInvoiceLastId !== $recurringInvoice->id)
            {
                $recurringInvoiceLastId = $recurringInvoice->id;
                $tag_ids                = [];
                $invoiceData            = [
                    'company_profile_id'        => $recurringInvoice->company_profile_id,
                    'created_at'                => $recurringInvoice->next_date,
                    'document_number_scheme_id' => $recurringInvoice->document_number_scheme_id,
                    'user_id'                   => $userId,
                    'client_id'                 => $recurringInvoice->client_id,
                    'currency_code'             => $recurringInvoice->currency_code,
                    'template'                  => $recurringInvoice->template,
                    'terms'                     => $recurringInvoice->terms,
                    'footer'                    => $recurringInvoice->footer,
                    'summary'                   => $recurringInvoice->summary,
                    'discount'                  => $recurringInvoice->discount,
                    'recurring_invoice_id'      => $recurringInvoice->id,
                    'status'                    => $recurringInvoice->client->getAutomaticEmailOnRecur() ? 'sent' : 'draft',
                ];

                $invoice = Invoice::create($invoiceData);

                CustomField::copyCustomFieldValues($recurringInvoice, $invoice);

                $tags                 = $recurringInvoice->tags;
                $recurringInvoiceTags = [];
                foreach ($tags as $tag)
                {
                    $recurringInvoiceTags[] = $tag->tag->name;
                }

                if (!empty($recurringInvoiceTags))
                {
                    foreach ($recurringInvoiceTags as $tag)
                    {
                        $tag = Tag::firstOrNew(['name' => $tag, 'tag_entity' => 'sales'])->fill(['name' => $tag, 'tag_entity' => 'sales']);

                        $tag->save();

                        $tag_ids[] = $tag->id;
                    }
                    foreach ($tag_ids as $tag_id)
                    {
                        $invoice->tags()->create(['invoice_id' => $invoice->id, 'tag_id' => $tag_id]);
                    }
                }

                foreach ($recurringInvoice->recurringInvoiceItems as $item)
                {
                    $itemData = [
                        'invoice_id'    => $invoice->id,
                        'name'          => $item->name,
                        'description'   => $item->description,
                        'quantity'      => $item->quantity,
                        'price'         => $item->price,
                        'tax_rate_id'   => $item->tax_rate_id,
                        'tax_rate_2_id' => $item->tax_rate_2_id,
                        'display_order' => $item->display_order,
                    ];

                    $invoiceItem = InvoiceItem::create($itemData);
                    CustomField::copyCustomFieldValues($item, $invoiceItem);

                    if (config('commission_enabled'))
                    {
                        $recurringInvoiceItemCommissions = $item->commissions()->get();

                        foreach ($recurringInvoiceItemCommissions as $recurringInvoiceItemCommission)
                        {
                            if ($recurringInvoiceItemCommission->stop_date > Carbon::now())
                            {
                                InvoiceItemCommission::create([
                                    'invoice_item_id' => $invoiceItem->id,
                                    'user_id'         => $recurringInvoiceItemCommission->user_id,
                                    'type_id'         => $recurringInvoiceItemCommission->type_id,
                                    'note'            => $recurringInvoiceItemCommission->note,
                                    'amount'          => $recurringInvoiceItemCommission->amount,
                                    'status'          => 'new',
                                ]);

                            }
                        }
                    }
                }

                if ($recurringInvoice->stop_date == '0000-00-00' or ($recurringInvoice->stop_date !== '0000-00-00' and ($recurringInvoice->next_date < $recurringInvoice->stop_date)))
                {
                    $nextDate = DateFormatter::incrementDate(substr($recurringInvoice->next_date, 0, 10), $recurringInvoice->recurring_period, $recurringInvoice->recurring_frequency);
                }
                else
                {
                    $nextDate = '0000-00-00';
                }

                $recurringInvoice->next_date = $nextDate;
                $recurringInvoice->save();

                event(new InvoiceCreatedRecurring($invoice));

            }
            else
            {
                Log::info('FI TaskController.recurInvoices() - Attempted to generate duplicate live invoice. RI ID: ' . $recurringInvoiceLastId);
            }
        }

        return count($recurringInvoices);
    }

    public function createLiveInvoice(RecurringInvoiceCreateLiveInvoiceRequest $request)
    {
        try
        {
            $recurringInvoice = RecurringInvoice::find(request('id'));
            $retStr           = '';

            if ($recurringInvoice->id == $recurringInvoice->client_id)
            {
                $newLine = '*** RI ID: ' . $recurringInvoice->id . '  ClientID: ' . $recurringInvoice->client_id;
            }
            else
            {
                $newLine = 'RI ID: ' . $recurringInvoice->id . '  ClientID: ' . $recurringInvoice->client_id;
            }
            $retStr = $retStr . '\n' . $newLine;

            $userId = User::whereUserType('system')->first()->id;

            $xxx = Carbon::now()->format('Y-m-d');

            if ($recurringInvoice->stop_date == '0000-00-00' or ($recurringInvoice->stop_date !== '0000-00-00' and ($recurringInvoice->next_date < $recurringInvoice->stop_date)))
            {
                $invoiceDate = substr($recurringInvoice->next_date, 0, 10);
                $nextDate = DateFormatter::incrementDate(substr($recurringInvoice->next_date, 0, 10), $recurringInvoice->recurring_period, $recurringInvoice->recurring_frequency);
            }
            else
            {
                $invoiceDate = Carbon::now()->format('Y-m-d');
                $nextDate = '0000-00-00';
            }

            $tag_ids     = [];
            $invoiceData = [
                'company_profile_id'        => $recurringInvoice->company_profile_id,
                'created_at'                => $recurringInvoice->next_date,
                'invoice_date'              => $invoiceDate,
                'document_number_scheme_id' => $recurringInvoice->document_number_scheme_id,
                'user_id'                   => $userId,
                'client_id'                 => $recurringInvoice->client_id,
                'currency_code'             => $recurringInvoice->currency_code,
                'template'                  => $recurringInvoice->template,
                'terms'                     => $recurringInvoice->terms,
                'footer'                    => $recurringInvoice->footer,
                'summary'                   => $recurringInvoice->summary,
                'discount'                  => $recurringInvoice->discount,
                'recurring_invoice_id'      => $recurringInvoice->id,
                'status'                    => $recurringInvoice->client->getAutomaticEmailOnRecur() ? 'sent' : 'draft',
            ];

            $invoice = Invoice::create($invoiceData);

            CustomField::copyCustomFieldValues($recurringInvoice, $invoice);

            $tags                 = $recurringInvoice->tags;
            $recurringInvoiceTags = [];
            foreach ($tags as $tag)
            {
                $recurringInvoiceTags[] = $tag->tag->name;
            }

            if (!empty($recurringInvoiceTags))
            {
                foreach ($recurringInvoiceTags as $tag)
                {
                    $tag = Tag::firstOrNew(['name' => $tag, 'tag_entity' => 'sales'])->fill(['name' => $tag, 'tag_entity' => 'sales']);

                    $tag->save();

                    $tag_ids[] = $tag->id;
                }
                foreach ($tag_ids as $tag_id)
                {
                    $invoice->tags()->create(['invoice_id' => $invoice->id, 'tag_id' => $tag_id]);
                }
            }

            foreach ($recurringInvoice->recurringInvoiceItems as $item)
            {
                $itemData = [
                    'invoice_id'    => $invoice->id,
                    'name'          => $item->name,
                    'description'   => $item->description,
                    'quantity'      => $item->quantity,
                    'price'         => $item->price,
                    'tax_rate_id'   => $item->tax_rate_id,
                    'tax_rate_2_id' => $item->tax_rate_2_id,
                    'display_order' => $item->display_order,
                ];

                $invoiceItem = InvoiceItem::create($itemData);
                CustomField::copyCustomFieldValues($item, $invoiceItem);

                if (config('commission_enabled'))
                {
                    $recurringInvoiceItemCommissions = $item->commissions()->get();

                    foreach ($recurringInvoiceItemCommissions as $recurringInvoiceItemCommission)
                    {
                        if ($recurringInvoiceItemCommission->stop_date > Carbon::now())
                        {
                            InvoiceItemCommission::create([
                                'invoice_item_id' => $invoiceItem->id,
                                'user_id'         => $recurringInvoiceItemCommission->user_id,
                                'type_id'         => $recurringInvoiceItemCommission->type_id,
                                'note'            => $recurringInvoiceItemCommission->note,
                                'amount'          => $recurringInvoiceItemCommission->amount,
                                'status'          => 'new',
                            ]);

                        }
                    }
                }
            }

            $recurringInvoice->next_date = $nextDate;
            $recurringInvoice->save();

            event(new InvoiceCreatedRecurring($invoice));

            return response()->json(['success' => true, 'message' => trans('fi.create_live_invoice_successfully', ['number' => $invoice->number])], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.create_live_invoice_unsuccessfully')], 400);
        }
    }

    public function generateTimelineHistory()
    {
        GenerateTimelineHistory::dispatchAfterResponse();
        return response()->json(
            [
                'success' => true,
                'message' => trans('fi.generated_timeline_request_accepted'),
            ], 200);
    }

    private function taskDueNotification()
    {
        GenerateTaskDueNotification::dispatch();
    }

    public function testLayout()
    {
        return view('system_log.test');
    }

}
