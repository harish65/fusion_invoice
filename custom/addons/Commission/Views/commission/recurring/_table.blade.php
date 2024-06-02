<table class="table table-hover table-striped table-responsive-sm table-sm">
    <thead>
    <tr>
        <th>{{ trans('Commission::lang.user') }}</th>
        <th>{{ trans('Commission::lang.commission_type') }}</th>
        <th>{{ trans('Commission::lang.product') }}</th>
        <th>{{ trans('Commission::lang.sub_total')  }}</th>
        <th>{{ trans('Commission::lang.commission') }}</th>
        <th>{{ trans('Commission::lang.note') }}</th>
        <th>{{ trans('Commission::lang.stop_date') }}</th>
        <th>{{ trans('fi.options') }}</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($invoiceItems as $items)
        @foreach ($items->commissions as $commission)
            <tr>
                <td>
                    <a href="{{ route('users.edit', [$commission->user->id, $commission->user->user_type]) }}">
                        {{ $commission->user->name}}
                    </a>
                </td>
                <td>{{ $commission->type->name}}</td>
                <td>{{ $items->name}}</td>
                <td>{{ $items->amount->formatted_subtotal}}</td>
                <td>{{ $commission->formatted_amount }}</td>
                <td>{{ $commission->note }}</td>
                <td>{{ $commission->stop_date}}</td>
                <td>
                    @if(Gate::check('commission.update') || Gate::check('commission.delete'))
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                {{ trans('fi.options') }} <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('commission.update')
                                <a class="dropdown-item edit-commission"
                                   href="{{ route('recurring.invoice.commission.edit', [$commission->id,$items->recurringInvoice->id]) }}"><i
                                            class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                @endcan
                                @can('commission.delete')
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item commission-delete text-danger"
                                   data-action="{{ route('recurring.invoice.commission.delete', [$commission->id]) }}"><i
                                            class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>
                                @endcan
                            </div>
                        </div>
                    @endif
                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
