@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1>Language Checker</h1>
    </section>

	<section class="content">

		<div class="row">

			<div class="col-xs-12">

				<div class="box box-primary">

					<div class="box-body">

                        <textarea class="form-control" style="height: 250px;">
                            @foreach ($validStrings as $validString)
                                '{{ $validString['key'] }}' => '{{ str_replace("'", "\\'", $validString['string']) }}',
                            @endforeach
                        </textarea>

                        <p>{{ $invalidStrings->count() }} strings removed:</p>

                        @foreach ($invalidStrings as $invalidString)
                            {{ $invalidString['key'] }}<br>
                        @endforeach

					</div>

				</div>

			</div>

		</div>

	</section>

@stop