@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function () {
            $('.modal-box').change(function () {
                if (($(this).val()).length > 0) {
                    $('.data-seeder-toggle').removeClass('d-none').addClass('d-block');
                } else {
                    $('.data-seeder-toggle').addClass('d-none').removeClass('d-block');
                }
            });

            $('.btn-seeder').click(function () {
                $.post('{{ route('data.seeder.modal') }}', {
                    module: $('#modal-box').val(),
                    number_of_seed_data: $('#number-of-seed-data').val()
                }).done(function (response) {
                    $('.data-seed-record').html(response);
                    alertify.success('{{ trans('fi.seeds_success') }}', 5);
                }).fail(function (response) {
                    alertify.error($.parseJSON(response.responseText).message, 5);
                });
            })
        });

    </script>
@stop

@section('content')
    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1><i class="fa fa-database"></i> {{trans('fi.data_seeder')}}</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">

        <div class="container-fluid">

            <div class="card card-primary card-outline">

                <div class="card-header">
                    <div class="form-group row">
                        <label class="control-label p-lg-1" for="modal-box">
                            {{trans('fi.select_module')}}
                        </label>
                        <div class="col-2">
                            {!! Form::select('module', $modules,'', ['id' => 'modal-box', 'class' => 'form-control form-control-sm modal-box','placeholder'=>trans('fi.select_module')]) !!}
                        </div>
                        <label class="control-label p-lg-1 data-seeder-toggle d-none"
                               for="number-of-seed-data">
                            {{trans('fi.number_of_seed')}}
                        </label>
                        <div class="col-2 data-seeder-toggle d-none">
                            {!! Form::text('number_of_seed_data', null, ['id' => 'number-of-seed-data', 'class' => 'form-control form-control-sm number-of-seed-data','placeholder'=>trans('fi.number_of_seed')]) !!}
                        </div>
                        <div class="float-right data-seeder-toggle d-none">
                            <button type="button"
                                    class="btn btn-sm btn-primary btn-seeder">{{trans('fi.seed_it')}}</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive data-seed-record"></div>
                </div>

            </div>
        </div>
    </section>

@stop