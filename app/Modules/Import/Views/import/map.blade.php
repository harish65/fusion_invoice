@extends('layouts.master')

@section('javascript')
    <script>
        $(document).ready(function () {

            $('#mapping-dropdown').change(function () {
                let selectedMappingOptionValue = this.value;
                if ($(this).val() != '' && $(this).val().length > 0) {
                    $('.icon-delete').show();

                    $("#mapping-dropdown option[value=" + selectedMappingOptionValue + "]").attr('selected', true).siblings().removeAttr('selected');
                    $.getJSON("{{ route('import.change_mapping') }}", {id: $(this).val()}, function (data) {
                        if ('description' in data) {
                            $.each(data.description, function (key, value) {
                                let selectedValue = $('select[name="' + key + '"] option').filter(function () {
                                    return $(this).html() == value;
                                }).val();
                                $('select[name="' + key + '"]').val(selectedValue);
                            });
                        }
                    });
                } else {
                    $('.icon-delete').hide();
                }
            });
            $('.mapping-option-actions .icon-delete').click(function () {
                $(this).addClass('delete-import-mapping-active');
                var url = "{{ route("import.delete_mapping", ["id" => ":id", "type" => ":type"]) }}";
                let selectedMappingId = $('#mapping-dropdown').val();
                url = url.replace(':id', selectedMappingId);
                url = url.replace(':type', "{{ $importType }}");
                $('#modal-placeholder').load('{!! route('import.delete.modal') !!}', {
                        action: url,
                        modalName: 'import-mapping',
                        isReload: false,
                        returnURL: '{{route('import.index')}}',
                        selectedMappingId: selectedMappingId,
                        message: "{!! trans('fi.delete_import_mapping_warning') !!}"
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );
            });

            $('.mapping-option-actions .icon-edit').on('click', function () {
                let mapping_id = $('#mapping-dropdown').find(':selected').val();
                if (mapping_id != '') {
                    let mapping_is_default = $('#mapping-dropdown').find(':selected').attr('data-is-default');
                    $('#mapping-id').val(mapping_id);
                    $('#mapping-name').val($('#mapping-dropdown').find(':selected').text());
                    if (mapping_is_default == 1) {
                        $('#is-default-mapping').prop('checked', true);
                    } else {
                        $('#is-default-mapping').removeAttr('checked');
                    }
                } else {
                    $('#mapping-id, #mapping-name').val('');
                    $('#is-default-mapping').prop('checked', false);
                }
                $('#modal-mappings').modal();
            });

            $('#submit-mappings').on('click', function () {
                let formData = new FormData();
                formData.append('id', $('#save-mapping-form #mapping-id').val());
                formData.append('name', $('#save-mapping-form #mapping-name').val());
                formData.append('type', "{{ $importType }}");
                formData.append('is_default', ($('#save-mapping-form #is-default-mapping').prop('checked')) ? 1 : 0);

                $("#field-mappings select").each(function (index, element) {
                    formData.append('description[' + $(element).attr('name') + ']', $(element).children(":selected").text());
                });

                $.ajax({
                    url: "{{ route('import.save_mapping') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (data) {
                        alertify.success(data.message, 5);
                        if (data.isNew) {
                            $('#mapping-dropdown').append(new Option(data.data.name, data.data.id));
                            $('#mapping-dropdown').val(data.data.id);
                        } else {
                            $("#mapping-dropdown option[value=" + data.data.id + "]").html(data.data.name)
                        }
                        if (data.data.is_default) {
                            $("#mapping-dropdown > option").each(function () {
                                $(this).attr('data-is-default', '0');
                            });
                            $("#mapping-dropdown option[value=" + data.data.id + "]").attr('data-is-default', '1');
                        }
                        $('#modal-mappings').modal('hide');
                        $('.icon-delete').show();
                    },
                    error: function (XMLHttpRequest, textStatus, error) {
                        if (XMLHttpRequest.status == 422) {
                            for (const [key, value] of Object.entries(XMLHttpRequest.responseJSON.errors)) {
                                alertify.error(`${value}`, 5);
                            }
                        } else {
                            alertify.error(XMLHttpRequest.responseJSON.message, 5);
                        }
                    }
                });
            });
        });
    </script>
