<script type="text/javascript">
    $(function () {
        $('.btn-edit-contact').click(function () {
            $('#modal-placeholder').load($(this).data('url'));
        });

        $('.btn-delete-contact').click(function () {

            var $_this = $(this);
            $_this.addClass('delete-contacts-active');

            $('#modal-placeholder').load('{!! route('clients.delete.contact.modal') !!}', {
                    action: '{{ route('clients.contacts.delete', [$client->id]) }}',
                    id: $_this.data('contact-id'),
                    modalName: 'contacts',
                    isReload: false,
                    returnURL: '{{route('clients.show', [$client->id])}}'
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );
        });

        $('.update-default').click(function () {
            $.post('{{ route('clients.contacts.updateDefault', [$client->id]) }}', {
                id: $(this).data('contact-id'),
                default: $(this).data('default')
            }).done(function (response) {
                $('#tab-contacts').html(response);
            });
        });
    });
</script>

<div class="row">
    <div class="col-lg-12">
        <table class="table table-hover table-striped table-responsive-md table-responsive-sm">
            <thead>
            <tr>
                <th>{{ trans('fi.title') }}</th>
                <th>{{ trans('fi.name') }}</th>
                <th>{{ trans('fi.email') }}</th>
                <th>{{ trans('fi.primary_phone') }}</th>
                <th>{{ trans('fi.notes') }}</th>
                <th>{{ trans('fi.default_to') }}</th>
                <th>{{ trans('fi.default_cc') }}</th>
                <th>{{ trans('fi.default_bcc') }}</th>
                @if(Gate::check('contacts.update') || Gate::check('contacts.delete'))
                    <th class="text-right">{{ trans('fi.options') }}</th>
                @endif
            </tr>
            </thead>
            <tbody>
            <?php foreach ($client->contacts as $contact) { ?>
            <tr>
                <td>{{ $contact->title ? trans('fi.'.$contact->title) : '' }}</td>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->email }}</td>
                <td>{{ $contact->primary_phone }}</td>
                <td>{!! $contact->formatted_notes !!}</td>
                <td>{{ $contact->formatted_default_to }}</td>
                <td>{{ $contact->formatted_default_cc }}</td>
                <td>{{ $contact->formatted_default_bcc }}</td>
                @if(Gate::check('contacts.update') || Gate::check('contacts.delete'))
                    <td class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                {{ trans('fi.options') }} <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('contacts.update')
                                    <a href="javascript:void(0)" class="btn-edit-contact dropdown-item"
                                       data-url="{{ route('clients.contacts.edit', [$contact->client_id, $contact->id]) }}">
                                        <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                                    </a>
                                @endcan
                                @can('contacts.delete')
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0)" class="btn-delete-contact text-danger dropdown-item"
                                       data-contact-id={{ $contact->id }}>
                                        <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </td>
                @endif
            </tr>
            <?php } ?>
            </tbody>
        </table>

    </div>
</div>
