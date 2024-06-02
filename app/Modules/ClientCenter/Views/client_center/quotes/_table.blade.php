<div class="table-responsive">
    <table class="table table-hover table-striped table-responsive-xs table-responsive-sm">
        <thead>
        <tr>
            <th>{{ trans('fi.status') }}</th>
            <th>{{ trans('fi.quote') }}</th>
            <th>{{ trans('fi.date') }}</th>
            <th>{{ trans('fi.expires') }}</th>
            <th>{{ trans('fi.summary') }}</th>
            <th>{{ trans('fi.total') }}</th>
            <th class="text-right">{{ trans('fi.options') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($quotes as $quote)
            <tr>
                <td>
                    <span class="badge badge-{{ $quote->status }}">{{ trans('fi.' . $quote->status) }}</span>
                    @if ($quote->viewed)
                        <span class="badge badge-success">{{ trans('fi.viewed') }}</span>
                    @endif
                </td>
                <td><a href="{{ route('clientCenter.public.quote.show', [$quote->url_key, $quote->token]) }}" target="_blank">{{ $quote->number }}</a></td>
                <td>{{ $quote->formatted_created_at }}</td>
                <td>{{ $quote->formatted_expires_at }}</td>
                <td>{{ $quote->summary }}</td>
                <td>{{ $quote->amount->formatted_total }}</td>
                <td class="text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.options') }} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('clientCenter.public.quote.pdf', [$quote->url_key]) }}" target="_blank"><i class="fa fa-print"></i> {{ trans('fi.pdf') }}</a>
                            <a class="dropdown-item" href="{{ route('clientCenter.public.quote.show', [$quote->url_key, $quote->token]) }}" target="_blank"><i class="fa fa-search"></i> {{ trans('fi.view') }}</a>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>