@section('javascript')
    @parent
    <script type="text/javascript">
        $(function () {

            updatePDFOptions();

            $('#pdfDriver').change(function () {
                updatePDFOptions();
            });

            function updatePDFOptions() {

                $('.wkhtmltopdf-option').hide();

                pdfDriver = $('#pdfDriver').val();

                if (pdfDriver == 'wkhtmltopdf') {
                    $('.wkhtmltopdf-option').show();
                }
            }

        });
    </script>
@stop

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ trans('fi.paper_size') }}: </label>
            {!! Form::select('setting[paperSize]', $paperSizes, config('fi.paperSize'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ trans('fi.paper_orientation') }}: </label>
            {!! Form::select('setting[paperOrientation]', $paperOrientations, config('fi.paperOrientation'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

</div>

<div class="form-group">
    <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_pdf_driver') !!}">
        {{ trans('fi.pdf_driver') }}: </label>
    {!! Form::select('setting[pdfDriver]', $pdfDrivers, config('fi.pdfDriver'), ['id' => 'pdfDriver', 'class' => 'form-control form-control-sm']) !!}
</div>

<div class="form-group wkhtmltopdf-option">
    <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_pdf_binary_path') !!}">
        {{ trans('fi.binary_path') }}: </label>
    {!! Form::text('setting[pdfBinaryPath]', config('fi.pdfBinaryPath'), ['class' => 'form-control form-control-sm']) !!}
</div>