@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('SimpleTodo::translations.tasks') }}
        </h1>

        <div class="pull-right">
            <a href="{{ route('simpleTodo.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ trans('fi.new') }}</a>
        </div>
        <div class="clearfix"></div>
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
									<th>{{ trans('fi.start_date') }}</th>
									<th>{{ trans('SimpleTodo::translations.title') }}</th>
									<th>{{ trans('fi.due_date') }}</th>
									<th>{{ trans('fi.options') }}</th>
								</tr>
							</thead>

							<tbody>
								@foreach ($tasks as $task)
								<tr>
									<td>{{ $task->start_at }}</td>
									<td>{{ $task->title }}</td>
                                    <td>{{ $task->due_at }}</td>
									<td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                                {{ trans('fi.options') }} <span class="caret"></span>
                                            </button>
											<ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('simpleTodo.edit', [$task->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                                                <li><a href="{{ route('simpleTodo.delete', [$task->id]) }}" onclick="return confirm('{{ trans('fi.delete_record_warning') }}');"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}</a></li>
                                            </ul>
                                        </div>
									</td>
								</tr>
								@endforeach
							</tbody>

						</table>
					</div>

				</div>

				<div class="pull-right">
					{!! $tasks->appends(request()->except('page'))->render() !!}
				</div>

			</div>

		</div>

	</section>

@stop