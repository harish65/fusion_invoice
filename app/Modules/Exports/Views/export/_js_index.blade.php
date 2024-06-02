<script type="text/javascript">
    $(document).ready(function () {

        $('body').on('click', '#save-mapping', function(){
            let selectedMappingOptionValue = $('#mapping-dropdown').val();
            if(selectedMappingOptionValue){
                let mapping_name = $('#mapping-dropdown').find(':selected').text();
                let mapping_is_default = $('#mapping-dropdown').find(':selected').attr('data-is-default');
                $('#mapping-name').val(mapping_name);
                if(mapping_is_default == 1)
                {
                    $('#is-default-mapping').prop('checked', true);
                }
                else
                {
                    $('#is-default-mapping').removeAttr('checked');
                }
                $('#modal-mappings').modal();
            }
            else
            {
                $('#mapping-name').val('');
                $('#is-default-mapping').prop('checked',false);
                $('#modal-mappings').modal();
            }
        });

        $('body').on('change', '#mapping-dropdown', function() {
            let selectedMappingOptionValue = this.value;
            if($(this).val() != '' && $(this).val().length > 0){
                $('#delete-mapping').show();

                $("#mapping-dropdown option[value=" + selectedMappingOptionValue + "]").attr('selected', true).siblings().removeAttr('selected');
                $.getJSON( "{{ route('export.change_mapping') }}", {id: $(this).val()} ,function( data ) {
                    if('description' in data){
                        $('input[name="fields[]"]').removeAttr('checked');
                        $.each( data.description, function( key, value ) {
                            $('input[name="fields[]"][value="' + value + '"]').prop('checked', true);
                        });
                        $('#export-form #format').val(data.format);
                    }
                });
            }
            else
            {
                $('input[name="fields[]"]').prop('checked', true);
                $('#delete-mapping').hide();
                $('#export-form #format').val('CSV');
            }
        });

        $('body').on('click', '#delete-mapping', function (){
            $(this).addClass('delete-export-mapping-active');
            var url = "{{ route("export.delete_mapping", ["id" => ":id", "type" => ":type"]) }}";
            let selectedMappingId = $('#mapping-dropdown').val();
            url = url.replace(':id', selectedMappingId);
            url = url.replace(':type', $('#export-form #mapping-type').val());

            $('#modal-placeholder').load('{!! route('export.delete.modal') !!}', {
                    action: url,
                    modalName: 'export-mapping',
                    isReload: false,
                    returnURL: '{{route('import.index')}}',
                    selectedMappingId: selectedMappingId,
                    message: "{!! trans('fi.delete_import_mapping_warning') !!}"
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );

        });

        $('body').on('click', '#submit-mappings', function (){
            let formData = new FormData();
            formData.append('name', $('#save-mapping-form #mapping-name').val());
            formData.append('type', $('#export-form #mapping-type').val());
            formData.append('is_default', ($('#save-mapping-form #is-default-mapping').prop('checked')) ? 1 : 0);
            formData.append('format', $('#export-form #format').val());

            $( "#export-form input[name='fields[]']:checked" ).each(function( index,  element) {
                formData.append('description[' + index + ']', $(element).val());
            });

            $.ajax({
                url: "{{ route('export.save_mapping') }}",
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(data){
                    alertify.success(data.message, 5);
                    if($("#mapping-dropdown option[value='" + data.data.id + "']").length > 0){
                        $("#mapping-dropdown option[value=" + data.data.id + "]").html(data.data.name)
                    }
                    else{
                        $('#mapping-dropdown').append(new Option(data.data.name, data.data.id));
                        $('#mapping-dropdown').val(data.data.id).trigger('change');
                    }
                    if(data.data.is_default){
                        $("#mapping-dropdown > option").each(function() {
                            $(this).attr('data-is-default', '0');
                        });
                        $("#mapping-dropdown option[value=" + data.data.id + "]").attr('data-is-default', '1');
                    }
                    $('#modal-mappings').modal('hide');
                },
                error: function (XMLHttpRequest) {
                    if (XMLHttpRequest.status == 422) {
                        for (const [key, value] of Object.entries(XMLHttpRequest.responseJSON.errors)) {
                            alertify.error(`${value}`, 5);
                        }
                    }
                    else {
                        alertify.error(XMLHttpRequest.responseJSON.message, 5);
                    }
                }
            });
        });

        $('body').on('click', '#export-tabs a',function (e) {
            e.preventDefault();

            let exportType = $(this).data("export-type");
            let url = '{{ route("export.populate_form", ["type" => ":type"]) }}';
            url = url.replace(':type', exportType);

            // ajax load from data-url
            $('#tab-pane').load(url,function(){
                $(this).show();
            });
        });

        $('#export-tabs li.active a').trigger('click');
    })
</script>
