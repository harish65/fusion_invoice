@extends('layouts.master')

@section('javascript')

    <script type="text/javascript">
        $(function () {
            $('.nav-item').click(function () {
                $('#client-edit-form').attr('action', $(this).data('save-link'));
            });

            let selectedTab = '#{{ $selectedTab }}' + '-tab';
            $(selectedTab).trigger('click');
        });
    </script>
    <script>
        $(function () {
            @if ($editMode)
            $("form").one('submit', function myFormSubmitCallback(evt) {
                evt.stopPropagation();
                evt.preventDefault();
                var formAllData = $(this).serializeFormJSON();
                var customFields = {};
                var missingValues = {};
                var selectCustomRadioButtonValue = null;
                $('#custom-body-table').find('.custom-file-input,.custom-form-field,.form-check-input').each(function () {
                    var fieldName = $(this).data('clients-field-name');
                    var inputType = $(this).attr('type') || this.tagName.toLowerCase();
                    if (fieldName !== undefined) {
                        if ('file' === inputType) {
                            customFields[fieldName] = typeof this.files[0] === 'undefined' ? '' : this.files[0];
                            return true;
                        }
                        if ('select' === inputType) {
                            if ($(this).find('option:selected').length == 0) {
                                customFields[fieldName] = '';
                                return true;
                            }
                        }
                        if ('checkbox' === inputType) {
                            customFields[fieldName] = ($(this).is(":checked")) ? 1 : 0;
                            return true;
                        }
                        if ('radio' === inputType) {
                            if ($(this).prop('checked') == true) {
                                customFields[fieldName] = $(this).val();
                                selectCustomRadioButtonValue = $(this).val();
                            }
                            if ($(this).prop('checked') == false && selectCustomRadioButtonValue == null) {
                                customFields[fieldName] = 'null';
                            }
                            return customFields[fieldName];
                        }
                        customFields[$(this).data('clients-field-name')] = $(this).val();
                    }
                });
                $.each(customFields, function (customKey, valueCustom) {
                    $.each(formAllData, function (formKey, formValue) {
                        var filter_column = formKey.substring(0, formKey.lastIndexOf("]") + 1);
                        var result = filter_column.startsWith("custom", 0);
                        if (result == true) {
                            if ('custom[' + customKey + ']' != filter_column) {
                                return missingValues['custom[' + customKey + ']'] = valueCustom;
                            }
                        } else {
                            return missingValues['custom[' + customKey + ']'] = valueCustom;
                        }
                    });
                });
                $.each(missingValues, function (key, value) {
                    var input = $("<input>").attr("type", "hidden").attr("class", "missingValue").attr("name", key).val(value);
                    $("form").append($(input));
                });
                $(this).submit();
            });
            @else
            $('.data-submit').click(function (evt) {
                evt.stopPropagation();
                evt.preventDefault();
                var formAllData = $('form').serializeFormJSON();
                formAllData.address = (formAllData.address).replace(/(\r\n|\r|\n){2}/g, '$1').replace(/(\r\n|\r|\n){3,}/g, '$1\n');
                var customFields = {};
                var missingValues = {};
                var selectCustomRadioButtonValue = null;
                $('#custom-body-table').find('.custom-file-input,.custom-form-field,.form-check-input').each(function () {
                    var fieldName = $(this).data('clients-field-name');
                    var inputType = $(this).attr('type') || this.tagName.toLowerCase();
                    if (fieldName !== undefined) {
                        if ('file' === inputType) {
                            customFields[fieldName] = typeof this.files[0] === 'undefined' ? '' : this.files[0];
                            return true;
                        }
                        if ('select' === inputType) {
                            if ($(this).find('option:selected').length == 0) {
                                customFields[fieldName] = '';
                                return true;
                            }
                        }
                        if ('checkbox' === inputType) {
                            customFields[fieldName] = ($(this).is(":checked")) ? 1 : 0;
                            return true;
                        }
                        if ('radio' === inputType) {
                            if ($(this).prop('checked') == true) {
                                customFields[fieldName] = $(this).val();
                                selectCustomRadioButtonValue = $(this).val();
                            }
                            if ($(this).prop('checked') == false && selectCustomRadioButtonValue == null) {
                                customFields[fieldName] = 'null';
                            }
                            return customFields[fieldName];
                        }
                        customFields[$(this).data('clients-field-name')] = $(this).val();
                    }
                });
                $.each(customFields, function (customKey, valueCustom) {
                    $.each(formAllData, function (formKey, formValue) {
                        var filter_column = formKey.substring(0, formKey.lastIndexOf("]") + 1);
                        var result = filter_column.startsWith("custom", 0);
                        if (result == true) {
                            if ('custom[' + customKey + ']' != filter_column) {
                                return missingValues['custom[' + customKey + ']'] = valueCustom;
                            }
                        } else {
                            return missingValues['custom[' + customKey + ']'] = valueCustom;
                        }
                    });
                });
                $.each(missingValues, function (key, value) {
                    formAllData[key] = value;
                });

                var clientUrl = $('form').attr('action');

                $.post(clientUrl, formAllData)
                    .done(function (response) {
                        if (response.duplicate == true) {
                            if (response.mess) {
                                $('#modal-placeholder').load("{{route('clients.get.duplicateName') }}", {data: response.duplicateData});
                            }
                        } else {
                            var url = '{{route('clients.show',['id' => ':id'])}}';
                            url = url.replace(':id', response.clientId);
                            window.location.replace(url);
                            alertify.success(response.alertSuccess);
                        }
                    })
                    .fail(function (response) {
                        $.each($.parseJSON(response.responseText).errors, function (id, message) {
                            alertify.error(message[0], 5);
                        });
                    });
            });
            @endif
        });
    </script>