@endsection
@section('content')

    <section class="content-header">

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>{{ trans('fi.map_fields_to_import') }} - {{ trans('fi.'.unCamelize($importType)) }}</h1>

                </div>

                <div class="col-sm-6">

                    <div class="text-right">

                        @if (!config('app.demo'))
                            <a href="{{ route('import.index', ['importType' => $importType]) }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-backward"></i> {{trans('fi.back')}}
                            </a>
                        @endif

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            {!! Form::open(['route' => ['import.map.submit', $importType], 'class' => 'form-horizontal']) !!}

            @include('layouts._alerts')

            <div class="card card-primary card-outline">

                <div class="card-header">

                    <div class="card-tools">

                        <ul class="nav nav-pills ml-auto">

                            @if(in_array($importType,['invoices','invoiceItems','itemLookups','payments']))

                                <li class="nav-item mr-1">

                                    <button type="button" id="show-notice" class="btn btn-sm btn-warning btn-margin-left" data-toggle="modal" data-target="#modal-important-details">
                                        <i class="fa fa-exclamation"></i>&nbsp;{{ trans('fi.important_details') }}
                                    </button>

                                </li>

                            @endif

                            <li class="nav-item mr-1">

                                <select class="form-control form-control-sm inline" id="mapping-dropdown" name="mapping">
                                    <option value="">{{trans('fi.select_mapping')}}</option>
                                    @foreach($mappingOptions as $mapping)
                                        <option data-is-default="{{($mapping->is_default) ? '1' : '0'}}" value="{{$mapping->id}}" {{($defaultMapping && $mapping->id == $defaultMapping->id) ? 'selected' : ''}}>{{ $mapping->name }}</option>
                                    @endforeach
                                </select>

                            </li>

                            <li class="nav-item mr-1 mapping-option-actions">
                                <span class="icon-edit btn btn-sm btn-primary" title="{{($defaultMapping) ? trans('fi.edit_mapping') : trans('fi.add_mapping')}}"><i class="fa fa-save"></i></span>
                                <span class="icon-delete btn btn-sm btn-danger" style="display: {{($defaultMapping) ? '' : 'none'}}"><i class="fa fa-trash"></i></span>
                            </li>

                            <li class="nav-item mr-1">

                                {!! Form::submit(trans('fi.import_data'), ['class' => 'btn btn-sm btn-primary']) !!}

                            </li>

                        </ul>

                    </div>

                </div>

                <div class="card-body">

                    <table class="table table-striped table-responsive-sm table-responsive-xs table-sm" id="field-mappings">
                        <tbody>
                        @foreach ($importFields as $key => $field)
                            <tr>
                                <td style="width: 20%;">{{ $field }}</td>
                                @if($defaultMapping && isset($defaultMapping->description) && isset($defaultMapping->description[$key]))
                                    <td>{!! Form::select($key, $fileFields, (is_numeric(array_search($defaultMapping->description[$key], $fileFields)) ? array_search($defaultMapping->description[$key], $fileFields) : null), ['class' => 'form-control form-control-sm']) !!}</td>
                                @else
                                    <td>{!! Form::select($key, $fileFields, (is_numeric(array_search($key, $fileFields)) ? array_search($key, $fileFields) : null), ['class' => 'form-control form-control-sm']) !!}</td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>

            </div>

            {!! Form::close() !!}

        </div>

    </section>


    <div class="modal fade" id="modal-mappings" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('fi.add_mapping') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="save-mapping-form">

                        {!! Form::hidden('id', '' , ['class' => 'form-control form-control-sm', 'id' => 'mapping-id']) !!}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('fi.name') }}</label>

                            <div class="col-sm-7">
                                {!! Form::text('name', '' , ['class' => 'form-control form-control-sm', 'id' => 'mapping-name']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-7">
                                {!! Form::checkbox('is_default', 1, 0, ['id' => 'is-default-mapping', 'class'=>'check check-aligned']) !!}
                                <label class="form-check-label" style="margin-left: 10px;" for="is-default-mapping">{{ trans('fi.save_as_default_mapping') }}</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                    @can('exports.create')
                        <button type="button" id="submit-mappings" class="btn btn-sm btn-primary"
                                data-text="{{ trans('fi.submit') }}"
                                data-loading-text="{{ trans('fi.please_wait') }}...">{{ trans('fi.submit') }}</button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-important-details" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-danger">{{ trans('fi.'.unCamelize($importType)) }}- {{ trans('fi.important_information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    @if($importType == 'invoices')
                        {!! trans('fi.invoices_important_information') !!}
                    @elseif($importType == 'invoiceItems')
                        {!! trans('fi.invoice_items_important_information') !!}
                    @elseif($importType == 'itemLookups')
                        {!! trans('fi.item_lookups_important_information') !!}
                    @elseif($importType == 'payments')
                        {!! trans('fi.payments_important_information') !!}
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">{{ trans('fi.ok') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop