<script type="text/javascript">
    $(function (){

        $('body #modal-users').modal();

        var settings = {
            placeholder:'{{ trans('fi.select_users') }}',
            allowClear:true,
            width:'100%',
            escapeMarkup:function (markup){
                return markup;
            },
        };

        $('#users-tags').select2(settings);

        $('#btn-submit-column-setting').click(function (evt){
            $_this = $(this);
            $.post($_this.data('url'), {
                userId:$('#users-tags').val(),
                userParentId:'{{$userParentId}}'
            }).done(function (response){
                if (response.success == true) {
                    alertify.success(response.message);
                } else {
                    alertify.error(response.message);
                }
                $('#modal-users').modal('hide');
            }).fail(function (response){
                $.each($.parseJSON(response.responseText).errors, function (id, message){
                    alertify.error(message[0], 5);
                });
            });
        });
    });
</script>

<div class="modal fade" id="modal-users" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{trans('fi.assign_configuration_other')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body no-padding">
                                <div class="form-group">
                                    <label data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_user_tags') !!}">{{ trans('fi.select_users') }}
                                        : </label>
                                    {{ Form::select('users[]', $users, 0,['class' => 'form-control form-control-sm users-tags','multiple' => true, 'id' => 'users-tags', 'style' => 'width:100%']) }}
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-sm-12">
                    <button type="button" id="btn-submit-column-setting"
                            data-url="{{ route('set.users.setting') }}"
                            class="btn btn-sm btn-primary float-right ml-2">{{ trans('fi.submit') }}</button>
                    <button type="button" class="btn btn-sm btn-default float-right" data-dismiss="modal"
                            id="btn-clear-column-filter">{{ trans('fi.close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>