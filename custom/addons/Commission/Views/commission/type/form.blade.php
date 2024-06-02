@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#name').focus();

            $("#method").change(function () {
                if ($(this).val() == 'formula') {
                    $(".formula").show();
                    $(".manual_entry").hide();
                } else {
                    $(".manual_entry").show();
                    $(".formula").hide();
                }
            });

            $(".copy-to-clipboard").click(function () {
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($(this).prev('span').text()).select();
                document.execCommand("copy");
                $temp.remove();
                alertify.success('{{ trans('Commission::lang.copied') }}', 5);
            });

            @isset($commissionType)
            $("#method").val('{{$commissionType->method}}').change();
                @if($commissionType->method == 'manual_entry')
                $(".manual_entry").show();
                $(".formula").hide();
                @endif
            @endif
        });

    </script>

    @isset($commissionType)
        {!! Form::model($commissionType, ['route' => ['invoice.commission.type.update', $commissionType->id]]) !!}
    @else
        {!! Form::open(['route' => 'invoice.commission.type.store']) !!}
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
                        @isset($commissionType)
                            {{ trans('Commission::lang.commission_type_form') }}
                        @else
                            {{ trans('Commission::lang.commission_type_edit') }}
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="text-right">
                        <a href="{{ route('invoice.commission.type.index') }}"
                           class="btn btn-sm btn-default"> {{ trans('fi.cancel') }}</a>

                        <button class="btn btn-sm btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}</button>
                    </div>
                </div>
            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

            <div class="row">

                <div class="col-md-12">

                    <div class="card card-primary card-outline">

                        <div class="card-body">

                            <div class="form-group">
                                <label>{{ trans('Commission::lang.name') }}: </label>
                                {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ trans('Commission::lang.method') }}: </label>
                                <select class="form-control form-control-sm" id="method" name="method">
                                    <option value="formula">{{ trans('Commission::lang.formula') }} </option>
                                    <option value="manual_entry">{{ trans('Commission::lang.manual_entry') }} </option>
                                </select>
                            </div>

                            <div class="form-group formula" style="display: {{ isset($commissionType) && $commissionType->method == 'formula' ? 'block' : 'block' }};">
                                <label>{{ trans('Commission::lang.formula') }}: </label>
                                {!! Form::text('formula', null, ['id' => 'formula', 'class' => 'form-control form-control-sm']) !!}
                                <small class="text-muted">{!! trans('Commission::lang.commission_formula_notes') !!}</small>
                                <small class="text-muted">{{ trans('fi.notes') }}: {{ trans('Commission::lang.commission_formula_note') }}</small>
                            </div>

                            <div class="form-group manual_entry" style="display: {{ isset($commissionType) && $commissionType->method == 'manual_entry' ? 'block' : 'none' }};">
                                <small class="text-muted">{!! trans('Commission::lang.commission_manual_entry_notes') !!}</small>
                            </div>                            
                            
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop
