@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1>Pending Clients</h1>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body no-padding">
                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>Created</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zip</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($clients as $client)
                                <tr>

                                    <td>{{ $client->formatted_created_at }}</td>
                                    <td>{{ $client->name }}</td>
                                    <td>{{ $client->address }}</td>
                                    <td>{{ $client->city }}</td>
                                    <td>{{ $client->state }}</td>
                                    <td>{{ $client->zip }}</td>
                                    <td>{{ $client->email }}</td>
                                    <td>{{ $client->phone }}</td>
                                    <td>
                                        <a href="{{ route('pendingClients.keep', [$client->client_id]) }}" class="btn btn-sm btn-primary">Keep</a>
                                        <a href="{{ route('pendingClients.delete', [$client->client_id]) }}" class="btn btn-sm btn-danger">Remove</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>

        </div>

    </section>

@stop