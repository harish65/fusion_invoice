<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @php $invoice_type_title = ($invoice->type == 'credit_memo') ? trans('fi.credit_memo') : trans('fi.invoice'); @endphp
    <title>{{ $invoice_type_title }} #{{ $invoice->number }}</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            color: #001028;
            background: #FFFFFF;
            font-family: DejaVu Sans, Helvetica, sans-serif;
            font-size: 12px;
            margin-left: 0px;
            margin-right: 0px;
        }

        a {
            color: #5D6975;
            border-bottom: 1px solid currentColor;
            text-decoration: none;
        }

        h1 {
            color: #5D6975;
            font-size: 2.8em;
            line-height: 1.4em;
            font-weight: bold;
            margin: 0;
        }

        table {
            width: 100%;
            border-spacing: 0;
            margin-bottom: 20px;
            padding: 0 2px;
        }

        th, .section-header {
            padding: 16px 10px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
            text-align: center;
        }

        @media only screen and (max-width: 600px) {
            th, .section-header {
                padding: 5px 4px;
            }
        }

        @media only screen and (max-width: 320px) {
            table {
                width: 100%;
                border-spacing: 0;
                margin-bottom: 20px;
                font-size: 10px;
                margin-left: 0px;
            }
        }

        td {
            padding: 10px 5px;
        }

        table.alternate tr:nth-child(odd) td {
            background: #F5F5F5;
        }

        table.customfield-table tr td {
            background: inherit !important;
        }

        th.amount, td.amount {
            text-align: right;
        }

        .info {
            color: #5D6975;
            font-weight: bold;
        }

        .terms {
            padding: 10px;
        }

        .footer {
            text-align: center;
            padding: 10px;
        }

        #cp-logo {
            max-width: 114px;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        {{isset($darkModeForInvoiceAndQuoteTemplate) ? $darkModeForInvoiceAndQuoteTemplate : null}}

    </style>
</head>
<body class="body-{{ $navClass }}">

<table class="body-{{ $navClass }}">
    <tr>
        <td style="width: {{ config('fi.qrCodeOnInvoiceQuote') == 1 ? 33 : 50 }}%;" valign="top">
            <h1>{{ mb_strtoupper($invoice_type_title) }}</h1>
            <span class="info">{{ mb_strtoupper($invoice_type_title) }} #</span>{{ $invoice->number }}<br>
            <span class="info">{{ mb_strtoupper(trans('fi.issued')) }}</span> {{ $invoice->formatted_created_at }}<br>
            <span class="info">{{ mb_strtoupper(trans('fi.due_date')) }}</span> {{ $invoice->formatted_due_at }}<br><br>
            <span class="info">{{ mb_strtoupper(trans('fi.bill_to')) }}</span><br>
            {{ $invoice->client->title != '' ? $invoice->client->title.' '.$invoice->client->name : $invoice->client->name }}
            <br>
            @if ($invoice->client->address) {!! $invoice->client->formatted_address !!}<br>@endif
        </td>
        @if(config('fi.qrCodeOnInvoiceQuote') == 1)
            <td style="width: 33%;" valign="top" align="center">
                <img alt="QR-Code" width=""
                     src="data:image/png;base64,{!! DNS2D::getBarcodePNG(route('clientCenter.public.invoice.show', [$invoice->url_key]),"QRCODE") !!}"
                     class="img-responsive">
            </td>
        @endif
        <td style="width: {{ config('fi.qrCodeOnInvoiceQuote') == 1 ? 33 : 50 }}%; text-align: right;" valign="top"></td>
        <td>
            {{ $invoice->companyProfile->company }}<br>

            {!! $invoice->companyProfile->formatted_address !!}<br>
            @if ($invoice->companyProfile->phone) {{ $invoice->companyProfile->phone }}<br>@endif
            @if (isset($invoice->user->fromEmail)) <a
                    href="mailto:{{ $invoice->user->fromEmail }}">{{ $invoice->user->fromEmail }}</a>@endif
        </td>
        <td>
            {!! $invoice->companyProfile->logo() !!}<br>
        </td>
    </tr>
</table>

