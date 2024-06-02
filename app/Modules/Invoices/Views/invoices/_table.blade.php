<table class="table table-hover table-striped table-sm text-nowrap">

    <thead>
    <tr>
        @if(isset($bulk_action) && $bulk_action == true)
            <th width="2%">
                <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
            </th>
        @endif

        @if($invoiceColumnSettings != '' && $invoiceColumnSettings != null)
            @foreach($defaultSequenceColumnsData as $defaultKey => $defaultValue)
                @foreach($invoiceColumnSettings as $key => $value)
                    @if($defaultKey == $key)
                        @if($value[0] == $defaultValue[0])
                            @if($defaultKey != 'client')
                                <th class="{!!  $defaultValue[2] ? $defaultSequenceColumnsData[$key][2] : ''!!}">
                                    @if($defaultValue[1] == 'sortable')
                                        {!! Sortable::link($defaultValue[3], trans('fi.'.$key), 'invoices') !!}
                                    @else
                                        {{ trans('fi.'.$key) }}
                                    @endif
                                </th>
                            @else
                                @if(!isset($client_view))
                                    <th>{!! Sortable::link('clients.name', trans('fi.client'), 'invoices') !!}</th>
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
    @foreach ($invoices as $invoice)
        <tr class="{{ $invoice->type == 'credit_memo' ? 'callout-pink-cm' : null }}">
            @if(isset($bulk_action) && $bulk_action == true)
                <td width="2%" class="{{($invoice->type=='credit_memo') ? 'column-credit-memo' : 'm-1'}}">
                    <input type="checkbox" class="bulk-record" data-id="{{ $invoice->id }}">
                </td>
            @endif
            @foreach($defaultSequenceColumnsData as $defaultKey => $defaultValue)

                @if($defaultKey == 'status' && $defaultSequenceColumnsData['status'][0] == $invoiceColumnSettings['status'][0])
                    <td class="{{(isset($client_view) && $invoice->type=='credit_memo') ? 'column-credit-memo' : ''}}">
                        @if(($invoice->status == 'sent') && $invoice->virtual_status != null && (in_array('paid', $invoice->virtual_status )))
                            {{-- Suppress the 'sent' badges if the invoice has been paid --}}
                        @else
                            @if($invoice->status == 'sent' && (in_array('mailed', $invoice->virtual_status) || in_array('emailed', $invoice->virtual_status)))
                                {{-- It would be redundant to show Mailed or Emailed and Sent. Mailed and Emailed are subsets of Sent.  --}}
                            @else
                                @if($invoice->status != '')
                                    <span class="badge badge-{{ $invoice->status }}">{{ trans('fi.' . $invoice->status) }}</span>
                                @endif
                            @endif
                        @endif

                        @if ($invoice->viewed)
                            <span class="badge badge-viewed">{{ trans('fi.viewed') }}</span>
                        @endif

                        @if ($invoice->virtual_status != null)
                            @foreach($invoice->virtual_status as $virtual_status)
                                @if($virtual_status != 'all_statuses' && $virtual_status != 'viewed')
                                    <span class="badge badge-{{ $virtual_status }}">{{ trans('fi.' . $virtual_status) }}</span>
                                @endif
                            @endforeach
                        @endif
                    </td>
                @endif

                @if($defaultKey == 'invoice' && $defaultSequenceColumnsData['invoice'][0] == $invoiceColumnSettings['invoice'][0])
                    <td>
                        @can('invoices.update')
                        <a href="{{ route('invoices.edit', [$invoice->id]) }}"
                           title="{{ trans('fi.edit') }}">{{ $invoice->number }}</a>
                        @else
                            {{ $invoice->number }}
                            @endcan
                    </td>
                @endif
                @if($defaultKey == 'recurring_id'  && $defaultSequenceColumnsData['recurring_id'][0] == $invoiceColumnSettings['recurring_id'][0])
                    @if ($invoice->recurring_invoice_id > 0)
                        <td class="hidden-xs">{{ $invoice->recurring_invoice_id }}</td>
                    @else
                        <td class="hidden-xs">{{''}}</td>
                    @endif
                @endif
                @if($defaultKey == 'date'  && $defaultSequenceColumnsData['date'][0] == $invoiceColumnSettings['date'][0])
                    <td class="hidden-xs">{{ $invoice->formatted_invoice_date }}</td>
                @endif
                @if($defaultKey == 'due'  && $defaultSequenceColumnsData['due'][0] == $invoiceColumnSettings['due'][0])
                    <td class="hidden-md hidden-sm hidden-xs"
                        @if ($invoice->isOverdue) style="color: red; font-weight: bold;" @endif>{{ $invoice->formatted_due_at }}
                    </td>
                @endif
                @if($defaultKey == 'client' && $defaultSequenceColumnsData['client'][0] == $invoiceColumnSettings['client'][0])
                    @if(!isset($client_view))
                        <td>
                            <a href="{{ route('clients.show', [$invoice->client->id]) }}"
                               title="{{ trans('fi.view_client') }}">{{ $invoice->client->name }}</a>
                        </td>
                    @endif
                @endif
                @if($defaultKey == 'summary' && $defaultSequenceColumnsData['summary'][0] == $invoiceColumnSettings['summary'][0])
                    <td>{{ $invoice->short_summary }}</td>
                @endif
                @if($defaultKey == 'tags' && $defaultSequenceColumnsData['tags'][0] == $invoiceColumnSettings['tags'][0])
                    <td>{{ $invoice->formatted_tags }}</td>
                @endif
                @if($defaultKey == 'total' && $defaultSequenceColumnsData['total'][0] == $invoiceColumnSettings['total'][0])
                    <td class="pr-4 text-right">{{ $invoice->amount->formatted_total }}</td>
                @endif
                @if($defaultKey == 'balance' && $defaultSequenceColumnsData['balance'][0] == $invoiceColumnSettings['balance'][0])
                    <td class="pr-4 text-right">{{ $invoice->amount->formatted_balance }}</td>
                @endif
            @endforeach
            <td class="text-right">
                <div class="btn-group action-menu">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                        {{ trans('fi.options') }} <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @can('invoices.update')
                            @if($invoice->status !== 'draft')
                                @if(config('fi.allowEditInvoiceStatus') == 'draft_and_sent' && $invoice->unPaid_status == true)
                                    <a class="dropdown-item text-warning btn-edit-invoice-sent-and-paid"
                                       href="{{ route('invoices.edit', [$invoice->id,'overlay' => 0]) }}"
                                       data-invoice="{{ $invoice->id }}"
                                       data-status="{{ ($invoice->paid_status == true) ? 'paid' : $invoice->status }}">
                                        <i class="fa fa-edit"></i> {{ trans('fi.allow_edit_status_invoice', ['status' => trans('fi.'.$invoice->status)]) }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endif

                                @if(config('fi.allowEditInvoiceStatus') == 'draft_or_sent_and_paid'&& $invoice->type != 'credit_memo')
                                    <a class="dropdown-item text-warning btn-edit-invoice-sent-and-paid"
                                       href="{{ route('invoices.edit', [$invoice->id,'overlay' => 0]) }}"
                                       data-invoice="{{ $invoice->id }}"
                                       data-status="{{ ($invoice->paid_status == true) ? 'paid' : $invoice->status }}">
                                        <i class="fa fa-edit"></i> {{ trans('fi.allow_edit_status_invoice', ['status' => ($invoice->paid_status == true) ? trans('fi.paid') : ucfirst($invoice->status)]) }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endif
                            @else
                                <a class="dropdown-item" href="{{ route('invoices.edit', [$invoice->id]) }}">
                                    <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                                </a>
                           @endif
                        @endcan

                        <a href="javascript:void(0);" class="email-invoice dropdown-item"
                           data-invoice-id="{{ $invoice->id }}"
                           data-redirect-to="{{ request()->fullUrl() }}">
                            <i class="fa fa-envelope"></i> {{ trans('fi.email') }}
                        </a>

                        @if($invoice->client->active != 0)

                            @if($invoice->type == 'invoice' && $invoice->isOverdue)
                                <a href="javascript:void(0);"
                                   data-action="{{ route('invoices.payment-reminder', [$invoice->id]) }}"
                                   data-invoice-id="{{ $invoice->id }}"
                                   class="send-overdue-reminder dropdown-item">
                                    <i class="fa fa-bell"></i> {{ trans('fi.email_overdue_invoice_reminder') }}
                                </a>
                            @endif

                            @if($invoice->type == 'invoice' && ! $invoice->isOverdue && $invoice->unPaid_status == true)
                                <a href="javascript:void(0);"
                                   data-action="{{ route('invoices.payment-notice', [$invoice->id]) }}"
                                   data-invoice-id="{{ $invoice->id }}"
                                   class="send-upcoming-notice dropdown-item">
                                    <i class="fa fa-bell"></i> {{ trans('fi.email_upcoming_invoice_notice') }}
                                </a>
                            @endif

                        @endif

                        <a href="{{ route('clientCenter.public.invoice.show', [$invoice->url_key, $invoice->token]) }}"
                           class="dropdown-item"
                           target="_blank" id="btn-public-invoice">
                            <i class="fa fa-globe"></i> {{ trans('fi.public_link') }}
                        </a>

                        <div class="dropdown-divider"></div>

                        @can('payments.create')
                        @if ($invoice->isPayable)
                            <a href="javascript:void(0);" id="btn-enter-payment" class="enter-payment dropdown-item"
                               data-invoice-id="{{ $invoice->id }}"
                               data-invoice-balance="{{ $invoice->amount->formatted_numeric_balance }}"
                               data-redirect-to="{{ request()->fullUrl() }}">
                                <i class="fa fa-credit-card"></i> {{ trans('fi.enter_payment') }}
                            </a>
                            @if(($invoice->count_credit_memo > 0) && ($invoice->type != 'credit_memo'))
                                <a href="javascript:void(0);" id="btn-apply-credit-memo"
                                   class="apply-credit-memo dropdown-item"
                                   data-invoice-id="{{ $invoice->id }}"
                                   data-invoice-balance="{{ $invoice->amount->formatted_numeric_balance }}"
                                   data-settlement-type="credit_memo"
                                   data-redirect-to="{{ request()->fullUrl() }}">
                                    <i class="fa fa-list-alt"></i> {{ trans('fi.apply_credit_memo') }}
                                </a>
                            @endif
                            @if(($invoice->count_pre_payment > 0) && ($invoice->type != 'credit_memo'))
                                <a href="javascript:void(0);" id="btn-apply-pre-payment"
                                   class="apply-pre-payment dropdown-item"
                                   data-invoice-id="{{ $invoice->id }}"
                                   data-settlement-type="pre_payment"
                                   data-invoice-balance="{{ $invoice->amount->formatted_numeric_balance }}"
                                   data-redirect-to="{{ request()->fullUrl() }}">
                                    <i class="fa fa-money-check-alt"></i> {{ trans('fi.apply_pre_payment') }}
                                </a>
                            @endif
                        @endif

                        @if ($invoice->isApplicable && $invoice->count_sent_invoices > 0)
                            <a href="javascript:void(0);" id="btn-apply-to-invoices"
                               class="apply-to-invoices dropdown-item"
                               data-invoice-id="{{ $invoice->id }}"
                               data-invoice-balance="{{ $invoice->amount->formatted_numeric_balance }}"
                               data-redirect-to="{{ request()->fullUrl() }}">
                                <i class="far fa-hand-point-right"></i> {{ trans('fi.apply_to_invoices') }}
                            </a>
                        @endif
                        @if ($invoice->isPayable || ($invoice->isApplicable && $invoice->count_sent_invoices > 0))
                            <div class="dropdown-divider"></div>
                        @endif
                        @endcan

                        @if($invoice->payments->count() == 0 && $invoice->status == 'sent')
                            <a class="dropdown-item btn-invoice-status-change-to-draft" href="javascript:void(0);"
                               data-action="{{route('invoices.status.changeToDraft',[$invoice->id])}}"
                               id="btn-invoice-status-change-to-draft">
                                <i class="fas fa-exchange-alt"></i> {{ trans('fi.change_to_draft') }}
                            </a>
                        @endif
                        @if (in_array('mailed',$invoice->virtual_status) == true && $invoice->virtual_status != null)
                            <a class="dropdown-item btn-un-mail-invoice" href="javascript:void(0);"
                               data-action="{{ route('invoices.remove.dateMailed', [$invoice->id]) }}"
                               id="btn-un-mail-invoice">
                                <i class="fa fa-share"></i> {{ trans('fi.unmark_mailed') }}
                            </a>
                        @else
                            <a class="dropdown-item btn-mail-invoice" href="javascript:void(0);"
                               data-action="{{ route('invoices.save.dateMailed', [$invoice->id]) }}"
                               id="btn-mail-invoice">
                                <i class="fa fa-reply"></i> {{ trans('fi.mark_as_mailed') }}
                            </a>
                        @endif
                        <div class="dropdown-divider"></div>

                        <a class="btn-pdf-invoice dropdown-item" href="{{ route('invoices.pdf', [$invoice->id]) }}"
                           target="_blank"
                           id="btn-pdf-invoice">
                            <i class="fa fa-file-pdf"></i> {{ trans('fi.pdf') }}
                        </a>
                        <a href="javascript:void(0);" data-action="{{ route('invoices.save.pdf', [$invoice->id]) }}"
                           class="btn-print-invoice dropdown-item">
                            <i class="fa fa-print"></i> {{ trans('fi.print') }}
                        </a>

                        @if (in_array('mailed',$invoice->virtual_status) == false)
                            <a href="javascript:void(0);"
                               data-action="{{ route('invoices.print.pdf.and.mark.as.mailed', [$invoice->id]) }}"
                               class="btn-print-pdf-and-mark-as-mailed-invoice dropdown-item">
                                <i class="fa fa-print"></i> {{ trans('fi.print_mark_mailed') }}
                            </a>
                            <div class="dropdown-divider"></div>
                        @endif

                        @can('invoices.create')
                        <a href="javascript:void(0);" class="btn-copy-invoice dropdown-item"
                           data-invoice-id="{{ $invoice->id }}">
                            <i class="fa fa-copy"></i> {{ trans('fi.copy') }}
                        </a>
                        @endcan
                        @if($invoice->type != 'credit_memo')
                            @can('recurring_invoices.create')
                            <a href="javascript:void(0);" class="btn-copy-recurring-invoice dropdown-item"
                               data-invoice-id="{{ $invoice->id }}">
                                <i class="fa fa-copy"></i> {{ trans('fi.copy_to_recurring_invoice') }}
                            </a>
                            @endcan
                        @endif

                        @if($invoice->type == 'invoice' && $invoice->status != 'canceled' && ($invoice->unPaid_status == true && $invoice->amount->balance == $invoice->amount->total))
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);"
                               data-action="{{route('invoices.status.changeToCancel',[$invoice->id])}}"
                               class="dropdown-item text-danger btn-invoice-status-change-to-cancel">
                                <i class="fa fa-times"></i> {{ trans('fi.cancel') }}
                            </a>
                        @endif

                        @if(config('fi.allowInvoiceDelete') == 1)
                            @can('invoices.delete')
                            <div class="dropdown-divider"></div>
                            <a href="#" data-action="{{ route('invoices.delete', [$invoice->id]) }}"
                               class="delete-invoice text-danger dropdown-item">
                                <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                            </a>
                            @endcan
                        @endif
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
                @if(($defaultSequenceColumnsData['total'][0] == $invoiceColumnSettings['total'][0])  || ($defaultSequenceColumnsData['balance'][0] == $invoiceColumnSettings['balance'][0]))
                    @if($value['index'] == 0)
                        <td class="text-right text-bold"
                            rowspan="{{count($totalAndBalance)}}"
                            colspan="{{$columnIndex}}">{{trans('fi.page_totals')}}</td>
                    @endif
                @endif

                @if($defaultSequenceColumnsData['total'][0] == $invoiceColumnSettings['total'][0])
                    <td class="text-right {{($value['index'] != 0) ? 'border-top-0' : ''}} pr-4">
                        <strong>{{ $value['total']}}</strong></td>
                @endif

                @if($defaultSequenceColumnsData['balance'][0] == $invoiceColumnSettings['balance'][0])
                    <td class="text-right {{($value['index'] != 0) ? 'border-top-0' : ''}} pr-4">
                        <strong>{{$value['balance']}}</strong></td>
                @endif

                <td class="{{($value['index'] != 0 || $columnIndex == 0)  ? 'border-top-0' : ''}} "></td>
            </tr>
        @endforeach
    @endif
    </tfoot>
</table>
