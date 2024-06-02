@include('invoices._js_edit_to')

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">{{ trans('fi.to') }}</h3>

        <div class="card-tools pull-right">
            @can('clients.view')
            <button  {{$invoiceOverlayStatus ? 'disabled' : null}} class="btn btn-sm btn-default" id="btn-change-client" data-client-id="{{ $invoice->client->id }}">
                <i class="fa fa-exchange"></i> {{ trans('fi.change') }}
            </button>
            @endcan
            @can('clients.update')
            <button  {{$invoiceOverlayStatus ? 'disabled' : null}} class="btn btn-sm btn-default" id="btn-edit-client" data-client-id="{{ $invoice->client->id }}">
                <i class="fa fa-pencil"></i> {{ trans('fi.edit') }}
            </button>
            @endcan
        </div>
    </div>
    <div class="card-body">
        @can('clients.view')
        <a href="{{ route('clients.show', [$invoice->client->id]) }}"
            title="{{ trans('fi.view_client') }}"><strong>{{ $invoice->client->name }}</strong></a><br>
        @else
        <strong>{{ $invoice->client->name }}</strong><br>
        @endcan

        {!! $invoice->client->formatted_address !!}<br>
        {{ trans('fi.phone') }}: {{ $invoice->client->phone }}<br>
        {{ trans('fi.email') }}: {{ $invoice->client->email }}
    </div>
</div>