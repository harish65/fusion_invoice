<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Controllers;

use Carbon\Carbon;
use Exception;
use FI\Http\Controllers\Controller;
use FI\Modules\Invoices\Events\AddTransition;
use FI\Modules\Invoices\Events\InvoiceEmailed;
use FI\Modules\Invoices\Events\InvoiceEmailing;
use FI\Modules\Invoices\Events\OverdueNoticeEmailed;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\MailQueue\Support\MailQueue;
use FI\Requests\SendEmailRequest;
use FI\Support\Contacts;
use FI\Support\Parser;

class InvoiceMailController extends Controller
{
    private $mailQueue;

    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function create()
    {
        $invoice = Invoice::find(request('invoice_id'));

        $contacts = new Contacts($invoice->client);

        $parser = new Parser($invoice);

        if ($invoice->type == 'credit_memo')
        {
            $subject = $parser->parse('creditMemoEmailSubject');
            $body    = (config('fi.creditMemoUseCustomTemplate') == 'custom_mail_template') ? $parser->parse('creditMemoCustomMailTemplate') : $parser->parse('creditMemoEmailBody');
        }
        else
        {
            if (!$invoice->is_overdue)
            {
                $subject = $parser->parse('invoiceEmailSubject');
                $body    = (config('fi.invoiceUseCustomTemplate') == 'custom_mail_template') ? $parser->parse('invoiceCustomMailTemplate') : $parser->parse('invoiceEmailBody');
            }
            else
            {
                $subject = $parser->parse('overdueInvoiceEmailSubject');
                $body    = (config('fi.overdueInvoiceUseCustomTemplate') == 'custom_mail_template') ? $parser->parse('overdueInvoiceCustomMailTemplate') : $parser->parse('overdueInvoiceEmailBody');
            }
        }

        if (strtolower($invoice->user->name) === 'system')
        {
            $fromMail = [
                config('fi.mailFromName') . '###' . config('fi.mailFromAddress') => config('fi.mailFromAddress'),
                auth()->user()->name . '###' . auth()->user()->email             => auth()->user()->email,
            ];
        }
        else
        {
            $fromMail = [
                config('fi.mailFromName') . '###' . config('fi.mailFromAddress') => config('fi.mailFromAddress'),
                $invoice->user->name . '###' . $invoice->user->email             => $invoice->user->email,
                auth()->user()->name . '###' . auth()->user()->email             => auth()->user()->email,
            ];
        }
        if (config('fi.secure_link') == 1)
        {
            $body .= '</br></br><hr><div style="text-align: center; "><span style="font-size: smaller;">' . trans('fi.invoice_link_expire', ['days' => config('fi.secure_link_expire_day')]) . '</span><span style="color: red;">*</span></div>';
        }

        return view('invoices._modal_mail')
            ->with('invoice', $invoice)
            ->with('redirectTo', urlencode(request('redirectTo')))
            ->with('subject', $subject)
            ->with('body', $body)
            ->with('contactDropdownTo', $contacts->contactDropdownTo())
            ->with('contactDropdownCc', $contacts->contactDropdownCc())
            ->with('contactDropdownBcc', $contacts->contactDropdownBcc())
            ->with('fromMail', $fromMail);
    }

    public function store(SendEmailRequest $request)
    {
        if (!config('app.demo'))
        {
            $currentDate           = Carbon::now()->format('Y-m-d');
            $invoice               = Invoice::find($request->input('invoice_id'));
            $invoice->date_emailed = $currentDate;

            if ($invoice->status == 'draft')
            {
                $invoice->status       = 'sent';
                if (config('fi.resetInvoiceDateEmailDraft') == 1)
                {
                    $diffDays              = Carbon::createFromDate(Carbon::createFromDate($invoice->invoice_date)->format('Y-m-d'))->subDays(1)->diffInDays($currentDate);
                    $invoice->invoice_date = $currentDate;
                    $invoice->due_at       = Carbon::createFromDate(Carbon::createFromDate($invoice->due_at)->format('Y-m-d'))->subDays(1)->addDays($diffDays)->format('Y-m-d');
                }
            }

            $invoice->save();

            $input = $request->except('invoice_id');

            event(new InvoiceEmailing($invoice));

            $mail = $this->mailQueue->create($invoice, $input);

            if ($this->mailQueue->send($mail->id))
            {
                event(new InvoiceEmailed($invoice));
                event(new AddTransition($invoice, 'email_sent'));
            }
            else
            {
                return response()->json(['errors' => [[$this->mailQueue->getError()]]], 400);
            }
        }
        else
        {
            return response()->json(['errors' => [[trans('fi.functionality_not_available_on_demo')]]], 400);
        }
    }

    public function sendPaymentDueReminder($id)
    {
        try
        {
            $invoice  = Invoice::find($id);
            $parser   = new Parser($invoice);
            $contacts = new Contacts($invoice->client);

            $contactTo = $contacts->getSelectedContactsTo();

            $mail = $this->mailQueue->create($invoice, [
                'to'         => $contactTo,
                'cc'         => [config('fi.mailDefaultCc')],
                'bcc'        => [config('fi.mailDefaultBcc')],
                'subject'    => $parser->parse('overdueInvoiceEmailSubject'),
                'body'       => (config('fi.overdueInvoiceUseCustomTemplate') == 'custom_mail_template') ? $parser->parse('overdueInvoiceCustomMailTemplate') : $parser->parse('overdueInvoiceEmailBody'),
                'attach_pdf' => config('fi.invoiceAttachPDF'),
            ]);

            $this->mailQueue->send($mail->id);

            event(new OverdueNoticeEmailed($invoice, $mail));
            return response()->json(['success' => true, 'message' => trans('fi.reminder_sent_successfully')], 200);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.error_sending_reminder')], 400);
        }
    }

    public function sendPaymentUpcomingNotice($id)
    {
        try
        {
            $invoice  = Invoice::find($id);
            $parser   = new Parser($invoice);
            $contacts = new Contacts($invoice->client);

            $contactTo = $contacts->getSelectedContactsTo();

            $mail = $this->mailQueue->create($invoice, [
                'to'         => $contactTo,
                'cc'         => [config('fi.mailDefaultCc')],
                'bcc'        => [config('fi.mailDefaultBcc')],
                'subject'    => $parser->parse('upcomingPaymentNoticeEmailSubject'),
                'body'       => (config('fi.upcomingPaymentNoticeUseCustomTemplate') == 'custom_mail_template') ? $parser->parse('upcomingPaymentNoticeCustomMailTemplate') : $parser->parse('upcomingPaymentNoticeEmailBody'),
                'attach_pdf' => config('fi.invoiceAttachPDF'),
            ]);

            $this->mailQueue->send($mail->id);

            event(new OverdueNoticeEmailed($invoice, $mail));

            return response()->json(['success' => true, 'message' => trans('fi.reminder_sent_successfully')], 200);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.error_sending_reminder')], 400);
        }
    }
}
