<script type="text/javascript">
    $(function () {
        $('#btn-add-contact').click(function () {
            $('#modal-placeholder').load('{{ route('clients.contacts.create', [$clientId]) }}');
        });

        $('.btn-edit-contact').click(function () {
            $('#modal-placeholder').load($(this).data('url'));
        });

        $('.btn-delete-contact').click(function () {

            var $_this = $(this);
            $_this.addClass('delete-contacts-active');

            $('#modal-placeholder').load('{!! route('clients.delete.contact.modal') !!}', {
                    action: '{{ route('clients.contacts.delete', [$clientId]) }}',
                    id: $_this.data('contact-id'),
                    modalName: 'contacts',
                    isReload: false,
                    returnURL: '{{route('clients.show', [$clientId])}}'
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
            $.post('{{ route('clients.contacts.updateDefault', [$clientId]) }}', {
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
        <div class="card card-primary card-outline">
            <div class="card-header">
                <div class="card-tools">
                    @can('contacts.create')
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-action-modal" id="btn-add-contact"><i class="fa fa-plus"></i> {{ trans('fi.add_contact') }}</a>
                    @endcan
                </div>
            </div>
            <div class="card-body pad table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>{{ trans('fi.title') }}</th>
                        <th>{{ trans('fi.name') }}</th>
                        <th>{{ trans('fi.email') }}</th>
                        <th>{{ trans('fi.notes') }}</th>
                        <th>{{ trans('fi.default_to') }}</th>
                        <th>{{ trans('fi.default_cc') }}</th>
                        <th>{{ trans('fi.default_bcc') }}</th>
                        @if(Gate::check('contacts.update') || Gate::check('contacts.delete'))
                            <th>{{ trans('fi.options') }}</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($contacts as $contact) { ?>
                    <tr>
                        <td>{{ $contact->title ? trans('fi.'.$contact->title) : '' }}</td>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{!! $contact->formatted_notes !!}</td>
                        <td>
                            @can('contacts.update')
                                <a href="javascript:void(0)" class="update-default" data-default="to"
                                   data-contact-id="{{ $contact->id }}">{{ $contact->formatted_default_to }}</a>
                            @else
                                {{ $contact->formatted_default_to }}
                            @endcan
                        </td>
                        <td>
                            @can('contacts.update')
                                <a href="javascript:void(0)" class="update-default" data-default="cc"
                                   data-contact-id="{{ $contact->id }}">{{ $contact->formatted_default_cc }}</a>
                            @else
                                {{ $contact->formatted_default_cc }}
                            @endcan
                        </td>
                        <td>
                            @can('contacts.update')
                                <a href="javascript:void(0)" class="update-default" data-default="bcc"
                                   data-contact-id="{{ $contact->id }}">{{ $contact->formatted_default_bcc }}</a>
                            @else
                                {{ $contact->formatted_default_bcc }}
                            @endcan
                        </td>
                        @if(Gate::check('contacts.update') || Gate::check('contacts.delete'))
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                            data-toggle="dropdown">
                                        {{ trans('fi.options') }} <span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @can('contacts.update')
                                            <a href="javascript:void(0)" class="btn-edit-contact dropdown-item"
                                               data-url="{{ route('clients.contacts.edit', [$clientId, $contact->id]) }}"><i
                                                        class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                        @endcan
                                        @can('contacts.delete')
                                            <a href="javascript:void(0)"
                                               class="btn-delete-contact text-danger dropdown-item"
                                               data-contact-id={{ $contact->id }}><i
                                                        class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>
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
    </div>
</div>