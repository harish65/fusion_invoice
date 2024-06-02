<style>
    .timeline-scroller::-webkit-scrollbar {
        height: 6px;
        width: 6px;
        cursor: e-resize;
    }

    .timeline-scroller::-webkit-scrollbar-thumb {
        border-radius: 10px;
        background-color: #fff;
        -webkit-box-shadow: inset 0 0 6px rgb(206 212 218);
    }

    @media screen and (max-width: 320px) {
        .media-query-time {
            float: none !important;
        }
    }
</style>
<script type="text/javascript">
    $(function () {
        $('.transition-count').html("{{$transitions->total()}}");

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
            <div class="timeline mb-0 small timeline-scroller overflow-auto text-nowrap" style="max-height: 500px;">
                @foreach($monthWiseTransitions as $month=>$monthTransitionGroup)
                    <div class="time-label">
                      <span class="bg-purple">
                        {{$month}}
                      </span>
                    </div>
                    @foreach($monthTransitionGroup as $transition)
                        <div>
                            <div title="{{$transition->user->name}}" class="fas">
                                {!! $transition->user != null ? $transition->user->getAvatar(30) : '' !!}
                            </div>

                            <div class="timeline-item text-wrap">
                                <span class="time media-query-time"
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
                                                <span class="badge badge-default">{{ $noteTag->tag->name }}</span>
                                            @endforeach
                                        @endif
                                     </span>
                                </h3>

                                <div class="timeline-body text-wrap">
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
                                    {!! $transition->transition_entity !!}
                                </div>

                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
            <div class="pull-left pl-2">
                @if(request('custom_search'))
                    <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $transitions->total(),'plural' => $transitions->total() > 1 ? 's' : '']) }}
                    <button type="button" class="btn btn-sm btn-link"
                            id="btn-clear-transition-filter">{{ trans('fi.clear') }}</button>
                @endif
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="float-right mt-3 pr-1 pagination-nav-css" id="transitions-pagination" >
                        {{ $transitions->onEachSide(0)->links() }}
                    </div>
                </div>

            </div>

        @else
            <p class="text-center mt-5 mb-5">{{ trans('fi.no_records_found') }}</p>
        @endif
    </div>
</div>


