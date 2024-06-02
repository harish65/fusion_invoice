<link href="{{ asset('assets/plugins/drag-drop-upload/drag-drop-upload.css') }}" rel="stylesheet" type="text/css"/>

<script type="text/javascript">
    var passed = [];

    async function dropHandler(ev) {
        // Prevent default behavior (Prevent file from being opened)
        ev.preventDefault();
        let draggedFiles = [];
        if (ev.dataTransfer.items) {
            // Use DataTransferItemList interface to access the file(s)
            for (var i = 0; i < ev.dataTransfer.items.length; i++) {
                // If dropped items aren't files, reject them
                if (ev.dataTransfer.items[i].kind === 'file') {
                    var file = ev.dataTransfer.items[i].getAsFile();
                    draggedFiles.push(file);
                }
            }
        } else {
            // Use DataTransfer interface to access the file(s)
            for (var i = 0; i < ev.dataTransfer.files.length; i++) {
                draggedFiles.push(ev.dataTransfer.files[i]);
            }
        }
        if (draggedFiles.length > 0)
        {
            let passedFiles = await validateUpload(draggedFiles);

            if (passedFiles && Array.isArray(passedFiles))
            {
                startUpload(passedFiles);
            }
        }
    }

    function dragOverHandler(ev) {
        // Prevent default behavior (Prevent file from being opened)
        ev.preventDefault();
    }

    var validateUploadFile = (file) => {

        return new Promise(async (resolve, reject) => {
            if ((file.size > 8000000)) {
                $('#input-attachments').val('');
                alertify.error("{{ trans('fi.attachment_error', ['size' => '8MB']) }}", 5);
                reject(false);
            } else if ((file.size > 2000000) && (file.size < 8000000)) {
                let attachment_warning = "{{ trans('fi.attachment_warning') }}";
                var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                let bytes = file.size;
                let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                let size = Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
                attachment_warning = attachment_warning.replace(':size', size);

                $('#modal-attachment-confirm').modal();

                $('.message-attachment').html(attachment_warning);

                $('#confirm-attachment-warning').click(function () {
                    $('#modal-attachment-confirm').modal('hide');
                    resolve(file);
                });
                $('.cancel-attachment-warning').click(function () {
                    $('#modal-attachment-confirm').modal('hide');
                    reject(false);
                });

            } else {
                resolve(file);
            }

        });
    }
    var validateUpload = async (files) => {

        const status = await Promise.all(files.map(file => validateUploadFile(file))).then(response => {
            return response;
        }).catch(e => {
            return false
        });

        return status;

    }

    function startUpload(files) {
        let formData = new FormData();
        formData.append('model', '{{ addslashes($model) }}');
        formData.append('model_id', '{{ $modelId }}');
        for (let cnt = 0; cnt < files.length; cnt++) {
            formData.append('attachments[]', files[cnt])
        }

        $('#input-attachments').attr('disabled', 'disabled');
        resetProgressBar('0%', '0%');
        $('#attachment-upload-progress').show();

        $.ajax({
            url: '{{ route('attachments.ajax.upload') }}',
            type: 'POST',
            data: formData,
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', progress, false);
                }
                return myXhr;
            },
            cache: false,
            contentType: false,
            processData: false,
            success: function () {
                $('#input-attachments').val('');
                $('#attachments-list').load("{{ route('attachments.ajax.list') }}", {
                    model: '{{ addslashes($model) }}',
                    model_id: '{{ $modelId }}'
                });
                $('#input-attachments').removeAttr('disabled');
                let attachmentCount = Number($('#attachments-list table tr').length);

                if (0 < attachmentCount) {
                    $('.attachment-count').html(Number(attachmentCount)).show().removeClass('hide');
                } else {
                    $('.attachment-count').html('').hide().addClass('hide');
                }
            },
            error: function (XMLHttpRequest, textStatus, error) {
                if (XMLHttpRequest.status == 422) {
                    $("#attachment-upload-progress-bar").addClass('bg-danger').html(XMLHttpRequest.responseJSON.message);
                } else {
                    $("#attachment-upload-progress-bar").addClass('bg-danger').html(error);
                }
                $('#input-attachments').removeAttr('disabled');
            }
        });
    }

    function progress(e) {
        if (e.lengthComputable) {
            var max = e.total;
            var current = e.loaded;
            var percentage = Math.round((current * 100) / max);
            $("#attachment-upload-progress-bar").css("width", percentage + '%').html(percentage + '%');

            if (percentage == 100) {
                resetProgressBar('100%', '{{ trans('fi.complete') }}');
                $('#attachment-upload-progress-bar').addClass('bg-success').html('{{ trans('fi.complete') }}');
            }
        }
    }

    function resetProgressBar(width, text) {
        $('#attachment-upload-progress-bar')
            .removeClass('bg-danger')
            .removeClass('bg-success')
            .css('width', width)
            .html(text);
    }

    $(function () {
        $('.attachment-count').html("{{$object->attachments()->count()}}")
        $("<style>").text(".ajs-header{ background-color: #ba0606 !important; }").appendTo($("body"));

        $('.btn-delete-attachment').click(function () {
            var attachment_id = $(this).data('attachment-id');

            $(this).addClass('delete-attachments-active');

            $('#modal-placeholder').load('{!! route('attachments.delete.modal') !!}', {
                    action: "{{ route('attachments.ajax.delete') }}",
                    modalName: 'attachments',
                    isReload: false,
                    returnURL: null,
                    model: '{{ addslashes($model) }}',
                    model_id: '{{ $object->id }}',
                    client_id: '{{ ($object->getTable() == 'clients') ?  $object->id :$object->client_id }}',
                    attachment_id: attachment_id
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );

        });

        $('.client-visibility').change(function () {
            $.post('{{ route('attachments.ajax.access.update') }}', {
                client_visibility: $(this).val(),
                attachment_id: $(this).data('attachment-id')
            });
        });

        $('#btn-file-upload').click(function () {
            $('#input-attachments').trigger('click');
        });

        $('#input-attachments').change(async function () {
            let files = this.files;
            let selectedFiles = [];
            for (var i = 0; i < files.length; i++) {
                selectedFiles.push(files[i]);
            }
            let passedFiles = await validateUpload(selectedFiles);

            if (passedFiles && Array.isArray(passedFiles))
            {
                startUpload(passedFiles);
            }
        });
    });
</script>

<div id="attachments-list">

    <div class="modal fade show" id="modal-attachment-confirm" data-keyboard="false" data-backdrop="static"
         style="padding-right: 15px;">
        <div class="modal-dialog text-break">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title"> Are you sure? </h5>
                    <button type="button" class="close close-hide cancel-attachment-warning" data-dismiss="modal"
                            aria-hidden="true">×
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 message-attachment">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer pb-1 pt-1">
                    <div class="col-sm-12">
                        <button type="button" id="confirm-attachment-warning"
                                class="btn btn-sm btn-outline-danger float-right ml-2 confirm-attachment-warning">
                            {{trans('fi.ok')}}
                        </button>
                        <button type="button" id="cancel-attachment-warning"
                                class="cancel-attachment-warning btn btn-sm btn-outline-secondary float-right close-hide">
                            {{trans('fi.cancel')}}
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">

        @if (!config('app.demo'))
            @can('attachments.create')
                <div class="card-header">
                    <small>{{trans('fi.note')}}:&nbsp;{{trans('fi.attachment_notice',['size' => '8MB'])}}</small>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" name="form-attachments" id="form-attachments">
                        <input type="file" name="attachments[]" id="input-attachments" multiple="true"
                               style="display: none;">
                    </form>

                    <div class="drag-area" id="limit_drop_zone" ondrop="dropHandler(event);"
                         ondragover="dragOverHandler(event);">
                        <div class="icon"><i class="fa fa-upload fa-xs"></i></div>
                        <header>{{ trans('fi.drag_drop_file') }}</header>
                        <span>{{ trans('fi.or') }}</span>
                        <button id="btn-file-upload" class="btn btn-sm btn-info">{{ trans('fi.browse_file') }}</button>
                    </div>
                    <div style="display: none;" id="attachment-upload-progress">
                        <p class="text-bold">{{ trans('fi.upload_progress') }}</p>

                        <div class="progress progress-sm active">
                            <div id="attachment-upload-progress-bar"
                                 class="progress-bar bg-success progress-bar-striped" role="progressbar"
                                 style="width: 0;">
                                0%
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        @else
            <div class="card-body">
                <a href="javascript:void(0)"
                   class="btn btn-sm btn-primary">{{ trans('fi.demo_file_attachment_disabled') }}</a>
            </div>
        @endif
        <div class="card-footer">
            <table class="table table-hover table-striped table-sm table-responsive-sm table-responsive-xs">
                <thead>
                <tr>
                    <th class="col-md-10">{{ trans('fi.attachment') }}</th>
                    @if(!$object instanceof FI\Modules\TaskList\Models\Task)
                        <th class="col-md-2">{{ trans('fi.client_visibility') }}</th>
                    @endif
                    <th class="text-right col-md-1">{{trans('fi.action')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($object->attachments()->orderBy('created_at', 'desc')->get() as $attachment)
                    <tr>
                        <td><a href="{{ $attachment->download_url }}">{{ $attachment->filename }}</a></td>
                        @if(!$object instanceof FI\Modules\TaskList\Models\Task)
                            <td>
                                {!! Form::select('', $object->attachment_permission_options, $attachment->client_visibility, ['class' => 'form-control form-control-sm client-visibility form-control-sm', 'data-attachment-id' => $attachment->id, 'style'=>'width: 166px;']) !!}
                            </td>
                        @endif
                        <td class="text-right">
                            @can('attachments.delete')
                                <a class="btn btn-sm btn-danger btn-delete-attachment" href="javascript:void(0);"
                                   title="{{ trans('fi.delete') }}" data-attachment-id="{{ $attachment->id }}">
                                    <i class="fa fa-times"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>