<script type="text/javascript">
    $(function () {
        var modalContact = $('#modal-contact');

        modalContact.modal();

        $('[data-toggle="tooltip"]').tooltip({
            'delay': {show: 1100, hide: 100}
        });

        $('#btn-contact-submit').click(function () {
            $.post("{{ $submitRoute }}", $('#client-contact').serialize()).fail(function (response) {
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            }).done(function (response) {
                modalContact.modal('hide');
                @if ($editMode)
                    alertify.success('{{ trans('fi.contact_updated') }}', 5);
                @else
                    alertify.success('{{ trans('fi.contact_added') }}', 5);
                @endif
                $('#tab-contacts').html(response);
            });
        });
        $('.modal-contact-close').click(function (){
           $('.btn-action-modal').removeClass('disabled');
        });
    });
</script>

<div class="modal" id="modal-contact">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if ($editMode)
                        {{ trans('fi.edit_contact') }}
                    @else
                        {{ trans('fi.add_contact') }}
                    @endif
                </h5>
                <button type="button" class="close modal-contact-close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal" id="client-contact" name="client-contact">

                    <div class="form-group">
                        <label>{{ trans('fi.title') }}:</label>
                        {!! Form::select('title', $contactTitle, ($editMode) ? $contact->title : null, ['id' => 'title', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.name') }}:</label>
                        {!! Form::text('name', ($editMode) ? $contact->name : null, ['class' => 'form-control form-control-sm', 'id' => 'contact_name']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.email') }}:</label>
                        {!! Form::text('email', ($editMode) ? $contact->email : null, ['class' => 'form-control form-control-sm', 'id' => 'contact_email']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.primary_phone') }}:</label>
                        {!! Form::text('primary_phone', ($editMode) ? $contact->primary_phone : null, ['class' => 'form-control form-control-sm', 'id' => 'primary_phone']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.alternate_phone') }}:</label>
                        {!! Form::text('alternate_phone', ($editMode) ? $contact->alternate_phone : null, ['class' => 'form-control form-control-sm', 'id' => 'alternate_phone']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.notes') }}:</label>
                        {!! Form::textarea('notes', ($editMode) ? $contact->notes : null, ['class' => 'form-control form-control-sm', 'id' => 'contact_notes', 'rows' => 3]) !!}
                    </div>

                    <div class="form-group">
                        <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_default_to') !!}">{{ trans('fi.default_to') }}:</label>
                        {!! Form::select('default_to', ['0' => trans('fi.no'), '1' => trans('fi.yes')], ($editMode) ? $contact->default_to : null, ['id' => 'default_to', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_default_cc') !!}">{{ trans('fi.default_cc') }}:</label>
                        {!! Form::select('default_cc', ['0' => trans('fi.no'), '1' => trans('fi.yes')], ($editMode) ? $contact->default_cc : null, ['id' => 'default_cc', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_default_bcc') !!}">{{ trans('fi.default_bcc') }}:</label>
                        {!! Form::select('default_bcc', ['0' => trans('fi.no'), '1' => trans('fi.yes')], ($editMode) ? $contact->default_bcc : null, ['id' => 'default_bcc', 'class' => 'form-control form-control-sm']) !!}
                    </div>
                    {!! Form::hidden('client_id', $clientId) !!}
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default modal-contact-close" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-contact-submit" class="btn btn-sm btn-primary">{{ trans('fi.save') }}</button>
            </div>
        </div>
    </div>
</div>
