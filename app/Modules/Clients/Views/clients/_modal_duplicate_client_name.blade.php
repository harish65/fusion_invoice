<script type="text/javascript">

    $(function (){

        $('#modal-edit-client').modal('show');

        $(document).on('click', '.single-record', function (){
            $('.single-record').not(this).prop('checked', false);
            if ($(this).is(':checked') == true) {
                $('#btn-edit-client-submit').removeAttr('disabled');
            } else {
                $('#btn-edit-client-submit').attr("disabled", true);
            }
        });

        $('#btn-edit-client-duplicate-data-submit').on('click', function (e){
            @if($duplicate)
            var form_all_data = {};
            var tags = {};
            var custom = {};
            @foreach($requestData as $key => $data)
                @if ($key == 'custom')
                    @foreach($data as $customKey => $customValue)
                        custom['{{$customKey}}'] = '{{$customValue}}';
                    @endforeach
                @elseif ($key == 'tags')
                    @foreach($data as $tagKey => $tag)
                        tags['{{$tagKey}}'] = '{{$tag}}';
                    @endforeach
                @elseif($key == 'address' || $key == 'important_note' || $key == 'general_notes' || $key == 'lead_source_notes')
                        form_all_data['{{$key}}'] ='{{jsFormattedAddress($data)}}';
                @else
                        form_all_data['{{$key}}'] = '{!! $data !!}';
                @endif
            @endforeach
            form_all_data['tags']   = tags;
            form_all_data['custom'] = custom;

            $.ajax({
                type:"POST",
                url:'{{route('clients.duplicate.store')}}',
                data:form_all_data,
                success:function (data){
                    if (data.error) {
                        alertify.error(data.error, 5);
                    }
                }
            }).done(function (response){
                if (response.flag) {
                    alertify.success(response.message);
                    var url = '{{route('clients.show',['id' => ':id'])}}';
                    url = url.replace(':id', response.clientId);
                    window.location.replace(url);
                }
            }).fail(function (response){
                $.each($.parseJSON(response.responseText).errors, function (id, message){
                    alertify.error(message[0], 5);
                });
            });
            @endif
        })

        $('#btn-edit-client-submit').on('click', function (e){

            var _error = false;
            $('.single-record').each(function (){
                if ($(this).is(':checked') === true) {
                    var id = $(this).data('id');
                    var link = "{{ route('clients.show', ['id'=>':client_id']) }}";
                    link = link.replace(':client_id', id);
                    window.open(link, '_self');
                    return false;
                } else {
                    _error = true;
                }
            });
            if (_error === true) {
                alertify.error('{{trans('fi.select_checkbox')}}');
                return false;
            }
        });

    });

</script>

<div class="modal" id="modal-edit-client">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.looks_like_duplicate') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modal-status-placeholder"></div>
                <div class="card-header">
                    {{ trans('fi.duplicate_instructions') }}
                </div>
                <div class="card-body">
                    <table class="table table-striped table-sm">
                        <thead>
                        <tr>
                            <th></th>
                            <th>{{ trans('fi.name') }}</th>
                            <th>{{ trans('fi.email') }}</th>
                            <th>{{ trans('fi.address') }}</th>
                            <th>{{ trans('fi.phone') }}</th>
                            <th>{{ trans('fi.created') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($clients != null)
                            @foreach ($clients as $client)
                                <tr>
                                    <td width="2%">
                                        <input type="checkbox" class="single-record" data-id="{{ $client->id }}">
                                    </td>
                                    <td>{{ $client->name }}</td>
                                    <td>{{ $client->email }}</td>
                                    <td>{{ ($client->address != null) ? $client->address :  null}}</td>
                                    <td>{{ (($client->phone ? $client->phone : ($client->mobile ? $client->mobile : ''))) }}</td>
                                    <td>{{ $client->formatted_created_at  }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" rowspan="6"> {{trans('fi.data_not_found')}}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                    <input class="btn btn-sm btn-primary" type="button" id="btn-edit-client-duplicate-data-submit" value="{{ trans('fi.save_this_client') }}">
                    <input class="btn btn-sm btn-danger" type="button" id="btn-edit-client-submit" disabled="disabled" value="{{ trans('fi.open_selected_client') }}">
                </div>
            </div>
        </div>
    </div>
</div>