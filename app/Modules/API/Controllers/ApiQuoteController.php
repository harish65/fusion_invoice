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

use Exception;
use FI\Modules\API\Requests\APIQuotesCustomFieldsRequest;
use FI\Modules\API\Requests\APIQuoteItemRequest;
use FI\Modules\API\Requests\APIQuoteEmailRequest;
use FI\Modules\API\Requests\APIQuoteStoreRequest;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CustomFields\Models\QuoteCustom;
use FI\Modules\MailQueue\Support\MailQueue;
use FI\Modules\Quotes\Events\QuoteEmailed;
use FI\Modules\Quotes\Events\QuoteEmailing;
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\Quotes\Models\QuoteItem;
use FI\Support\Contacts;
use FI\Support\FileNames;
use FI\Support\Parser;
use FI\Support\PDF\PDFFactory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ApiQuoteController extends ApiController
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
                $quotes = Quote::select('quotes.*')
                               ->join('quote_amounts', 'quote_amounts.quote_id', '=', 'quotes.id')
                               ->with(['items.amount', 'client', 'amount', 'currency'])
                               ->customField($request->get('include_custom_fields'))
                               ->status(request('status'))
                               ->fieldsWiseSearch($fieldsWiseSearch)
                               ->sortable(['quote_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
                               ->paginate(config('fi.resultsPerPage'));
            }
            else
            {
                $quotes = Quote::select('quotes.*')
                               ->join('quote_amounts', 'quote_amounts.quote_id', '=', 'quotes.id')
                               ->with(['items.amount', 'client', 'amount', 'currency'])
                               ->customField($request->get('include_custom_fields'))
                               ->status(request('status'))
                               ->fieldsWiseSearch($fieldsWiseSearch)
                               ->sortable(['quote_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])->get();
            }

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_retrieved'), 'data' => $quotes], 200);

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

            $client = Quote::with(['items.amount', 'client', 'amount', 'currency'])->customField($request->get('include_custom_fields'))->find($id);

            if ($client)
            {
                return response()->json(['success' => true, 'message' => trans('fi.record_successfully_retrieved'), 'data' => $client], 200);
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

    public function store(APIQuoteStoreRequest $request)
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
            $quote = Quote::create($input);
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_created'), 'data' => $quote], 201);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

    }

    public function addItem(APIQuoteItemRequest $request)
    {
        try
        {
            QuoteItem::create($request->all());
            $quote = Quote::with(['items.amount', 'client', 'amount', 'currency'])->find($request->get('quote_id'));
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_created'), 'data' => $quote], 201);
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

            if (Quote::destroy($id) == true)
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

    public function addUpdateCustomFields(APIQuotesCustomFieldsRequest $request)
    {
        try
        {
            $input = $request->except('quote_id');

            QuoteCustom::updateOrCreate(['quote_id' => request('quote_id')], $input)->toSql();

            $client = Quote::select('quotes.*')->with(['items.amount', 'client', 'amount', 'currency'])->first(request('quote_id'));

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_created'), 'data' => $client], 201);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

    }

    public function sendMail(APIQuoteEmailRequest $request)
    {

        $quote = Quote::find($request->input('id'));

        $contacts = new Contacts($quote->client);

        $parser = new Parser($quote);

        event(new QuoteEmailing($quote));

        $input = $request->except('id');

        $input = [
            'to'         => isset($input['to']) && !empty($input['to']) ? is_array($input['to']) ? $input['to'] : [$input['to']] : $contacts->getSelectedContactsTo()->toArray(),
            'cc'         => isset($input['cc']) && !empty($input['cc']) ? is_array($input['cc']) ? $input['cc'] : [$input['cc']] : $contacts->getSelectedContactsCc(),
            'bcc'        => isset($input['bcc']) && !empty($input['bcc']) ? is_array($input['bcc']) ? $input['bcc'] : [$input['bcc']] : $contacts->getSelectedContactsBcc(),
            'subject'    => isset($input['subject']) && !empty($input['subject']) ? $input['subject'] : $parser->parse('quoteEmailSubject'),
            'body'       => (config('fi.quoteUseCustomTemplate') == 'custom_mail_template') ? $parser->parse('quoteCustomMailTemplate') : $parser->parse('quoteEmailBody'),
            'attach_pdf' => $request->input('attach_pdf', 0),
        ];

        $mail = $this->mailQueue->create($quote, $input);

        if ($this->mailQueue->send($mail->id))
        {
            event(new QuoteEmailed($quote));
            return response()->json(['success' => true, 'message' => trans('fi.quote_email_success')], 200);
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
            $quote = Quote::find($id);

            $pdf = PDFFactory::create();

            $html = darkModeForInvoiceAndQuoteTemplate($quote->html);

            return $pdf->download($html, FileNames::quote($quote));

        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

}