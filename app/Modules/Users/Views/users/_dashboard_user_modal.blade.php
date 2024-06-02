@include('layouts._colorpicker')
<script type="text/javascript">
    $(function () {

        $('body #modal-{{$modalName}}-setting').modal();

        $('#btn-{{$modalName}}-setting').click(function (e) {

            showHideLoaderModal();

            var formData = $('#dashboard-user-modal').serializeFormJSON();

            $.post('{{route('dashboard.user.modal.update')}}', formData)
                .done(function (response) {
                    showHideLoaderModal();
                    $('#modal-{{$modalName}}-setting').modal('hide');
                    if (response.success == true) {
                        alertify.success(response.message, 5);
                        location.reload();
                    }

                }).fail(function (response) {
                showHideLoaderModal();
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
        });


        $('.fi-dashboard-colorpicker').colorpicker();

        $('.fi-dashboard-colorpicker').on('colorpickerChange', function (event) {
            $('.colorpicker-element .fa-square').css('color', event.color.toString());
        });

        $(document).on('change', '#dashboard-name', function () {
            // $('#dashboard-name').change(function () {
            let name = $(this).val().replace(/[^a-zA-Z ]/g, "").toLowerCase();
            let parts = name.split(' ');
            let initials = parts[0].substring(0, 1);

            if (2 > parts.length) {
                initials = initials + parts[parts.length - 1].substring(1, 2);
            } else {
                initials = initials + parts[parts.length - 1].substring(0, 1);
            }
            initials = initials.toUpperCase();

            $('#dashboard-initials').val(initials);

        });
    });
</script>

<div class="modal fade" id="modal-{{$modalName}}-setting" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break">
        {!! Form::model($user, ['route' => array('users.update', $user->id),'id'=>'dashboard-user-modal']) !!}
        {!! Form::hidden('userId',$user->id) !!}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{trans('fi.profile_setting')}} </h5>
                <button type="button" class="close btn-{{$modalName}}-cancel" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('fi.name') }}: </label>
                                        {!! Form::text('name', null, ['id' => 'dashboard-name', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_gen_skin') !!}">{{ trans('fi.skin') }}: </label>
                                    {!! Form::select('skin', $skins, config('fi.skin'), ['id'=>'dashboard-skin','class' => 'form-control form-control-sm']) !!}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('fi.initials') }}
                                            : </label>
                                        {!! Form::text('initials', null, ['id' => 'dashboard-initials', 'class' => 'form-control form-control-sm', 'maxlength' => 2]) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans(('fi.initials_bg_color')) }}: </label>

                                        <div class="input-group colorpicker-element">
                                            {!! Form::text('initials_bg_color', null, ['class' => 'form-control form-control-sm fi-dashboard-colorpicker dashboard-initials-bg-color', 'readonly' => true]) !!}
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i
                                                            class="fas fa-square"
                                                            style="{{ isset($user->initials_bg_color) && $user->initials_bg_color != '' ? 'color:'.$user->initials_bg_color : '' }}"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('fi.password') }}: </label>
                                        {!! Form::password('password', ['id' => 'dashboard-password', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('fi.password_confirmation') }}: </label>
                                        {!! Form::password('password_confirmation', ['id' => 'dashboard-password_confirmation', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" id="btn-{{$modalName}}-cancel"
                        data-dismiss="modal">{{trans('fi.cancel')}}</button>
                <button type="button" id="btn-{{$modalName}}-setting"
                        class="btn btn-sm btn-primary">{{trans('fi.submit')}}</button>
            </div>
        </div>

        {!! Form::close() !!}

    </div>

</div>