@stop

@section('content')

    @if ($editMode)
        {!! Form::model($client, ['route' => ['clients.update', $client->id, 'tab' => $selectedTab], 'enctype'=>'multipart/form-data', 'id' => 'client-edit-form']) !!}

        {!!  Form::hidden('client_id', $client->id,['id' => 'client_id']) !!}
        {!!  Form::hidden('custom_module', 'client',['id' => 'custom_module']) !!}
    @else
        {!! Form::open(['route' => 'clients.store', 'enctype'=>'multipart/form-data']) !!}
    @endif

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="pull-left d-inline">
                        {{ trans('fi.client_form') }}
                    </h1>
                    @if ($editMode)
                        <span class="badge {{ isset($typeLabels[$client->type]) ? $typeLabels[$client->type] : '' }}">
                            {{ trans('fi.' . $client->type) }}
                        </span>
                        {{ $client->name }}
                    @endif
                </div>
                <div class="col-sm-6">
                    <div class="text-right">
                        @if ($editMode)
                            <a href="{{ $returnUrl }}" class="btn btn-sm btn-default">
                                <i class="fa fa-backward"></i> {{ trans('fi.back') }}
                            </a>
                        @endif
                        <button class="btn btn-sm btn-primary data-submit">
                            <i class="fa fa-save"></i> {{ trans('fi.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

            <div class="row">

                <div class="col-12">
                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" role="tablist">
                                @if ($editMode)
                                    <li class="nav-item"
                                        data-save-link="{{ route('clients.update', [$client->id, 'tab' => 'general']) }}">
                                        <a class="active nav-link" id="general-tab" href="#tab-general"
                                           data-toggle="tab">
                                            {{ trans('fi.general') }}
                                        </a>
                                    </li>
                                    @can('contacts.view')
                                        <li class="nav-item"
                                            data-save-link="{{ route('clients.update', [$client->id, 'tab' => 'contacts']) }}">
                                            <a class="nav-link" id="contacts-tab" href="#tab-contacts"
                                               data-toggle="tab">
                                                {{ trans('fi.contacts') }} {!! $client->contacts->count() > 0 ? '<span class="badge badge-default">'.$client->contacts->count().'</span>' : '' !!}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('attachments.view')
                                        <li class="nav-item"
                                            data-save-link="{{ route('clients.update', [$client->id, 'tab' => 'attachments']) }}">
                                            <a class="nav-link" id="attachments-tab" href="#tab-attachments"
                                               data-toggle="tab">
                                                {{ trans('fi.attachments') }} {!! $client->attachments->count() > 0 ? '<span class="badge badge-default">'.$client->attachments->count().'</span>' : '' !!}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('notes.view')
                                        <li class="nav-item"
                                            data-save-link="{{ route('clients.update', [$client->id, 'tab' => 'notes']) }}">
                                            <a class="nav-link" id="notes-tab" data-toggle="tab" href="#tab-notes">
                                                {{ trans('fi.notes') }}
                                                @if($client->notes->count() > 0)
                                                <span class="badge badge-primary" id="notes-count">{{ $client->notes->count() }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan
                                    <li class="nav-item"
                                        data-save-link="{{ route('clients.update', [$client->id, 'tab' => 'settings']) }}">
                                        <a id="settings-tab" href="#tab-settings" class="nav-link" data-toggle="tab">
                                            {{ trans('fi.settings') }}
                                        </a>
                                    </li>
                                @else
                                    <li class="nav-item">
                                        <a id="general-tab" href="#tab-general" class="active nav-link"
                                           data-toggle="tab">{{ trans('fi.general') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a id="settings-tab" href="#tab-settings" class="nav-link"
                                           data-toggle="tab">{{ trans('fi.settings') }}</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-general">
                                    @include('clients._form')
                                </div>
                                @if ($editMode)
                                    @can('contacts.view')
                                        <div class="tab-pane" id="tab-contacts">
                                            @include('clients._contacts', ['contacts' => $client->contacts()->orderBy('name')->get(), 'clientId' => $client->id])
                                        </div>
                                    @endcan
                                    @can('attachments.view')
                                        <div class="tab-pane" id="tab-attachments">
                                            @include('attachments._table', ['object' => $client, 'model' => 'FI\Modules\Clients\Models\Client', 'modelId' => $client->id])
                                        </div>
                                    @endcan
                                    @can('notes.view')
                                        <div id="tab-notes" class="tab-pane">
                                            @include('notes._js_timeline', ['object' => $client, 'model' => 'FI\Modules\Clients\Models\Client', 'hideHeader' => true, 'showPrivateCheckbox' => 0, 'showPrivate' => 1])
                                            <div id="note-timeline-container"></div>
                                        </div>
                                    @endcan
                                @endif
                                <div class="tab-pane" id="tab-settings">
                                    @include('clients._settings')
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="pull-right">
                @if ($editMode)
                    <a href="{{ $returnUrl }}" class="btn btn-sm btn-default"><i
                                class="fa fa-backward"></i> {{ trans('fi.back') }}</a>
                @endif
                <button class="btn btn-sm btn-primary data-submit"><i class="fa fa-save"></i> {{ trans('fi.save') }}
                </button>
            </div>

        </div>

    </section>

    {!! Form::close() !!}

@stop