@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function () {

            $('#btn-delete-client').click(function () {

                $('#modal-placeholder').load('{!! route('clients.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'clients',
                        isReload: true,
                        returnURL: '{{route('clients.index')}}'
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );

            });

            $('#btn-add-contact').click(function () {
                var $clientId = '{{$client->id}}';
                var $createContactUrl = '{{ route("clients.contacts.create", ":client_id") }}';
                $createContactUrl = $createContactUrl.replace(':client_id', $clientId);
                $('#modal-placeholder').load($createContactUrl);
            });

            @if (!empty($client->important_note) && !str_contains(URL::previous(), 'edit'))
            $('.important-note-modal').removeClass('d-none').addClass('d-block');
            $('#important-note-modal').modal();
            @endif

            $('.important-note-modal-close').click(function () {
                $('.important-note-modal').removeClass('d-block').addClass('d-none');
            });

            @if (str_contains(URL::previous(), 'payments'))
            $('[href="#tab-payments"]').click();
            @endif

            $('.editable-tab').click(function () {
                $('#client-edit-btn').attr('href', $(this).data('edit-link'));
            });

            let selectedTab = '#{{ $selectedTab }}' + '-tab';
            $(selectedTab).trigger('click');

            $('.create-task').click(function () {
                $('#modal-placeholder').load($(this).data('action'));
            });
            $('#client-create-note').click(function () {
                $('#note-modal-placeholder').load('{{ route('notes.create') }}');
            });
            $("#currency_code").change(function () {
                var url = '{{ route("clients.invoiceSummary", ["id" => ":id", "currency_code" => ":currency_code"]) }}';
                url = url.replace(':id', {{$client->id}});
                url = url.replace(':currency_code', $(this).val());
                $('#currency-summary').load(url);
            })
            $('#client-field-settings').click(function () {
                $('#modal-placeholder').load('{!! route('client.get.filterColumns') !!}');
            });
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                    @can('clients.update')
                        <a href="javascript:void(0);" class="btn btn-sm btn-light float-sm-right mr-4"
                           id="client-field-settings">
                            <i class="fa fa-sliders-h" data-toggle="tooltip" data-placement="auto"
                               title="{!! trans('fi.tt_client_field_settings') !!}">
                            </i>
                        </a>
                    @endcan
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-6">

                    <h1 class="d-inline">{!! $client->name !!}</h1>

                    <div class="row" style="margin-top:3px;">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-12 p-0">
                                        <span class="badge {{ isset($typeLabels[$client->type]) ? $typeLabels[$client->type] : '' }}">
                                            {{ trans('fi.' . $client->type) }}
                                        </span>
                                        <span class="badge badge-info">
                                           {{ trans('fi.local_time') }} : {{ $client->local_time }}
                                        </span>
                                        @if(!$client->active)
                                            <span class="badge badge-danger text-uppercase">{{ trans('fi.inactive') }}</span>
                                        @endif
                                        @if($client->allow_child_accounts != 0)
                                            <span class="badge badge-parent-account">
                                           {{ trans('fi.parent_account') }}
                                            </span>
                                        @endif
                                        @if($client->third_party_bill_payer != 0)
                                            <span class="badge badge-third-party-bill-payer">
                                            {{ trans('fi.third_party_bill_payer') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6 p-0">
                                    <div class="col-md-12 ">
                                        @if($client->parent_name)
                                            <span class="m-md-0">
                                            <i class="fa fa-link" aria-hidden="true"> </i> <span>{{ trans('fi.parent_account') }}:</span>
                                            <a href="{{ route('clients.show', [$client->parent_client_id]) }}"><span>{!! $client->parent_name !!}</span></a>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        @if($client->invoices_paid_by_name)
                                            <span class="m-md-0">
                                            <i class="fa fa-credit-card" aria-hidden="true"> </i> <span>{{ trans('fi.invoices_paid_by') }}:</span>
                                            <a href="{{ route('clients.show', [$client->invoices_paid_by]) }}"><span>{!! $client->invoices_paid_by_name !!}</span></a>
                                        </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>
                <div class="col-sm-6 text-right" style="padding-top:7px;">
                    @can('notes.create')
                        <a href="javascript:void(0)"
                           class="btn btn-sm btn-action-modal btn-default {{(!$client->active) ? 'disabled' : null }}"
                           id="client-create-note">
                            <i class="fa fa-comments"></i> {{ trans('fi.add_note') }}
                        </a>
                    @endcan
                    <a href="javascript:void(0)"
                       class="btn btn-sm btn-default btn-action-modal create-task {{(!$client->active) ? 'disabled' : null }}"
                       data-action="{{ route('task.widget.create', ['client' => $client->id,'tab' => 'tasks']) }}">
                        <i class="fa fa-list"></i> {{ trans('fi.create_task') }}
                    </a>
                    @if($invoicePaymentSummary != null)
                        <a href="{{route('reports.clientStatement')}}?client={{$client->id}}"
                           class="btn btn-sm btn-default" target="_blank" id="view-client-statement">
                            <i class="nav-icon far fa-file-alt pr-2"></i>{{ trans('fi.statement') }}
                        </a>
                    @endif
                    @can('clients.update')
                        <a id="client-edit-btn" href="{{ route('clients.edit', [$client->id]) }}"
                           class="btn btn-sm btn-default mr-2">
                            <i class="fa fa-edit pr-2 pl-2"></i>{{ trans('fi.edit') }}</a>
                    @endcan
                    @can('clients.delete')
                        <a class="btn btn-sm btn-danger mr-2" href="#"
                           data-action="{{ route('clients.delete', [$client->id]) }}"
                           id="btn-delete-client"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>
                    @endcan
                </div>

                <div class="offset-md-6 col-md-6">
                    @if(count(array_keys($invoicePaymentSummary)) > 1)
                        <div class="row">
                            <div class="offset-md-3 col-md-5 text-right">
                                <label>{{ trans('fi.currency') }}</label>
                            </div>
                            <div class="offset-md-1 col-md-3">
                                {!! Form::select('currency', array_combine(array_keys($invoicePaymentSummary), array_keys($invoicePaymentSummary)), $client->currency_code, ['class' => 'form-control pull-right form-control-sm', 'id' => 'currency_code']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 no-padding text-right" id="currency-summary">
                                @include('clients.summary', ['invoicePaymentSummary' => $invoicePaymentSummary, 'currency' => $client->currency_code])
                            </div>
                        </div>
                    @else
                        <div class="col-md-12 no-padding text-right" id="currency-summary">
                            @include('clients.summary', ['invoicePaymentSummary' => $invoicePaymentSummary, 'currency' => $client->currency_code])
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">

            @if(!$client->active)
                <div class="client-inactive-watermark">{{ trans('fi.inactive') }}</div>
            @endif

            @include('layouts._alerts')

            <div class="row">

                <div class="col-12">

                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"
                                    data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'general']) }}">
                                    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#tab-details">
                                        {{ trans('fi.details') }}
                                    </a>
                                </li>
                                @can('quotes.view')
                                    <li class="nav-item"
                                        data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'general']) }}">
                                        <a class="nav-link" data-toggle="tab" href="#tab-quotes">
                                            {{ trans('fi.quotes') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('invoices.view')
                                    <li class="nav-item"
                                        data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'general']) }}">
                                        <a class="nav-link" data-toggle="tab" href="#tab-invoices">
                                            {{ trans('fi.invoices') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('recurring_invoices.view')
                                    <li class="nav-item"
                                        data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'general']) }}">
                                        <a class="nav-link" data-toggle="tab" href="#tab-recurring-invoices">
                                            {{ trans('fi.recurring_invoices') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('payments.view')
                                    <li class="nav-item"
                                        data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'general']) }}">
                                        <a class="nav-link" data-toggle="tab" href="#tab-payments">
                                            {{ trans('fi.payments') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('contacts.view')
                                    <li class="nav-item"
                                        data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'contacts']) }}">
                                        <a class="nav-link" id="contacts-tab" data-toggle="tab" href="#tab-contacts">
                                            {{ trans('fi.contacts') }} {!! $client->contacts->count() > 0 ? '<span class="badge badge-primary contacts-tab-count">'.$client->contacts->count().'</span>' : '' !!}
                                        </a>
                                    </li>
                                @endcan
                                @can('attachments.view')
                                    <li class="nav-item"
                                        data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'attachments']) }}">
                                        <a class="nav-link" id="attachments-tab" data-toggle="tab"
                                           href="#tab-attachments">
                                            {{ trans('fi.attachments') }} {!! $client->attachments->count() > 0 ? '<span class="badge badge-primary attachment-count">'.$client->attachments->count().'</span>' : '' !!}
                                        </a>
                                    </li>
                                @endcan
                                @can('notes.view')
                                    <li class="nav-item"
                                        data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'notes']) }}">
                                        <a class="nav-link" id="notes-tab" data-toggle="tab" href="#tab-notes">
                                            {{ trans('fi.notes') }} {!! $client->notes->count() > 0 ? '<span class="badge badge-primary notes-count">'.$client->notes->count().'</span>' : '<span class="badge badge-primary notes-count"></span>' !!}
                                        </a>
                                    </li>
                                @endcan
                                <li class="nav-item"
                                    data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'tasks']) }}">
                                    <a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tab-tasks">
                                        {{ trans('fi.tasks') }} <span
                                                class="badge badge-primary {!! $client->tasks->count() <= 0 ? 'hide' : '' !!}">{!! $client->tasks->count() <= 0 ? '' :  $client->tasks->count()  !!}</span>
                                    </a>
                                </li>
                                @if(isset($containerAddonStatus->enabled) && $containerAddonStatus->enabled == 1)
                                    <li class="nav-item"
                                        data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'containers']) }}">
                                        <a class="nav-link" id="containers-tab" data-toggle="tab"
                                           href="#tab-containers">
                                            {{ trans('Containers::lang.containers') }} <span
                                                    class="badge badge-primary {!! $client->containers->count() <= 0 ? 'hide' : '' !!}">{{ $client->containers->count() }}</span>
                                        </a>
                                    </li>
                                @endcan

                                @if($relatedAccounts['count'] > 0)
                                    <li class="nav-item"
                                        data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'related']) }}">
                                        <a class="nav-link" id="related-tab" data-toggle="tab" href="#related-accounts">
                                            {{ trans('fi.related_accounts') }}
                                            <span class="badge badge-primary {!! ($relatedAccounts['count'] <= 0) ? 'hide' : '' !!}"
                                                  id="child-count">{!! ($relatedAccounts['count'] <= 0) ? '' : $relatedAccounts['count'] !!}
                                            </span>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item"
                                    data-edit-link="{{ route('clients.edit', [$client->id, 'tab' => 'settings']) }}">
                                    <a class="nav-link" id="settings-tab" data-toggle="tab" href="#tab-settings">
                                        {{ trans('fi.settings') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div id="tab-details" class="tab-pane active">

                                    <div class="row">

                                        <div class="col-md-12">

                                            <table class="table table-sm table-striped table-responsive-sm table-responsive-xs">
                                                <tr>
                                                    <td class="col-md-2"><label>{{ trans('fi.name') }}</label></td>
                                                    <td class="col-md-10">{!! $client->name !!}</td>
                                                </tr>
                                                @if(config('fi.clientColumnSettingsVatTaxId') == 1)
                                                    <tr>
                                                        <td class="col-md-2"><label>{{ trans('fi.vat_tax_id') }}</label>
                                                        </td>
                                                        <td class="col-md-10">{!! $client->vat_tax_id !!}</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td class="col-md-2"><label>{{ trans('fi.address') }}</label></td>
                                                    <td class="col-md-10">{!! $client->formatted_address !!}</td>
                                                </tr>

                                                @if($client->city != null || $client->state != null || $client->zip != null || $client->country != null)
                                                    <tr>
                                                        <td class="col-md-1"></td>
                                                        <td class="col-md-2">
                                                            @if($client->city != null)
                                                                {!! $client->city !!} &nbsp;
                                                            @endif
                                                            @if($client->state != null)
                                                                {!! $client->state !!} &nbsp;&nbsp;
                                                            @endif
                                                            @if($client->zip != null)
                                                                {!! $client->zip !!} &nbsp;&nbsp;
                                                            @endif
                                                            @if($client->country != null)
                                                                {!! $client->country !!}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif

                                                <tr>
                                                    <td class="col-md-2"><label>{{ trans('fi.email') }}</label></td>
                                                    <td class="col-md-10"><a
                                                                href="mailto:{!! $client->email !!}">{!! $client->email !!}</a>
                                                    </td>
                                                </tr>
                                                @if(config('fi.clientColumnSettingsPhoneNumber') == 1)
                                                    <tr>
                                                        <td class="col-md-2"><label>{{ trans('fi.phone') }}</label></td>
                                                        <td class="col-md-10">{!! $client->phone !!}</td>
                                                    </tr>
                                                @endif

                                                @if(config('fi.clientColumnSettingsMobileNumber') == 1)
                                                    <tr>
                                                        <td class="col-md-2"><label>{{ trans('fi.mobile') }}</label>
                                                        </td>
                                                        <td class="col-md-10">{!! $client->mobile !!}</td>
                                                    </tr>
                                                @endif
                                                @if(config('fi.clientColumnSettingsFaxNumber') == 1)
                                                    <tr>
                                                        <td class="col-md-2"><label>{{ trans('fi.fax') }}</label></td>
                                                        <td class="col-md-10">{!! $client->fax !!}</td>
                                                    </tr>
                                                @endif
                                                @if(config('fi.clientColumnSettingsWebAddress') == 1)
                                                    <tr>
                                                        <td class="col-md-2"><label>{{ trans('fi.web') }}</label></td>
                                                        <td class="col-md-10">
                                                            <a href="{!! $client->formatted_web_address !!}"
                                                               target="_blank">{!! $client->web !!}</a>
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if(config('fi.clientColumnSettingsSocialMediaUrl') == 1)
                                                    <tr>
                                                        <td class="col-md-2">
                                                            <label>{{ trans('fi.social_media_url') }}</label>
                                                        </td>
                                                        <td class="col-md-10">
                                                            <a href="{!! $client->formatted_social_media_url !!}"
                                                               target="_blank">{!! $client->social_media_url !!}</a>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td class="col-md-2">
                                                        <label data-toggle="tooltip" data-placement="auto"
                                                               title="{!! trans('fi.tt_client_tags') !!}">{{ trans('fi.tags') }}</label>
                                                    </td>
                                                    <td class="col-md-10">
                                                        @foreach ($client->tags as $tagDetail)
                                                            <span class="badge badge-primary">{{ $tagDetail->tag->name }}</span>
                                                        @endforeach
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="col-md-2">
                                                        <span>
                                                            <label class="text-danger" data-toggle="tooltip"
                                                                   data-placement="auto"
                                                                   title="{!! trans('fi.tt_important_note') !!}">{{ trans('fi.important-note') }}:</label>
                                                        </span>
                                                    </td>
                                                    <td class="col-md-10">{!! $client->formattedImportantNote !!}</td>
                                                </tr>
                                                @if(config('fi.clientColumnSettingsLeadSource') == 1)

                                                    <tr>
                                                        <td class="col-md-2">
                                                            <label data-toggle="tooltip" data-placement="auto"
                                                                   title="{!! trans('fi.tt_client_lead_source_tags') !!}">{{ trans('fi.lead_source') }}</label>
                                                        </td>
                                                        <td class="col-md-2">
                                                            @if($client->lead_source_tag_id != null && $client->lead_source_tag_id != 0)
                                                                <span class="badge badge-primary">{{ $client->clientLeadSource->name}}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if(config('fi.clientColumnSettingsLeadSourceNotes') == 1)

                                                    <tr>
                                                        <td class="col-md-2">
                                                            <label>{{ trans('fi.lead_source_notes') }}:</label>
                                                        </td>
                                                        <td class="col-md-10">{{ $client->lead_source_notes }}</td>
                                                    </tr>
                                                @endif
                                                @if(config('fi.clientColumnSettingsGeneralNotes') == 1)
                                                    <tr>
                                                        <td class="col-md-2">
                                                            <label>{{ trans('fi.general_notes') }}:</label>
                                                        </td>
                                                        <td class="col-md-10">{!! $client->formattedGeneralNotes !!}</td>
                                                    </tr>
                                                @endif

                                            </table>
                                            @if ($customFields)
                                                @include('custom_fields._custom_fields_view_unbound', ['object' => isset($client) ? $client : []])
                                            @endif
                                        </div>

                                    </div>

                                </div>

                                @can('quotes.view')
                                    <div id="tab-quotes" class="tab-pane">
                                        <div class="card">
                                            <div class="card-header">
                                                @can('quotes.create')
                                                    <div class="card-tools">
                                                        <a href="javascript:void(0)"
                                                           class="btn btn-sm btn-primary border-0 btn-action-modal create-quote {{(!$client->active) ? 'disabled' : null }}"
                                                           data-client-id="{{ $client->id }}"
                                                           data-client-name="{{ $client->name}}">
                                                            <i class="fa fa-file-alt"></i> {{ trans('fi.create_quote') }}
                                                        </a>
                                                    </div>
                                                @endcan
                                            </div>
                                            <div class="card-body">
                                                @include('quotes._js_index')
                                                @include('quotes._table',['client_view' => 1])
                                            </div>
                                            @can('quotes.view')
                                                <div class="card-footer">
                                                    <p class="text-center">
                                                        <strong>
                                                            <a href="{{ route('quotes.index') }}?client={{ $client->id }}">{{ trans('fi.view_all') }}</a>
                                                        </strong>
                                                    </p>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>
                                @endcan

                                @can('invoices.view')
                                    <div id="tab-invoices" class="tab-pane">
                                        <div class="card">
                                            <div class="card-header">
                                                @can('invoices.create')
                                                    <div class="card-tools">
                                                        <a href="javascript:void(0)"
                                                           class="btn btn-sm btn-primary border-0 btn-action-modal create-invoice {{(!$client->active) ? 'disabled' : null }}"
                                                           data-client-id="{{ $client->id }}"
                                                           data-client-name="{{ $client->name}}">
                                                            <i class="fa fa-file-invoice"></i> {{ trans('fi.create_invoice') }}
                                                        </a>
                                                    </div>
                                                @endcan
                                            </div>
                                            <div class="card-body">
                                                @include('invoices._js_index')
                                                @include('invoices._table',['client_view' => 1])
                                            </div>
                                            <div class="card-footer">
                                                <p class="text-center">
                                                    <strong>
                                                        <a href="{{ route('invoices.index') }}?client={{ $client->id }}">{{ trans('fi.view_all') }}</a>
                                                    </strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endcan

                                @can('recurring_invoices.view')
                                    <div id="tab-recurring-invoices" class="tab-pane">
                                        <div class="card">
                                            <div class="card-header">
                                                @can('recurring_invoices.create')
                                                    <div class="card-tools">
                                                        <a href="javascript:void(0)"
                                                           class="btn btn-sm btn-primary border-0 btn-action-modal create-recurring-invoice {{(!$client->active) ? 'disabled' : null }}"
                                                           data-client-id="{{ $client->id }}"
                                                           data-client-name="{{ $client->name}}">
                                                            <i class="fa fa-sync"></i> {{ trans('fi.create_recurring_invoice') }}
                                                        </a>
                                                    </div>
                                                @endcan
                                            </div>
                                            <div class="card-body">
                                                @include('recurring_invoices._js_index')
                                                @include('recurring_invoices._table',['client_view' => 1])
                                            </div>
                                            <div class="card-footer">
                                                <p class="text-center">
                                                    <strong>
                                                        <a href="{{ route('recurringInvoices.index') }}?client={{ $client->id }}">{{ trans('fi.view_all') }}</a>
                                                    </strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endcan

                                @can('payments.view')
                                    <div id="tab-payments" class="tab-pane">
                                        <div class="card">
                                            <div class="card-body table-responsive ">
                                                @include('payments._js_index')
                                                @include('payments._table',['client_view' => 1])
                                                @can('payments.view')
                                            </div>
                                            <div class="card-footer">
                                                <p class="text-center">
                                                    <strong><a href="{{ route('payments.index') }}?client={{ $client->id }}">{{ trans('fi.view_all') }}</a></strong>
                                                </p>
                                            </div>
                                            @endcan
                                        </div>
                                    </div>
                                @endcan

                                @can('contacts.view')
                                    <div id="tab-contacts" class="tab-pane">
                                        <div class="card">
                                            <div class="card-header">
                                                @can('contacts.create')
                                                    <div class="card-tools">
                                                        <a href="javascript:void(0)"
                                                           class="btn btn-sm border-0 btn-primary {{(!$client->active) ? 'disabled' : null }}"
                                                           id="btn-add-contact">
                                                            <i class="fa fa-plus"></i> {{ trans('fi.add_contact') }}
                                                        </a>
                                                    </div>
                                                @endcan
                                            </div>
                                            <div class="card-body table-responsive ">
                                                @include('clients._table_contacts')
                                            </div>
                                        </div>
                                    </div>
                                @endcan

                                @can('attachments.view')
                                    <div class="tab-pane " id="tab-attachments">
                                        @include('attachments._table', ['object' => $client, 'model' => 'FI\Modules\Clients\Models\Client', 'modelId' => $client->id])
                                    </div>
                                @endcan

                                @can('notes.view')
                                    <div id="tab-notes" class="tab-pane">
                                        @include('notes._js_timeline', ['object' => $client, 'model' => 'FI\Modules\Clients\Models\Client', 'hideHeader' => true, 'showPrivateCheckbox' => 0, 'showPrivate' => 1])
                                        <div id="note-timeline-container"></div>
                                    </div>
                                @endcan

                                <div id="tab-tasks" class="tab-pane">
                                    <div class="card">
                                        <div class="card-body table-responsive ">
                                            @include('tasks._js_index')
                                            @include('tasks._table', ['client_view' => true])
                                        </div>
                                    </div>
                                </div>

                                @if(isset($containerAddonStatus->enabled) && $containerAddonStatus->enabled == 1)
                                    @can('containers.view')
                                        <div id="tab-containers" class="tab-pane">
                                            <div class="card">
                                                @include('containers._js_index')
                                                @include('containers._table', ['object' => $client])
                                            </div>
                                        </div>
                                    @endcan
                                @endif
                                @if($relatedAccounts['count'] > 0)
                                    <div id="related-accounts" class="tab-pane">
                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline card-outline-tabs">
                                                <div class="card-header p-0 border-bottom-0">
                                                    <ul class="nav nav-tabs" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link {{$relatedAccounts['active'] == 'childClient' ? 'active' : '' }}"
                                                               id="tab-childs-tab"
                                                               data-toggle="pill" href="#tab-childs"
                                                               role="tab" aria-controls="tab-childs"
                                                               aria-selected="false">{{ trans('fi.child_account') }}
                                                                <span class="badge badge-primary {!! count($childClients) <= 0 ? 'hide' : '' !!}"
                                                                      id="child-count">{!! (count($childClients) <= 0) ? '' : count($childClients) !!}</span></a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link {{$relatedAccounts['active'] == 'thirdBP' ? 'active' : '' }}"
                                                               id="child-third-party"
                                                               data-toggle="pill" href="#third-party-pill-payer"
                                                               role="tab" aria-controls="third-party-pill-payer"
                                                               aria-selected="false"> {!! trans('fi.third_party_bill_payer') !!}
                                                                <span class="badge badge-primary {!! count($thirdPartyBillPayers) <= 0 ? 'hide' : '' !!}"
                                                                      id="child-count">{!! (count($thirdPartyBillPayers) <= 0) ? '' : count($thirdPartyBillPayers) !!}</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <div class="card-body">
                                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                                        <div class="tab-pane fade  {{$relatedAccounts['active'] == 'childClient' ? 'show active' : '' }}"
                                                             id="tab-childs"
                                                             role="tabpanel" aria-labelledby="tab-childs-tab">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    @if(count($childClients) > 0)
                                                                        @foreach($childClients as $childClient)
                                                                            <a href="{{ route('clients.show', [$childClient->id]) }}">
                                                                                @if($childClient->active == 1)
                                                                                    <span class="badge badge-primary">{!! $childClient->client_name !!}</span>
                                                                                @else
                                                                                    <span class="badge badge-primary"
                                                                                          title="{{ trans('fi.inactive') }}"><del>{!! $childClient->client_name !!}</del></span>
                                                                                @endif
                                                                            </a>
                                                                        @endforeach
                                                                    @else
                                                                        {!! trans('fi.not_found_related_clients') !!}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade {{$relatedAccounts['active'] == 'thirdBP' ? 'show active' : '' }}"
                                                             id="third-party-pill-payer"
                                                             role="tabpanel" aria-labelledby="child-third-party">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    @if(count($thirdPartyBillPayers) > 0)
                                                                        @foreach($thirdPartyBillPayers as $thirdPartyBillPayer)
                                                                            <a href="{{ route('clients.show', [$thirdPartyBillPayer->id]) }}">
                                                                                @if($thirdPartyBillPayer->active == 1)
                                                                                    <span class="badge badge-primary">{!! $thirdPartyBillPayer->client_name !!}</span>
                                                                                @else
                                                                                    <span class="badge badge-primary"
                                                                                          title="{{ trans('fi.inactive') }}"><del>{!! $thirdPartyBillPayer->client_name !!}</del></span>
                                                                                @endif
                                                                            </a>
                                                                        @endforeach
                                                                    @else
                                                                        {!! trans('fi.invoices_paid_for_list') !!}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div id="tab-settings" class="tab-pane">

                                    <div class="row mt-1">

                                        <div class="col-md-12">

                                            @if($client->parent_name || $client->invoices_paid_by_name)
                                                <table class="table table-sm table-striped table-responsive-sm table-responsive-xs">

                                                    @if($client->parent_name && $client->invoices_paid_by_name)
                                                        <tr>
                                                            <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                                data-placement="auto"
                                                                title="{!! trans('fi.tt_parent_account') !!}">
                                                                {{ trans('fi.parent_account') }}</td>
                                                            <td class="col-md-3"><a
                                                                        href="{{ route('clients.show', [$client->parent_client_id]) }}">{!! $client->parent_name !!}</a>
                                                            </td>
                                                            <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                                data-placement="auto"
                                                                title="{!! trans('fi.tt_invoices_paid_by') !!}">
                                                                {{ trans('fi.invoices_paid_by') }}</td>
                                                            <td class="col-md-3">
                                                                <a href="{{ route('clients.show', [$client->invoices_paid_by]) }}"><span>{!! $client->invoices_paid_by_name !!}</span></a>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        @if($client->parent_name)
                                                            <tr>
                                                                <td class="col-md-3 view-field-label"
                                                                    data-toggle="tooltip" data-placement="auto"
                                                                    title="{!! trans('fi.tt_parent_account') !!}">
                                                                    {{ trans('fi.parent_account') }}</td>
                                                                <td class="col-md-9">
                                                                    <a href="{{ route('clients.show', [$client->parent_client_id]) }}">{!! $client->parent_name !!}</a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if($client->invoices_paid_by_name)
                                                            <tr>
                                                                <td class="col-md-3 view-field-label"
                                                                    data-toggle="tooltip" data-placement="auto"
                                                                    title="{!! trans('fi.tt_invoices_paid_by') !!}">
                                                                    {{ trans('fi.invoices_paid_by') }}</td>
                                                                <td class="col-md-9">
                                                                    <a href="{{ route('clients.show', [$client->invoices_paid_by]) }}"><span>{!! $client->invoices_paid_by_name !!}</span></a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif

                                                </table>
                                                <br>
                                            @endif

                                            <table class="table table-sm table-striped table-responsive-sm table-responsive-xs">
                                                <tr>
                                                    <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                        data-placement="auto"
                                                        title="{!! trans('fi.tt_active') !!}">
                                                        {{ trans('fi.active') }}</td>
                                                    <td class="col-md-9">
                                                        @if($client->active == 1)
                                                            {{trans('fi.yes')}}
                                                        @else
                                                            <span class="badge badge-danger text-uppercase">{{trans('fi.inactive')}}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if(config('fi.clientColumnSettingsInvoicePrefix') == 1)
                                                    <tr>
                                                        <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                            data-placement="auto"
                                                            title="{!! trans('fi.tt_invoice_prefix') !!}">
                                                            {{ trans('fi.invoice_prefix') }}</td>
                                                        <td class="col-md-9">{!! $client->invoice_prefix !!}</td>
                                                    </tr>
                                                @endif
                                                @if(config('fi.clientColumnSettingsAutomaticEmailPaymentReceipt') == 1)

                                                    <tr>
                                                        <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                            data-placement="auto"
                                                            title="{!! trans('fi.tt_automatic_email_payment_receipts') !!}">
                                                            {{ trans('fi.automatic_email_payment_receipts') }}</td>
                                                        <td class="col-md-9">{!! trans('fi.'.$client->automatic_email_payment_receipt) !!}</td>
                                                    </tr>
                                                @endif
                                                @if(config('fi.clientColumnSettingsAutomaticEmailOnRecurringInvoice') == 1)
                                                    <tr>
                                                        <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                            data-placement="auto"
                                                            title="{!! trans('fi.tt_automatic_email_on_recur') !!}">
                                                            {{ trans('fi.automatic_email_on_recur') }}</td>
                                                        <td class="col-md-9">{!! trans('fi.'.$client->automatic_email_on_recur) !!}</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                        data-placement="auto"
                                                        title="{!! trans('fi.tt_allow_client_center_login') !!}">
                                                        {{ trans('fi.allow_client_center_login') }}</td>
                                                    <td class="col-md-9">
                                                        @if($client->user != null)
                                                            {{trans('fi.yes')}}
                                                        @else
                                                            {{trans('fi.no')}}
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if(config('fi.clientColumnSettingsDefaultCurrency') == 1)

                                                    <tr>
                                                        <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                            data-placement="auto"
                                                            title="{!! trans('fi.tt_default_currency') !!}">
                                                            {{ trans('fi.default_currency') }}</td>
                                                        <td class="col-md-9">{!! $client->currency_code ?? config('fi.baseCurrency') !!}</td>
                                                    </tr>
                                                @endif
                                                @if(config('fi.clientColumnSettingsLanguage') == 1)

                                                    <tr>
                                                        <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                            data-placement="auto"
                                                            title="{!! trans('fi.tt_language') !!}">
                                                            {{ trans('fi.language') }}</td>
                                                        <td class="col-md-9">{!! $client->language ?? config('fi.language') !!}</td>
                                                    </tr>
                                                @endif
                                                @if(config('fi.clientColumnSettingsTimezone') == 1)
                                                    <tr>
                                                        <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                            data-placement="auto"
                                                            title="{!! trans('fi.tt_timezone') !!}">
                                                            {{ trans('fi.timezone') }}</td>
                                                        <td class="col-md-9">{!! $client->timezone !!}</td>
                                                    </tr>
                                                @endif
                                                @if(config('fi.clientColumnSettingsOnlinePaymentProcessingFee') == 1)
                                                    <tr>
                                                        <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                            data-placement="auto"
                                                            title="{!! trans('fi.tt_allow_online_payment_processing_fees') !!}">
                                                            {{ trans('fi.allow_online_payment_processing_fees') }}</td>
                                                        <td class="col-md-9">{!!trans('fi.'.$client->online_payment_processing_fee)!!}</td>
                                                    </tr>
                                                @endif

                                                @if(config('fi.clientColumnSettingsAllowChildAccounts') == 1)
                                                    <tr>
                                                        <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                            data-placement="auto"
                                                            title="{!! trans('fi.tt_allow_child_accounts') !!}">
                                                            {{ trans('fi.allow_child_accounts') }}
                                                        </td>
                                                        <td class="col-md-9">{!! ($client->allow_child_accounts == 1) ? trans('fi.yes') : trans('fi.no')  !!}</td>
                                                    </tr>
                                                @endif

                                                @if(config('fi.clientColumnSettingsThirdPartyBillPayer') == 1)
                                                    <tr>
                                                        <td class="col-md-3 view-field-label" data-toggle="tooltip"
                                                            data-placement="auto"
                                                            title="{!! trans('fi.tt_third_party_bill_payer') !!}">
                                                            {{ trans('fi.third_party_bill_payer') }}</td>
                                                        <td class="col-md-9">{!! ($client->third_party_bill_payer == 1) ? trans('fi.yes') : trans('fi.no')  !!}</td>
                                                    </tr>
                                                @endif

                                            </table>

                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>

                </div>

            </div>

            @include('transitions.client_timeline', ['clientId'=> $client->id, 'filterUsers' => $filterUsers, 'modules' => $modules])

        </div>

        <div class="modal fade important-note-modal d-none" id="important-note-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h4 class="modal-title">{!! trans('fi.important') !!}</h4>
                        <button type="button" class="close important-note-modal-close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {!! $client->jsFormattedImportantNote !!}
                    </div>
                    <div class="modal-footer ">
                        <button type="button" class="btn btn-default btn-sm important-note-modal-close"
                                data-dismiss="modal">
                            {{trans('fi.close')}}</button>
                    </div>
                </div>

            </div>

        </div>
    </section>

@stop
