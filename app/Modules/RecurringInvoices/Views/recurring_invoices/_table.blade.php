<table class="table table-hover table-striped table-sm table-responsive-sm table-responsive-xs text-center">
    <thead>
    <tr>

        @if($recurringInvoiceColumnSettings != '' && $recurringInvoiceColumnSettings != null)
            @foreach($defaultRecurringInvoiceSequenceColumnsData as $defaultKey => $defaultValue)
                @foreach($recurringInvoiceColumnSettings as $key => $value)
                    @if($defaultKey == $key)
                        @if($value[0] == $defaultValue[0])
                            @if($defaultKey != 'client')
                                <th class="{!!  $defaultValue[2] ? $defaultRecurringInvoiceSequenceColumnsData[$key][2] : ''!!}">
                                    @if($defaultValue[1] == 'sortable')
                                        {!! Sortable::link($defaultValue[3], trans('fi.'.$key), 'recurring_invoices') !!}
                                    @else
                                        {{ trans('fi.'.$key) }}
                                    @endif
                                </th>
                            @else
                                @if(!isset($client_view))
                                    <th>{!! Sortable::link('clients.name', trans('fi.client'), 'recurring_invoices') !!}</th>
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
    @foreach ($recurringInvoices as $recurringInvoice)
        <tr>
            @foreach($defaultRecurringInvoiceSequenceColumnsData as $defaultKey => $defaultValue)
                @if($defaultKey == 'id' && $defaultRecurringInvoiceSequenceColumnsData['id'][0] == $recurringInvoiceColumnSettings['id'][0])
                    <td>
                        @can('recurring_invoices.update')
                            <a href="{{ route('recurringInvoices.edit', [$recurringInvoice->id]) }}"
                               title="{{ trans('fi.edit') }}">{{ $recurringInvoice->id }}</a>
                        @else
                            {{ $recurringInvoice->id }}
                        @endcan
                    </td>
                @endif
                @if($defaultKey == 'client' && $defaultRecurringInvoiceSequenceColumnsData['client'][0] == $recurringInvoiceColumnSettings['client'][0])
                    @if(!isset($client_view))
                        <td>
                            @can('clients.view')
                                <a href="{{ route('clients.show', [$recurringInvoice->client->id]) }}"
                                   title="{{ trans('fi.view_client') }}">{{ $recurringInvoice->client->name }}</a>
                            @else
                                {{ $recurringInvoice->client->name }}
                            @endcan
                        </td>
                    @endif
                @endif
                @if($defaultKey == 'summary' && $defaultRecurringInvoiceSequenceColumnsData['summary'][0] == $recurringInvoiceColumnSettings['summary'][0])
                    <td>{{ $recurringInvoice->short_summary }}</td>
                @endif
                @if($defaultKey == 'next_date' && $defaultRecurringInvoiceSequenceColumnsData['next_date'][0] == $recurringInvoiceColumnSettings['next_date'][0])
                    <td>{{ $recurringInvoice->formatted_next_date }}</td>
                @endif
                @if($defaultKey == 'stop_date' && $defaultRecurringInvoiceSequenceColumnsData['stop_date'][0] == $recurringInvoiceColumnSettings['stop_date'][0])
                    <td>{{ $recurringInvoice->formatted_stop_date }}</td>
                @endif
                @if($defaultKey == 'every' && $defaultRecurringInvoiceSequenceColumnsData['every'][0] == $recurringInvoiceColumnSettings['every'][0])
                    <td>{{ $recurringInvoice->recurring_frequency . ' ' . $frequencies[$recurringInvoice->recurring_period] }}</td>

                @endif
                @if($defaultKey == 'tags' && $defaultRecurringInvoiceSequenceColumnsData['tags'][0] == $recurringInvoiceColumnSettings['tags'][0])
                    <td>{{ $recurringInvoice->formatted_tags }}</td>
                @endif
                @if($defaultKey == 'total' && $defaultRecurringInvoiceSequenceColumnsData['total'][0] == $recurringInvoiceColumnSettings['total'][0])
                    <td class="pr-4 text-right">{{ $recurringInvoice->amount->formatted_total }}</td>
                @endif

            @endforeach


            <td class="text-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                        {{ trans('fi.options') }} <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @can('recurring_invoices.update')
                            <a class="dropdown-item"
                               href="{{ route('recurringInvoices.edit', [$recurringInvoice->id]) }}">
                                <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                            </a>
                        @endcan
                        @can('recurring_invoices.create')
                            <a class="dropdown-item btn-copy-recurring-invoice" href="javascript:void(0);"
                               data-recurring-invoice-id="{{$recurringInvoice->id}}">
                                <i class="fa fa-copy"></i> {{ trans('fi.copy') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" id="btn-create-live-invoice"
                               data-id="{{$recurringInvoice->id}}">
                                <i class="fa fa-retweet"></i> {{ trans('fi.create_live_invoice') }}
                            </a>
                        @endcan
                        @can('recurring_invoices.delete')
                            <div class="dropdown-divider"></div>
                            <a href="#" data-action="{{ route('recurringInvoices.delete', [$recurringInvoice->id]) }}"
                               class="delete-recurring-invoice text-danger dropdown-item">
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

                @if(($defaultRecurringInvoiceSequenceColumnsData['total'][0] == $recurringInvoiceColumnSettings['total'][0]))
                    @if($value['index'] == 0)
                        <td class="text-right text-bold"
                            style="{{( count($totalAndBalance) != 1 ) ? 'padding-top:15px;padding-right:95px;' : ''}} "
                            rowspan="{{count($totalAndBalance)}}"
                            colspan="{{$columnIndex - 1 }}">{{trans('fi.page_totals')}}</td>
                    @endif
                @endif

                @if($defaultRecurringInvoiceSequenceColumnsData['total'][0] == $recurringInvoiceColumnSettings['total'][0])
                    <td class="text-right {{($value['index'] != 0) ? 'border-top-0' : ''}} pr-4">
                        <strong>{{ $value['total']}}</strong></td>
                @endif

                <td class="{{($value['index'] != 0 || $columnIndex == 0)  ? 'border-top-0' : ''}} "></td>
            </tr>
        @endforeach
    @endif
    </tfoot>
</table>