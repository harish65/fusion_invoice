@include('layouts._select2')
@include('clients._js_lookup')
@include('clients._js_subchange')

<div class="modal fade" id="modal-lookup-client">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.change_client') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="form-group">
                        <label>{{ trans('fi.client') }}</label>
                        {!! Form::select('client_name', $clients, $client->id, ['id' => 'change_client_name', 'class' => 'form-control form-control-sm client-lookup', 'autocomplete' => 'off', 'style'=>"width: 100%;"]) !!}
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-submit-change-client" class="btn btn-sm btn-primary">{{ trans('fi.save') }}
                </button>
            </div>
        </div>
    </div>
</div>