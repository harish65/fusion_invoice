<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Quotes\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Quotes\Events\AddTransition;
use FI\Modules\MailQueue\Support\MailQueue;
use FI\Modules\Quotes\Events\QuoteEmailed;
use FI\Modules\Quotes\Events\QuoteEmailing;
use FI\Modules\Quotes\Models\Quote;
use FI\Requests\SendEmailRequest;
use FI\Support\Contacts;
use FI\Support\Parser;

class QuoteMailController extends Controller
{
    private $mailQueue;

    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function create()
    {
        $quote = Quote::find(request('quote_id'));

        $contacts = new Contacts($quote->client);

        $parser = new Parser($quote);

        $fromMail = [
            $quote->user->name . '###' . $quote->user->email                 => $quote->user->email,
            config('fi.mailFromName') . '###' . config('fi.mailFromAddress') => config('fi.mailFromAddress'),
            auth()->user()->name . '###' . auth()->user()->email             => auth()->user()->email,
        ];
        $body     = (config('fi.quoteUseCustomTemplate') == 'custom_mail_template') ? $parser->parse('quoteCustomMailTemplate') : $parser->parse('quoteEmailBody');
        if (config('fi.secure_link') == 1)
        {
            $body .= '</br></br><hr><div style="text-align: center; "><span style="font-size: smaller;">' . trans('fi.quote_link_expire', ['days' => config('fi.secure_link_expire_day')]) . '</span><span style="color: red;">*</span></div>';
        }

        return view('quotes._modal_mail')
            ->with('quoteId', $quote->id)
            ->with('redirectTo', urlencode(request('redirectTo')))
            ->with('subject', $parser->parse('quoteEmailSubject'))
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
            $quote = Quote::find($request->input('quote_id'));

            $contacts = new Contacts($quote->client);

            $contactTo = $contacts->getAllContacts();

            if (count($request->get('to')) > 1)
            {
                $body = trans('fi.default_greeting') . $request->get('body');
            }
            else
            {
                if (isset($contactTo[$request->get('to')[0]]))
                {
                    $contactName = explode(' <', $contactTo[$request->get('to')[0]]);
                    $body        = (config('fi.quoteUseCustomTemplate') == 'custom_mail_template') ? $request->get('body') : trans('fi.hi') . current($contactName) . '<br>' . $request->get('body');
                }
                else
                {
                    $body = (config('fi.quoteUseCustomTemplate') == 'custom_mail_template') ? $request->get('body') : trans('fi.hi') . '<br>' . $request->get('body');
                }

            }

            $input = $request->except('quote_id');

            $input['body'] = $body;

            event(new QuoteEmailing($quote));

            $mail = $this->mailQueue->create($quote, $input);

            if ($this->mailQueue->send($mail->id))
            {
                event(new QuoteEmailed($quote));
                event(new AddTransition($quote, 'email_sent'));
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
}
