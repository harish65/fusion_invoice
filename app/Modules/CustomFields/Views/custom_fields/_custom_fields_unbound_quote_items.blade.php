@if(count($customFields))
    <table style="width: 100%" class="table table-striped no-padding custom-fields-table mt-10">
        <tr>
            <td width="100%">
                @foreach (array_chunk($customFields, 3) as $customFieldsChunk)
                    <div class="row">
                        @foreach($customFieldsChunk as $customField)
                            <div class="col-md-4">
                                @include('custom_fields._custom_fields',['key' => $key])
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </td>
        </tr>
    </table>
@endif
