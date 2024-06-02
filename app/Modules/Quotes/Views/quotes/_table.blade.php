<table class="table table-hover table-striped table-sm text-nowrap">

<thead>
    <tr>
        @if(isset($bulk_action) && $bulk_action == true)
            <th>
                <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
            </th>
        @endif

        @if($quoteColumnSettings != '' && $quoteColumnSettings != null)
            @foreach($defaultQuoteSequenceColumnsData as $defaultKey => $defaultValue)
                @foreach($quoteColumnSettings as $key => $value)
                    @if($defaultKey == $key)
                        @if($value[0] == $defaultValue[0])
                            @if($defaultKey != 'client')
                                <th class="{!!  $defaultValue[2] ? $defaultQuoteSequenceColumnsData[$key][2] : ''!!}">
                                    @if($defaultValue[1] == 'sortable')
                                        {!! Sortable::link($defaultValue[3], trans('fi.'.$key), 'quotes') !!}
                                    @else
                                        {{ trans('fi.'.$key) }}
                                    @endif
                                </th>
                            @else
                                @if(!isset($client_view))
                                    <th>{!! Sortable::link('clients.name', trans('fi.client'), 'quotes') !!}</th>
                                @endif
                            @endif
                        @endif
                    @endif
                @endforeach
            @endforeach
        @endif

        <th class="text-right">{{ trans('fi.options') }}</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($quotes as $quote)
        <tr>
            @if(isset($bulk_action) && $bulk_action == true)
                <td><input type="checkbox" class="bulk-record" data-id="{{ $quote->id }}"></td>
            @endif

            @foreach($defaultQuoteSequenceColumnsData as $defaultKey => $defaultValue)
                @if($defaultKey == 'status' && $defaultQuoteSequenceColumnsData['status'][0] == $quoteColumnSettings['status'][0])
                    <td>
                        <span class="badge badge-{{ $quote->status }}">{{ trans('fi.' . $quote->status) }}</span>
                        @if ($quote->viewed)
                            <span class="badge badge-success">{{ trans('fi.viewed') }}</span>
                        @endif
                    </td>
                @endif
                @if($defaultKey == 'quote' && $defaultQuoteSequenceColumnsData['quote'][0] == $quoteColumnSettings['quote'][0])
                    <td>
                        @can('quotes.update')
                            <a href="{{ route('quotes.edit', [$quote->id]) }}"
                               title="{{ trans('fi.edit') }}">{{ $quote->number }}</a>
                        @else
                            {{ $quote->number }}
                        @endcan
                    </td>
                @endif
                @if($defaultKey == 'date' && $defaultQuoteSequenceColumnsData['date'][0] == $quoteColumnSettings['date'][0])
                    <td>{{ $quote->formatted_quote_date }}</td>

                @endif
                @if($defaultKey == 'expires' && $defaultQuoteSequenceColumnsData['expires'][0] == $quoteColumnSettings['expires'][0])
                    <td>{{ $quote->formatted_expires_at }}</td>

                @endif
                @if($defaultKey == 'client' && $defaultQuoteSequenceColumnsData['client'][0] == $quoteColumnSettings['client'][0])
                    @if(!isset($client_view))
                        <td>
                            @can('clients.view')
                                <a href="{{ route('clients.show', [$quote->client->id]) }}"
                                   title="{{ trans('fi.view_client') }}">{{ $quote->client->name }}</a>
                            @else
                                {{ $quote->client->name }}
                            @endcan
                        </td>
                    @endif
                @endif
                @if($defaultKey == 'summary' && $defaultQuoteSequenceColumnsData['summary'][0] == $quoteColumnSettings['summary'][0])
                    <td>{{ $quote->short_summary }}</td>
                @endif
                @if($defaultKey == 'total' && $defaultQuoteSequenceColumnsData['total'][0] == $quoteColumnSettings['total'][0])
                    <td class="pr-4 text-right">{{ $quote->amount->formatted_total }}</td>
                @endif
                @if($defaultKey == 'invoiced' && $defaultQuoteSequenceColumnsData['invoiced'][0] == $quoteColumnSettings['invoiced'][0])
                    <td class="text-center ">
                        @if ($quote->invoice)
                            <a href="{{ route('invoices.edit', [$quote->invoice_id]) }}">{{ trans('fi.yes') }}</a>
                        @else
                            {{ trans('fi.no') }}
                        @endif
                    </td>
                @endif
            @endforeach

            <td class="text-right">
                <div class="btn-group action-menu">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                        {{ trans('fi.options') }} <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @can('quotes.update')
                            <a class="dropdown-item" href="{{ route('quotes.edit', [$quote->id]) }}">
                                <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                            </a>
                        @endcan
                        <a class="dropdown-item" href="{{ route('quotes.pdf', [$quote->id]) }}" target="_blank"
                           id="btn-pdf-quote">
                            <i class="fa fa-file-pdf"></i> {{ trans('fi.pdf') }}
                        </a>
                        <a class="dropdown-item btn-print-quote" href="javascript:void(0);"
                           data-action="{{ route('quotes.save.pdf', [$quote->id]) }}">
                            <i class="fa fa-print"></i> {{ trans('fi.print') }}
                        </a>
                        <a href="javascript:void(0)" class="email-quote dropdown-item" data-quote-id="{{ $quote->id }}"
                           data-redirect-to="{{ request()->fullUrl() }}">
                            <i class="fa fa-envelope"></i> {{ trans('fi.email') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('clientCenter.public.quote.show', [$quote->url_key, $quote->token]) }}"
                           target="_blank"
                           id="btn-public-quote">
                            <i class="fa fa-globe"></i> {{ trans('fi.public') }}
                        </a>
                        @can('quotes.delete')
                            <div class="dropdown-divider"></div>
                            <a href="#" data-action="{{ route('quotes.delete', [$quote->id]) }}"
                               class="delete-quote text-danger dropdown-item">
                                <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>

    @if(!isset($client_view))
        @foreach($totalAndBalance as $value)
            <tr class="w-100">
                @if($defaultQuoteSequenceColumnsData['total'][0] == $quoteColumnSettings['total'][0])
                    @if($value['index'] == 0)
                        <td class="text-right text-bold"
                            rowspan="{{count($totalAndBalance)}}"
                            colspan="{{$columnIndex}}">{{trans('fi.page_totals')}}</td>
                    @endif
                @endif

                @if($defaultQuoteSequenceColumnsData['total'][0] == $quoteColumnSettings['total'][0])
                    <td class="text-right {{($value['index'] != 0) ? 'border-top-0' : ''}} pr-4">
                        <strong>{{ $value['total']}}</strong></td>
                @endif

                <td class="{{($value['index'] != 0 || $columnIndex == 0)  ? 'border-top-0' : ''}} "></td>
            </tr>
        @endforeach
    @endif
    </tfoot>
</table>