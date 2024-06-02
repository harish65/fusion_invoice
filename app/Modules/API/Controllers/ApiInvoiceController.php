<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\API\Controllers;

use Carbon\Carbon;
use Exception;
use FI\Modules\API\Requests\APIApplyCreditMemoRequest;
use FI\Modules\API\Requests\APIInvoiceCustomFieldsRequest;
use FI\Modules\API\Requests\APIInvoiceEmailRequest;
use FI\Modules\API\Requests\APIInvoiceItemRequest;
use FI\Modules\API\Requests\APIInvoiceStoreRequest;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CustomFields\Models\InvoiceCustom;
use FI\Modules\Invoices\Events\InvoiceEmailed;
use FI\Modules\Invoices\Events\InvoiceEmailing;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Modules\MailQueue\Support\MailQueue;
use FI\Modules\Payments\Models\Payment;
use FI\Modules\Payments\Models\PaymentInvoice;
use FI\Support\Contacts;
use FI\Support\FileNames;
use FI\Support\NumberFormatter;
use FI\Support\Parser;
use FI\Support\PDF\PDFFactory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ApiInvoiceController extends ApiController
{
    private $mailQueue;

    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function index(Request $request)
    {
        $fieldsWiseSearch = ($request->except('status', 'paginated_response', 'include_custom_fields') != null) ? $request->except('status', 'paginated_response', 'include_custom_fields') : null;

        try
        {
            if ($request->has('paginated_response') && $request->get('paginated_response') == 1)
            {
                $invoices = Invoice::select('invoices.*')
                                   ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
                                   ->with(['items.amount', 'client', 'amount', 'currency'])
                                   ->customField($request->get('include_custom_fields'))
                                   ->status(request('status'))
                                   ->fieldsWiseSearch($fieldsWiseSearch)
                                   ->sortable(['invoice_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
                                   ->paginate(config('fi.resultsPerPage'));
            }
            else
            {
                $invoices = Invoice::select('invoices.*')
                                   ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
                                   ->with(['items.amount', 'client', 'amount', 'currency'])
                                   ->customField($request->get('include_custom_fields'))
                                   ->status(request('status'))
                                   ->fieldsWiseSearch($fieldsWiseSearch)
                                   ->sortable(['invoice_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
                                   ->get();
            }

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_retrieved'), 'data' => $invoices], 200);

        }
        catch (QueryException $e)
        {
            if ($e->getCode() == '42S22')
            {
                return response()->json(['success' => false, 'message' => trans('fi.invalid_field_name')], 400);
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
            }
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
        }

    }

    public function show($id, Request $request)
    {
        try
        {

            $invoice = Invoice::with(['items.amount', 'client', 'amount', 'currency'])->customField($request->get('include_custom_fields'))->find($id);

            if ($invoice)
            {
                return response()->json(['success' => true, 'message' => trans('fi.record_successfully_retrieved'), 'data' => $invoice], 200);
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
            }

        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
        }

    }

    public function store(APIInvoiceStoreRequest $request)
    {
        $input = $request->all();

        $input['user_id'] = auth()->user()->id;

        if (isset($input['client_name']))
        {
            $client = Client::findByName($input['client_name']);

            if (!$client)
            {
                return response()->json(['errors' => [[trans('fi.client_not_found')]]], 422);
            }
            $input['client_id'] = $client->id;

            unset($input['client_name']);
        }

        try
        {
            $invoice = Invoice::create($input);
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_created'), 'data' => $invoice], 201);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

    }

    public function addItem(APIInvoiceItemRequest $request)
    {
        try
        {
            InvoiceItem::create($request->all());
            $invoice = Invoice::with(['items.amount', 'client', 'amount', 'currency'])->find($request->get('invoice_id'));
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_created'), 'data' => $invoice], 201);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

    }

    public function addUpdateCustomFields(APIInvoiceCustomFieldsRequest $request)
    {
        try
        {
            $input = $request->except('invoice_id');
            InvoiceCustom::updateOrCreate(['invoice_id' => request('invoice_id')], $input);

            $invoice = Invoice::select('invoices.*')
                              ->with(['custom', 'items.amount', 'client', 'amount', 'currency'])->first(request('invoice_id'));
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_created'), 'data' => $invoice], 201);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

    }

    public function delete($id)
    {
        try
        {

            if (Invoice::destroy($id) == true)
            {
                return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
            }

        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

    }

    public function sendMail(APIInvoiceEmailRequest $request)
    {

        $invoice = Invoice::find($request->input('id'));

        $contacts = new Contacts($invoice->client);

        $parser               = new Parser($invoice);
        $overdueAttachInvoice = 0;

        if (!$invoice->is_overdue)
        {
            $subject = $parser->parse('invoiceEmailSubject');
            $body    = (config('fi.invoiceUseCustomTemplate') == 'custom_mail_template') ? $parser->parse('invoiceCustomMailTemplate') : $parser->parse('invoiceEmailBody');
        }
        else
        {
            $overdueAttachInvoice = config('fi.overdueAttachInvoice') ? config('fi.overdueAttachInvoice') : 0;
            $subject              = $parser->parse('overdueInvoiceEmailSubject');
            $body                 = (config('fi.overdueInvoiceUseCustomTemplate') == 'custom_mail_template') ? $parser->parse('overdueInvoiceCustomMailTemplate') : $parser->parse('overdueInvoiceEmailBody');
        }

        event(new InvoiceEmailing($invoice));

        $input = $request->except('id');

        $input = [
            'to'             => isset($input['to']) && !empty($input['to']) ? is_array($input['to']) ? $input['to'] : [$input['to']] : $contacts->getSelectedContactsTo()->toArray(),
            'cc'             => isset($input['cc']) && !empty($input['cc']) ? is_array($input['cc']) ? $input['cc'] : [$input['cc']] : $contacts->getSelectedContactsCc(),
            'bcc'            => isset($input['bcc']) && !empty($input['bcc']) ? is_array($input['bcc']) ? $input['bcc'] : [$input['bcc']] : $contacts->getSelectedContactsBcc(),
            'subject'        => isset($input['subject']) && !empty($input['subject']) ? $input['subject'] : $subject,
            'body'           => $body,
            'attach_pdf'     => $request->input('attach_pdf', 0),
            'attach_invoice' => $overdueAttachInvoice,
        ];

        $mail = $this->mailQueue->create($invoice, $input);

        if ($this->mailQueue->send($mail->id))
        {
            event(new InvoiceEmailed($invoice));
            return response()->json(['success' => true, 'message' => trans('fi.invoice_email_success')], 200);
        }
        else
        {
            return response()->json(['success' => false, 'message' => $this->mailQueue->getError()], 400);
        }
    }

    public function pdf($id)
    {
        try
        {
            $invoice         = Invoice::find($id);
            $invoice->status = 'sent';
            $invoice->save();

            $pdf = PDFFactory::create();

            $html = darkModeForInvoiceAndQuoteTemplate($invoice->html);

            return $pdf->download($html, FileNames::invoice($invoice));

        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function applyCreditMemo(APIApplyCreditMemoRequest $request)
    {
        try
        {
            $invoiceId    = request('invoice_id');
            $creditMemoId = request('credit_memo_id');
            $amount       = request('amount');

            $invoice    = Invoice::whereId($invoiceId)->first();
            $creditMemo = Invoice::find($creditMemoId);

            $payment = [
                'client_id'         => $invoice->client->id,
                'payment_method_id' => null,
                'paid_at'           => Carbon::now()->toDateString(),
                'amount'            => NumberFormatter::unformat($amount),
                'currency_code'     => $creditMemo->currency_code,
                'remaining_balance' => (abs($creditMemo->amount->balance) - $amount),
                'credit_memo_id'    => $creditMemoId,
                'user_id'           => auth()->user()->id,
                'type'              => 'credit-memo',
            ];
            $payment = Payment::create($payment);

            $paymentInvoice                        = [];
            $paymentInvoice['payment_id']          = $payment->id;
            $paymentInvoice['invoice_id']          = $invoice->id;
            $paymentInvoice['invoice_amount_paid'] = NumberFormatter::unformat($amount);
            $paymentInvoice['convenience_charges'] = 0;

            PaymentInvoice::create($paymentInvoice);

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_updated')], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => [$e->getMessage()]], 422);
        }
    }
}