<table class="alternate  table-{{ $navClass }}">
    <thead>
    <tr>
        <th style="text-align: left;"><strong>{{ mb_strtoupper(trans('fi.product')) }}</strong></th>
        <th style="text-align: left;"><strong>{{ mb_strtoupper(trans('fi.description')) }}</strong></th>
        <th class="amount"><strong>{{ mb_strtoupper(trans('fi.quantity')) }}</strong></th>
        <th class="amount"><strong>{{ mb_strtoupper(trans('fi.price')) }}</strong></th>
        @if ($hasLineItemDiscount)
            <th class="amount"><strong>{{ mb_strtoupper(trans('fi.discount')) }}</strong></th>
        @else
            <th class="amount"></th>
        @endif
        <th class="amount"><strong>{{ mb_strtoupper(trans('fi.total')) }}</strong></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($invoice->items as $item)
        <tr>
            <td style="text-align: left;" valign="top">{!! $item->name !!}</td>
            <td>
                {!! $item->formatted_description !!}
                @if(count($invoiceItemCustomFields))
                    <table class="customfield-table" style="padding: 5px 0 0 0;">
                        @foreach(array_chunk($invoiceItemCustomFields, 3) as $chunkInvoiceItemCustomField)
                            <tr>
                                @foreach($chunkInvoiceItemCustomField as $invoiceItemCustomField)
                                    <td style="padding: 10px 0;">
                                        <strong>{{ ucfirst($invoiceItemCustomField->field_label) }} :</strong>
                                        <span style="margin-left: 4px;">{!! ($item->custom->{$invoiceItemCustomField->column_name}) ?? "-" !!}</span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                @endif
            </td>
            <td class="amount">{{ $item->formatted_quantity }}</td>
            <td class="amount">{{ $item->formatted_price}}  {{ $item->getAlternateCurrency($invoice->customField('alt_currency') , $item->price ,null) }}</td>
            @if ($hasLineItemDiscount)
                <td class="amount">{{ $item->formatted_discount }} {{ $item->getAlternateCurrency($invoice->customField('alt_currency'),$item->discount ,'discount_type') }}</td>
            @else
                <td class="amount"></td>
            @endif
            <td class="amount">{{ $item->amount->formatted_subtotal }} {{ $item->getAlternateCurrency($invoice->customField('alt_currency') , $item->amount->subtotal ,null) }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="5" class="amount"><strong>{{ mb_strtoupper(trans('fi.subtotal')) }}</strong></td>
        <td class="amount">{{ $invoice->amount->formatted_subtotal }} {{ $item->getAlternateCurrency($invoice->customField('alt_currency') , $invoice->amount->subtotal ,null) }}</td>
    </tr>

    @if ($invoice->discount > 0)
        <tr>
            <td colspan="5" class="amount">{{ mb_strtoupper(trans('fi.discount')) }}</td>
            <td class="amount">{{ $invoice->amount->formatted_discount }} {{ $item->getAlternateCurrency($invoice->customField('alt_currency'),$invoice->amount->discount ,null) }}</td>
        </tr>
    @endif

    @foreach ($invoice->summarized_taxes as $tax)
        <tr>
            <td colspan="5" class="amount">{{ mb_strtoupper($tax->name) }} ({{ $tax->percent }})</td>
            <td class="amount">{{ $tax->total }} {{ $item->getAlternateCurrency($invoice->customField('alt_currency'),$tax->unformated_total , null) }}</td>
        </tr>
    @endforeach
    @if($invoice->online_payment_processing_fee == 'yes' && $invoice->total_convenience_charges != 0)
        <tr>
            <td colspan="5" class="amount"><strong>{{ mb_strtoupper(config('fi.feeName')) }}
                    ({{ config('fi.feePercentage') }}%)</strong>
            </td>
            <td class="amount">{{ $invoice->formatted_total_convenience_charges }}  {{ $item->getAlternateCurrency($invoice->customField('alt_currency'),$invoice->total_convenience_charges , null) }}</td>
        </tr>
    @endif
    <tr>
        <td colspan="5" class="amount"><strong>{{ mb_strtoupper(trans('fi.total')) }}</strong></td>
        <td class="amount">{{ $invoice->amount->formatted_total }} {{ $item->getAlternateCurrency($invoice->customField('alt_currency'),$invoice->amount->total , null) }}</td>
    </tr>
    <tr>
        <td colspan="5" class="amount">
            <strong>{{ ($invoice->type=='credit_memo') ? mb_strtoupper(trans('fi.applied')) : mb_strtoupper(trans('fi.paid')) }}</strong>
        </td>
        <td class="amount">{{ $invoice->amount->formatted_paid }}  {{ $item->getAlternateCurrency($invoice->customField('alt_currency'),$invoice->amount->paid , null) }}</td>
    </tr>
    <tr>
        <td colspan="5" class="amount"><strong>{{ mb_strtoupper(trans('fi.balance')) }}</strong></td>
        <td class="amount">{{ $invoice->amount->formatted_balance }} {{ $item->getAlternateCurrency($invoice->customField('alt_currency'),$invoice->amount->balance , null) }}</td>
    </tr>
    </tbody>
</table>

<table class="body-{{ $navClass }}">
    <tbody>

    @if ($invoice->terms || $invoice->online_payment_processing_fee == 'yes')
        <tr>
            <td>
                <div class="section-header">{{ mb_strtoupper(trans('fi.terms_and_conditions')) }}</div>
            </td>
        </tr>
        <tr>
            <td>
                @if($invoice->online_payment_processing_fee == 'yes')
                    <div class="terms  text-justify" style="text-align: end;">
                        {!! config('fi.feeExplanation') !!}<span class="text-danger"> *</span>
                    </div>
                @endif
                @if ($invoice->terms)
                    <div class="terms">{!! $invoice->formatted_terms !!}</div>
                @endif
            </td>
        </tr>
    @endif
    <tr>
        <td colspan="2">
            <div class="footer">{!! $invoice->formatted_footer !!}</div>
        </td>
    </tr>
    </tbody>
</table>

</body>
</html>
