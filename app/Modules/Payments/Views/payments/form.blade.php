@extends('layouts.master')

@section('javascript')

    @include('payments._js_form')
    @include('layouts._select2')
@stop

@section('content')

    @if(Gate::check('payments.create') || Gate::check('payments.update'))
        @if ($editMode == true)
            {!! Form::model($payment, ['route' => ['payments.update', $payment->id]]) !!}
        @else
            {!! Form::open(['route' => 'payments.store']) !!}
        @endif

        {!! Form::hidden('invoice_id') !!}

        <section class="content-header">
            <h1 class="pull-left">
                {{ trans('fi.payment_form') }}
            </h1>

            <span class="label label-success"
                  style="vertical-align: sub; margin-left: 10px; font-size: 95%;">{{ $payment->invoice->client->name }}</span>

            <div class="pull-right">
                @can('payments.view')
                    <a href="{{ str_contains(URL::previous(), 'clients') ? route('clients.show', [$payment->invoice->client->id]) : route('payments.index') }}"
                       class="btn btn-default">Cancel</a>
                @endcan
                {!! Form::submit(trans('fi.save'), ['class' => 'btn btn-sm btn-primary']) !!}
            </div>
            <div class="clearfix"></div>
        </section>

        <section class="content">

            @include('layouts._alerts')

            <div class="row">

                <div class="col-md-12">

                    <div class="card card-primary card-outline">

                        <div class="card-body">

                            <div class="form-group">
                                <label>{{ trans('fi.amount') }}: </label>
                                {!! Form::text('amount', $payment->formatted_numeric_amount, ['id' => 'amount',
                                'class' => 'form-control form-control-sm']) !!}
                            </div>

                            <div class="input-group date">
                                <label>{{ trans('fi.payment_date') }}: </label>
                                <div class="input-group date" id="paid_at" data-target-input="nearest">

                                    {!! Form::text('paid_at', $payment->formatted_paid_at, ['class' => 'custom-form-field form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off' ,'data-target' => "#paid_at"]) !!}
                                    <div class="input-group-append"
                                         data-target="#paid_at" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>

                                </div>
                            </div>


                            <div class="form-group">
                                <label>{{ trans('fi.payment_method') }}</label>
                                {!! Form::select('payment_method_id', $paymentMethods, null, ['id' =>
                                'payment_method_id', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.note') }}</label>
                                {!! Form::textarea('note', null, ['id' => 'note', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                            @if ($customFields)
                                @include('custom_fields._custom_fields_unbound', ['object' => isset($payment) ? $payment : []])
                            @endif

                            {!! Form::hidden('referer', URL::previous()) !!}

                        </div>

                    </div>

                </div>

            </div>

        </section>

        {!! Form::close() !!}
    @endif

    <section class="content">
        @include('notes._js_timeline', ['object' => $payment, 'model' => 'FI\Modules\Payments\Models\Payment', 'hideHeader' => true, 'showPrivateCheckbox' => 0])
        <div id="note-timeline-container"></div>
    </section>
@stop