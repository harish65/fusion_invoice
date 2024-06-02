<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ mb_strtoupper(trans('fi.payment_receipt')) }}</title>

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
            padding: 5px 10px;
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

    </style>
</head>
<body>

<table>
    <tr>
        <td style="width: 50%;" valign="top">
            <h1>{{ mb_strtoupper(trans('fi.payment_receipt')) }}</h1>
        </td>
        <td style="width: 50%; text-align: right;" valign="top">
            <table>
                <tr>
                    <td style="width: 50%; text-align: right;" valign="top">
                        {!! $companyProfile->logo() !!}
                    </td>
                    <td style="width: 50%; text-align: right;" valign="top">
                        {{ $companyProfile->company }}<br>
                        {!! $companyProfile->formatted_address !!}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">{{ $payment->formatted_paid_at }}</td>
    </tr>
</table>
<hr style="border: 2px solid; margin-bottom: 50px">
<table style="width: 50%;">
    <tr>
        <td style="text-align: left;line-height: 23px;"><span class="info">{{ mb_strtoupper(trans('fi.customer')) }}</span> #{{ $payment->client->title != '' ? $payment->client->title.' '.$payment->client->name : $payment->client->name }}
            <br>
            @if ($payment->client->address) {!! $payment->client->formatted_address !!}@endif
            <br>
            <span class="info">{{ mb_strtoupper(trans('fi.email')) }} -</span>  {{ $payment->client->email }} <br>
            <span class="info">{{ mb_strtoupper(trans('fi.phone')) }} -</span>  {{ $payment->client->phone }} <br>
        </td>
    </tr>
</table>
<p style="margin-top: 50px; margin-bottom: 25px;">{{ $paymentText }}</p>
<table class="alternate">
    <thead>
    <tr>
        <th style="text-align: left;"><strong>{{ mb_strtoupper(trans('fi.invoice')) }}</strong></th>
        <th style="text-align: left;"><strong>{{ mb_strtoupper(trans('fi.summary')) }}</strong></th>
        <th style="text-align: left;"><strong>{{ mb_strtoupper(trans('fi.date')) }}</strong></th>
        <th style="text-align: left;"><strong>{{ mb_strtoupper(trans('fi.due')) }}</strong></th>
        <th style="text-align: center;"><strong>{{ mb_strtoupper(trans('fi.invoice_amount')) }}</strong></th>
        <th style="text-align: center;"><strong>{{ mb_strtoupper(trans('fi.paid')) }}</strong></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($payment->paymentInvoice as $paymentInvoice)
        <tr>
            <td style="text-align: left;"><a href="{{$paymentInvoice->invoice->public_url}}" style="color: #3c3cff;"> {{ $paymentInvoice->invoice->number }} </a></td>
            <td style="text-align: left;">{{ $paymentInvoice->invoice->summary }}</td>
            <td style="text-align: left;">{{ $paymentInvoice->invoice->formatted_invoice_date }}</td>
            <td style="text-align: left; @if ($paymentInvoice->invoice->isOverdue) color: #ff0000; font-weight: bold; @endif">
                {{ $paymentInvoice->invoice->formatted_due_at }}
            </td>
            <td style="text-align: center;">{{ $paymentInvoice->invoice->amount->formatted_total }}</td>
            <td style="text-align: center;">{{ $paymentInvoice->invoice->amount->formatted_paid }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>