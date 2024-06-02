<div class="table-responsive">
    <table class="table table-hover table-striped table-responsive-sm table-responsive-xs">
        <thead>
        <tr>
            <th>{{ trans('fi.status') }}</th>
            <th>{{ trans('fi.invoice') }}</th>
            <th>{{ trans('fi.date') }}</th>
            <th>{{ trans('fi.due') }}</th>
            <th>{{ trans('fi.summary') }}</th>
            <th>{{ trans('fi.total') }}</th>
            <th>{{ trans('fi.balance') }}</th>
            <th class="text-right">{{ trans('fi.options') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($invoices as $invoice)
            <tr>
                <td>
                    <span class="label label-{{ $invoice->status }}">{{ trans('fi.' . $invoice->status) }}</span>
                    @if ($invoice->viewed)
                        <span class="badge badge-success">{{ trans('fi.viewed') }}</span>
                    @endif
                </td>
                <td><a href="{{ route('clientCenter.public.invoice.show', [$invoice->url_key, $invoice->token]) }}" target="_blank">{{ $invoice->number }}</a></td>
                <td>{{ $invoice->formatted_created_at }}</td>
                <td>{{ $invoice->formatted_due_at }}</td>
                <td>{{ $invoice->summary }}</td>
                <td>{{ $invoice->amount->formatted_total }}</td>
                <td>{{ $invoice->amount->formatted_balance }}</td>
                <td class="text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.options') }} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('clientCenter.public.invoice.pdf', [$invoice->url_key]) }}" target="_blank"><i class="fa fa-print"></i> {{ trans('fi.pdf') }}</a>
                            <a class="dropdown-item" href="{{ route('clientCenter.public.invoice.show', [$invoice->url_key, $invoice->token]) }}" target="_blank"><i class="fa fa-search"></i> {{ trans('fi.view') }}</a>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>