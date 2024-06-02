@include('quotes._js_edit_to')

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">{{ trans('fi.to') }}</h3>

        <div class="card-tools pull-right">
            <button class="btn btn-sm btn-default" id="btn-change-client" data-client-id="{{ $quote->client->id }}"><i
                        class="fa fa-exchange"></i> {{ trans('fi.change') }}</button>
            <button class="btn btn-sm btn-default" id="btn-edit-client" data-client-id="{{ $quote->client->id }}"><i
                        class="fa fa-pencil"></i> {{ trans('fi.edit') }}</button>
        </div>
    </div>
    <div class="card-body">
        @can('clients.view')
        <a href="{{ route('clients.show', [$quote->client->id]) }}"
           title="{{ trans('fi.view_client') }}"><strong>{{ $quote->client->name }}</strong></a><br>
        @else
            <strong>{{ $quote->client->name }}</strong><br>
            @endcan

            {!! $quote->client->formatted_address !!}<br>
            {{ trans('fi.phone') }}: {{ $quote->client->phone }}<br>
            {{ trans('fi.email') }}: {{ $quote->client->email }}
    </div>
</div>