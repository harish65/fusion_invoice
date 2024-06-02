<script type="text/javascript">
    $(function () {
        $('.note-collapsed').click(function () {
            if ($(this).attr('aria-expanded') == 'false') {
                var text = '{{ trans("fi.show_less") }}';
            } else {
                var text = '{{ trans("fi.show_more") }}';
            }
            $(this).text(text);
        });
    })
</script>
<div class="row">
    <div class="col-md-12">
        @if($transitions->total() > 0)
            <div class="timeline small">
                @foreach($monthWiseTransitions as $month=>$monthTransitionGroup)
                    <div class="time-label">
                  <span class="bg-green">
                    {{$month}}
                  </span>
                    </div>
                    @foreach($monthTransitionGroup as $transition)
                        <div>
                            <div title="{{ $transition->user != null ? $transition->user->name : '' }}" class="fas">
                                {!! $transition->user != null ? $transition->user->getAvatar(30) : '' !!}
                            </div>

                            <div class="timeline-item">
                                <span class="time"
                                      style="color: {{$transition->formatted_action_type == 'Deleted' ? '#f17c7c' : '#999'}}">
                                    <span class="pr-1">
                                        {{ $transition->formatted_action_type }}
                                        @if($transition->formatted_action_count > 0)
                                            ({{ $transition->formatted_action_count }} {{trans('fi.times')}})
                                        @endif
                                    </span>                                
                                    <i class="fa fa-clock"></i>
                                    <span title="{!! $transition->formatted_created_at_system_format !!}">
                                        {!! $transition->formatted_created_at !!}
                                    </span>
                                </span>

                                <h3 class="timeline-header">
                                    <span class="title">
                                        <i class="{{$transition->transition_entity_icon}}"></i>
                                        <span>{{$transition->transition_entity_name}}</span>
                                        @if($transition->transitionable_type === "FI\Modules\Notes\Models\Note" && isset($transition->transitionable->tags))
                                            @foreach($transition->transitionable->tags as $noteTag)
                                                <span class="badge badge-default">
                                               {{ $noteTag->tag->name }}
                                           </span>
                                            @endforeach
                                        @endif
                                    </span>
                                    <span style="padding-left: 10px; font-size: .8em;">
                                        @if($transition->transitionable_type === "Addons\Commission\Models\InvoiceItemCommission" || $transition->transitionable_type === "Addons\Commission\Models\RecurringInvoiceItemCommission")
                                            {{$transition->sales_person_name}}
                                        @else
                                            @if($transition->client)
                                                <a href="{{route('clients.show', [$transition->client->id])}}">
                                                    {{$transition->client->name}}
                                                </a>
                                            @elseif($transition->transitionable_type === 'FI\Modules\Clients\Models\Client' && !$transition->client)
                                                <i class="fa fa-ban" title="{{trans('fi.client_deleted')}}"></i>
                                            @endif
                                        @endif
                                    </span>
                                </h3>

                                <div class="timeline-body">
                                    @php $breakLine = false;@endphp

                                    @if($transition->transitionable_type === "FI\Modules\Notes\Models\Note")
                                        @if($note = $transition->transitionable)
                                            @if($notable = $note->notable)
                                                @if(get_class($notable) === "FI\Modules\Invoices\Models\Invoice")
                                                    {{trans('fi.invoice')}}:&nbsp;<a
                                                            href="{{route('invoices.edit',[$notable->id])}}">{{'#'.$notable->number}}</a>
                                                @endif
                                                @if(get_class($notable) === "FI\Modules\Quotes\Models\Quote")
                                                    {{trans('fi.quote')}}:&nbsp;<a
                                                            href="{{route('quotes.edit',[$notable->id])}}">{{'#'.$notable->number}}</a>
                                                @endif
                                                @if(get_class($notable) === "FI\Modules\Clients\Models\Client")
                                                    {{trans('fi.client')}}:&nbsp;<a
                                                            href="{{route('clients.show',[$notable->id])}}">{{'#'.$notable->name}}</a>
                                                @endif
                                                @if(get_class($notable) === "FI\Modules\TaskList\Models\Task")
                                                    {{trans('fi.task')}}:&nbsp;<a
                                                            href="{{route('task.show',[$notable->id])}}">{{'#'.$notable->title}}</a>
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                    @if($breakLine)
                                        <br/>
                                    @endif
                                    {!! $transition->transition_entity !!}
                                </div>

                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        @else
            <p class="text-center" style="margin: 115px 0px;">{{ trans('fi.no_records_found') }}</p>
        @endif
    </div>
</div>
<div class="row">
    @if($transitions->total() > 0)
        <div class="col-sm-12 col-md-5 mt-3">
            @if(request('search'))
                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $transitions->total(),'plural' => $transitions->total() > 1 ? 's' : '']) }}
                <button type="button" class="btn btn-sm btn-link" id="btn-clear-notes-filter">
                    {{ trans('fi.clear') }}
                </button>
            @endif
        </div>
        <div class="col-sm-12 col-md-7 pull-right">

        </div>
        <div class="col-12 pull-right">
            <div class="transitions-pages float-right pagination-nav-css">
                {{ $transitions->onEachSide(0)->links() }}
            </div>
        </div>
    @endif
</div>
