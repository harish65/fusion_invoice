<table class="table table-hover table-striped table-sm">
    <thead>
    <tr>
        <th>{{ trans('Commission::lang.user') }}</th>
        <th>{{ trans('Commission::lang.commission_type') }}</th>
        <th>{{ trans('Commission::lang.product') }}</th>
        <th>{{ trans('Commission::lang.sub_total')  }}</th>
        <th>{{ trans('Commission::lang.commission') }}</th>
        <th>{{ trans('Commission::lang.note') }}</th>
        <th>{{ trans('Commission::lang.status') }}</th>
        <th class="text-right">{{ trans('fi.options') }}</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($invoiceItems as $items)
        @foreach ($items->commissions as $commission)
            <tr>
                <td>{{ $commission->user->name}}</td>
                <td>{{ $commission->type->name}}</td>
                <td>{{ $items->name}}</td>
                <td>{{ $items->amount->formatted_subtotal}}</td>
                <td>{{ $commission->formatted_amount }}</td>
                <td>{{ $commission->note }}</td>
                <td>{!! $commission->formatted_status !!}</td>
                <td class="float-right">
                    @if(Gate::check('commission.update') || Gate::check('commission.delete'))
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                {{ trans('fi.options') }} <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('commission.update')
                                <a class="dropdown-item edit-commission"
                                       href="{{ route('invoice.commission.edit', [$commission->id,$items->invoice->id]) }}"><i
                                                class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                @endcan
                                @can('commission.delete')
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item commission-delete text-danger"
                                       data-action="{{ route('invoice.commission.delete', [$commission->id]) }}"><i
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
