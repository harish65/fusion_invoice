<script type="text/javascript">
    @if(isset($errors))
        @foreach ($errors->all() as $error)
            alertify.error('{{ $error }}', 10);
        @endforeach
    @endif
</script>


@if (session()->has('error'))
    <script type="text/javascript">
        alertify.error('{{ session('error') }}', 10);
    </script>
@endif

@if (session()->has('alert'))
    <script type="text/javascript">
        alertify.error('{{ session('alert') }}', 10);
    </script>
@endif

@if (session()->has('alertSuccess'))
    <script type="text/javascript">
        alertify.success('{!! session('alertSuccess') !!}', 2.5);
    </script>
@endif

@if (session()->has('errorFolderCreate'))
    @if(isset(session()->get('errorFolderCreate')['create']) && !empty(session()->get('errorFolderCreate')['create']))
        @foreach(session()->get('errorFolderCreate')['create'] as $path)
            <script type="text/javascript">
                alertify.notify('{{trans("fi.create_missing_folder_failed",['path' => addslashes($path)])}}', 'error-lg', 10);
            </script>
        @endforeach
    @endif
    @if(isset(session()->get('errorFolderCreate')['permission']) && !empty(session()->get('errorFolderCreate')['permission']))
        @foreach(session()->get('errorFolderCreate')['permission'] as $path)
            <script type="text/javascript">
                alertify.notify('{{trans("fi.folder_is_not_writable",['path' => addslashes($path)])}}', 'error-lg', 10);
            </script>
        @endforeach
    @endif
@elseif (session()->has('successFolderCreate'))
    <script type="text/javascript">
        alertify.success('{!! session()->get('successFolderCreate')->first() !!}', 2.5);
    </script>
@endif

@if (session()->has('alertInfo'))
    <script type="text/javascript">
        alertify.notify('{!! session('alertInfo') !!}', 5);
    </script>
@endif

@if (session()->has('piracyAlert') && session('piracyAlert') != null)
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-ban"></i> {{ trans('fi.piracy_alert') }}</h5>
        {{ session('piracyAlert') }}
        <a href="https://www.fusioninvoice.com/store" class="btn btn-sm btn-success pull-right text-decoration-none"
                       target="_blank">{{ trans('fi.buy-now') }}</a>
    </div>
@endif