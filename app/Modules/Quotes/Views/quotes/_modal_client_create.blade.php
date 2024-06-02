<script type="text/javascript">
    $(function () {

        $('body #modal-client-new-quote').modal();

        $('#btn-submit-client-new-quote').click(function (evt) {
            $('#btn-submit-client-new-quote').attr('disabled', 'disabled');
            var name = $('#new-client-name').val();
            $.post('{{ route('clients.store.on_the_fly') }}', {
                name: name,
            }).done(function (response) {
                $('#modal-client-new-quote').modal('hide');
                var settings = {
                    placeholder: '{{ trans('fi.select_client') }}',
                    allowClear: true,
                    language: {
                        noResults: function () {
                            return '<li><a href="javascript:void(0)" class="text-primary create-client btn-sm"><i class="fa fa-plus"></i> {{ trans('fi.add-new-client') }}</a></li>';
                        }
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                };
                $('#' + '{{$type}}' + '_client_name').append('<option value="' + response.client_id + '">' + name + '</option>').val(response.client_id).select2(settings).trigger('change');

                alertify.success('{{ trans('fi.record_successfully_created') }}');
            }).fail(function (response) {
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });
    });
</script>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-client-new-quote" data-keyboard="false"
     data-backdrop="static" style="z-index:99999;position: absolute;">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{trans('fi.add_new_client')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form id="columns-filter">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="ajs-content">
                                    <p>Client Name</p>
                                    <input class="form-control form-control-sm" id="new-client-name"
                                           value="{{$clientName}}" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer pb-1 pt-1">
                    <div class="col-sm-12">
                        <button type="button" id="btn-submit-client-new-quote"
                                class="btn btn-sm btn-primary float-right ml-2">
                            {{ trans('fi.add') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-default float-right" data-dismiss="modal"
                                id="btn-clear-column-filter">{{ trans('fi.cancel') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>