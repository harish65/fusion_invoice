<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Transitions\Models;

use Carbon\Carbon;
use DB;
use FI\Modules\Notes\Models\Note;
use FI\Modules\Payments\Models\PaymentInvoice;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Transitions extends Model
{
    protected $guarded = ['id'];

    public function transitionable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('FI\Modules\Users\Models\User')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
    }

    public function note()
    {
        return $this->belongsTo('FI\Modules\Notes\Models\Note', 'transitionable_id')->where('transitions.transitionable_type', 'FI\Modules\Notes\Models\Note');
    }

    public function transitionClient()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client', 'transitionable_id')->where('transitions.transitionable_type', 'FI\Modules\Clients\Models\Client');
    }

    public function invoice()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\Invoice', 'transitionable_id')
                    ->where([
                        ['transitions.transitionable_type', '=', 'FI\Modules\Invoices\Models\Invoice'],
                        ['type', '=', 'invoice'],
                    ]);
    }

    public function creditMemo()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\Invoice', 'transitionable_id')
                    ->where([
                        ['transitions.transitionable_type', '=', 'FI\Modules\Invoices\Models\Invoice'],
                        ['type', '=', 'credit_memo'],
                    ]);
    }

    public function quote()
    {
        return $this->belongsTo('FI\Modules\Quotes\Models\Quote', 'transitionable_id')->where('transitions.transitionable_type', 'FI\Modules\Quotes\Models\Quote');
    }

    public function recurringInvoice()
    {
        return $this->belongsTo('FI\Modules\RecurringInvoices\Models\RecurringInvoice', 'transitionable_id')->where('transitions.transitionable_type', 'FI\Modules\RecurringInvoices\Models\RecurringInvoice');
    }

    public function payment()
    {
        return $this->belongsTo('FI\Modules\Payments\Models\Payment', 'transitionable_id')->where('transitions.transitionable_type', 'FI\Modules\Payments\Models\Payment');
    }

    public function paymentInvoice()
    {
        return $this->belongsTo('FI\Modules\Payments\Models\PaymentInvoice', 'transitionable_id')->where('transitions.transitionable_type', 'FI\Modules\Payments\Models\PaymentInvoice');
    }

    public function task()
    {
        return $this->belongsTo('FI\Modules\TaskList\Models\Task', 'transitionable_id')->where('transitions.transitionable_type', 'FI\Modules\TaskList\Models\Task');
    }

    public function expense()
    {
        return $this->belongsTo('FI\Modules\Expenses\Models\Expense', 'transitionable_id')->where('transitions.transitionable_type', 'FI\Modules\Expenses\Models\Expense');
    }

    public function attachment()
    {
        return $this->belongsTo('FI\Modules\Attachments\Models\Attachment', 'transitionable_id')->where('transitions.transitionable_type', 'FI\Modules\Attachments\Models\Attachment');
    }

    public function invoiceCommission()
    {
        return $this->belongsTo('Addons\Commission\Models\InvoiceItemCommission', 'transitionable_id')->where('transitions.transitionable_type', 'Addons\Commission\Models\InvoiceItemCommission');
    }

    public function recurringInvoiceCommission()
    {
        return $this->belongsTo('Addons\Commission\Models\RecurringInvoiceItemCommission', 'transitionable_id')->where('transitions.transitionable_type', 'Addons\Commission\Models\RecurringInvoiceItemCommission');
    }

    public function getFormattedCreatedAtAttribute()
    {
        if (Carbon::parse($this->attributes['created_at'])->diffInDays(Carbon::now()) <= 7)
        {
            Carbon::setLocale(app()->getLocale());
            return Carbon::parse($this->attributes['created_at'])->diffForHumans();
        }
        else
        {
            return DateFormatter::format($this->attributes['created_at'], true);
        }
    }

    public function getFormattedCreatedAtSystemFormatAttribute()
    {
        return DateFormatter::format($this->attributes['created_at'], true);
    }

    public function getFormattedCreatedAtNewlineAttribute()
    {
        return DateFormatter::format($this->attributes['created_at'], false) . '<br>' . DateFormatter::extractTime($this->attributes['created_at']);
    }

    public function getTransitionEntityAttribute()
    {
        $description = $info = '';
        $detail      = json_decode($this->detail);

        if ($this->transitionable_type == 'FI\Modules\Clients\Models\Client')
        {
            if ($this->action_type == 'client_created')
            {
                $description = trans('fi.transition.client.client_created', ['client_type' => trans('fi.' . $detail->type)]);
            }
            elseif ($this->action_type == 'type_changed')
            {
                $description = trans('fi.transition.client.type_changed', ['previous_value' => trans('fi.' . $this->previous_value), 'current_value' => trans('fi.' . $this->current_value)]);
            }
            elseif ($this->action_type == 'updated')
            {
                $description = trans('fi.transition.client.updated');
            }
            elseif ($this->action_type == 'deleted')
            {
                $description = trans('fi.transition.client.deleted', ['client_name' => $detail->name]);
            }
            elseif ($this->action_type == 'status_changed')
            {
                $description = trans('fi.transition.client.status_changed', ['current_value' => $this->current_value == 1 ? trans('fi.active') : trans('fi.inactive')]);
            }
            elseif ($this->action_type == 'client_tag_updated')
            {
                $description = trans('fi.transition.client.client_tag_updated', ['client_name' => $detail->name, 'tags' => $detail->tag_name]);
            }
            elseif ($this->action_type == 'client_tag_deleted')
            {
                $description = trans('fi.transition.client.client_tag_deleted', ['client_name' => $detail->name, 'tags' => $detail->tag_name]);
            }
        }

        if ($this->transitionable_type == 'FI\Modules\Invoices\Models\Invoice')
        {
            if ($this->action_type == 'created')
            {
                $description = trans('fi.transition.invoice.created', ['invoice_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            if ($this->action_type == 'credit_memo_created')
            {
                $description = trans('fi.transition.invoice.credit_memo_created', ['credit_memo_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            if ($this->action_type == 'created_from_recurring')
            {
                $description = trans('fi.transition.invoice.created_from_recurring', ['invoice_number' => '#' . $detail->number, 'recurring_invoice_id' => '#' . $detail->recurring_invoice_id]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'updated')
            {
                $description = trans('fi.transition.invoice.updated', ['invoice_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'credit_memo_updated')
            {
                $description = trans('fi.transition.invoice.credit_memo_updated', ['credit_memo_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'deleted')
            {
                $description = trans('fi.transition.invoice.deleted', ['invoice_number' => '#' . $detail->number]);
            }
            elseif ($this->action_type == 'credit_memo_deleted')
            {
                $description = trans('fi.transition.invoice.credit_memo_deleted', ['credit_memo_number' => '#' . $detail->number]);
            }
            elseif ($this->action_type == 'email_sent')
            {
                $description = trans('fi.transition.invoice.email_sent', ['invoice_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'email_opened')
            {
                $description = trans('fi.transition.invoice.email_opened', ['invoice_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'status_changed')
            {
                $description = trans('fi.transition.invoice.status_changed', ['invoice_number' => '#' . $detail->number, 'previous_value' => trans('fi.' . $this->previous_value), 'current_value' => trans('fi.' . $this->current_value)]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'invoice_tag_updated')
            {
                $description = trans('fi.transition.invoice.invoice_tag_updated', ['invoice_number' => '#' . $detail->number, 'tags' => $detail->tag_name]);
            }
            elseif ($this->action_type == 'invoice_tag_deleted')
            {
                $description = trans('fi.transition.invoice.invoice_tag_deleted', ['invoice_number' => '#' . $detail->number, 'tags' => $detail->tag_name]);
            }
            elseif ($this->action_type == 'mark_mail')
            {
                $description = trans('fi.transition.invoice.mark_mail', ['invoice_number' => '#' . $detail->number]);
            }
            elseif ($this->action_type == 'unmark_mail')
            {
                $description = trans('fi.transition.invoice.unmark_mail', ['invoice_number' => '#' . $detail->number]);
            }
            elseif ($this->action_type == 'paid_invoice_opened')
            {
                $description = trans('fi.transition.invoice.paid_invoice_opened', ['invoice_number' => '#' . $detail->number]);
            }
            elseif ($this->action_type == 'sent_invoice_opened')
            {
                $description = trans('fi.transition.invoice.sent_invoice_opened', ['invoice_number' => '#' . $detail->number]);
            }

        }

        if ($this->transitionable_type == 'FI\Modules\Quotes\Models\Quote')
        {
            if ($this->action_type == 'created')
            {
                $description = trans('fi.transition.quote.created', ['quote_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'updated')
            {
                $description = trans('fi.transition.quote.updated', ['quote_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'deleted')
            {
                $description = trans('fi.transition.quote.deleted', ['quote_number' => '#' . $detail->number]);
            }
            elseif ($this->action_type == 'email_sent')
            {
                $description = trans('fi.transition.quote.email_sent', ['quote_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'email_opened')
            {
                $description = trans('fi.transition.quote.email_opened', ['quote_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'status_changed')
            {
                $description = trans('fi.transition.quote.status_changed', ['quote_number' => '#' . $detail->number, 'previous_value' => trans('fi.' . $this->previous_value), 'current_value' => trans('fi.' . $this->current_value)]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'quote_to_invoice')
            {
                $description = trans('fi.transition.quote.quote_to_invoice', ['quote_number' => '#' . $detail->quote_number, 'invoice_number' => $detail->invoice_number]);
            }
        }

        if ($this->transitionable_type == 'FI\Modules\RecurringInvoices\Models\RecurringInvoice')
        {
            if ($this->action_type == 'created')
            {
                $description = trans('fi.transition.recurring_invoice.created', ['invoice_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'updated')
            {
                $description = trans('fi.transition.recurring_invoice.updated', ['invoice_number' => '#' . $detail->number]);
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            elseif ($this->action_type == 'deleted')
            {
                $description = trans('fi.transition.recurring_invoice.deleted', ['invoice_number' => '#' . $detail->number]);
            }
            elseif ($this->action_type == 'recurring_invoice_tag_updated')
            {
                $description = trans('fi.transition.recurring_invoice.recurring_invoice_tag_updated', ['recurringInvoice_number' => '#' . $detail->number, 'tags' => $detail->tag_name]);
            }
            elseif ($this->action_type == 'recurring_invoice_tag_deleted')
            {
                $description = trans('fi.transition.recurring_invoice.recurring_invoice_tag_deleted', ['recurringInvoice_number' => '#' . $detail->number, 'tags' => $detail->tag_name]);
            }
        }

        if ($this->transitionable_type == 'FI\Modules\Payments\Models\PaymentInvoice')
        {
            if ($this->action_type == 'payment_received')
            {
                $description = trans('fi.transition.invoice.payment_received', ['invoice_number' => '#' . $detail->invoice_number, 'full_payment_text' => ($detail->is_full_amount) ? trans('fi.full_and_final_payment') : trans('fi.partial')]);
                $info        = trans('fi.amount') . ': ' . $detail->invoice_amount_paid;
            }

            if ($this->action_type == 'payment_reversed')
            {
                $description = trans('fi.transition.invoice.payment_reversed', ['invoice_number' => '#' . $detail->invoice_number, 'full_payment_text' => ($detail->is_full_amount) ? trans('fi.full_payment_reversed') : trans('fi.partial_payment_reversed')]);
                $info        = trans('fi.amount') . ': ' . $detail->invoice_amount_paid;
            }
        }

        if ($this->transitionable_type == 'FI\Modules\Payments\Models\Payment')
        {
            if ($this->action_type == 'prepayment_created')
            {
                $description = trans('fi.transition.payment.prepayment_created');
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            if ($this->action_type == 'payment_receipt_email_sent')
            {
                $description = trans('fi.transition.payment.payment_receipt_email_sent');
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            if ($this->action_type == 'payment_updated')
            {
                $description = trans('fi.transition.payment.payment_updated');
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            if ($this->action_type == 'deleted')
            {
                $description = trans('fi.transition.payment.deleted');
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            if ($this->action_type == 'payment_receipt_pdf_download')
            {
                $description = trans('fi.transition.payment.payment_receipt_pdf_download');
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            if ($this->action_type == 'payment_note_deleted')
            {
                $description = trans('fi.transition.payment.payment_note_deleted', ['previous_value' => $this->previous_value]);
            }
            if ($this->action_type == 'payment_note_added')
            {
                $description = trans('fi.transition.payment.payment_note_added', ['current_value' => $this->current_value]);
            }
            if ($this->action_type == 'payment_note_updated')
            {
                $description = trans('fi.transition.payment.payment_note_updated', ['previous_value' => $this->previous_value, 'current_value' => $this->current_value]);
            }
        }

        if ($this->transitionable_type == 'FI\Modules\Notes\Models\Note')
        {
            if ($this->action_type == 'created')
            {
                $description = trans('fi.transition.note.created');
                $title = null;
                $info = noteFormatter($this->id,$title,$detail);
            }
            if ($this->action_type == 'updated')
            {
                $description = trans('fi.transition.note.updated');
                $title = null;
                $info = noteFormatter($this->id,$title,$detail);

            }
            if ($this->action_type == 'deleted')
            {
                $description = trans('fi.transition.note.deleted');
                $info        = (isset($detail->short_text)) ? trans('fi.text') . ': ' . utf8_encode($detail->short_text) : '';
            }
            if ($this->action_type == 'note_tag_updated')
            {
                $description = trans('fi.transition.note.note_tag_updated', ['tags' => $detail->tag_name]);
                $info        = (isset($detail->short_text)) ? trans('fi.text') . ': ' . utf8_encode($detail->short_text) : '';
            }
            if ($this->action_type == 'note_tag_deleted')
            {
                $description = trans('fi.transition.note.note_tag_deleted', ['tags' => $detail->tag_name]);
                $info        = (isset($detail->short_text)) ? trans('fi.text') . ': ' . utf8_encode($detail->short_text) : '';
            }
        }

        if ($this->transitionable_type == 'FI\Modules\Expenses\Models\Expense')
        {
            if ($this->action_type == 'created')
            {
                $description = trans('fi.transition.expense.created');
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            if ($this->action_type == 'updated')
            {
                $description = trans('fi.transition.expense.updated');
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            if ($this->action_type == 'deleted')
            {
                $description = trans('fi.transition.expense.deleted');
                $info        = (isset($detail->amount)) ? trans('fi.total') . ': ' . $detail->amount : '';
            }
            if ($this->action_type == 'billed')
            {
                $description = trans('fi.transition.expense.billed');
                $info        = (isset($detail->invoice)) ? trans('fi.invoice') . ': #' . $detail->invoice : '';
            }
        }

        if ($this->transitionable_type == 'FI\Modules\TaskList\Models\Task')
        {
            if ($this->action_type == 'created')
            {
                $description = trans('fi.transition.task.created');
                $title       = (isset($detail->short_title)) ? trans('fi.title') . ': ' . $detail->short_title : '';
                $info = noteFormatter($this->id,$title,$detail);
            }
            if ($this->action_type == 'updated')
            {
                $description = trans('fi.transition.task.updated');
                $title       = (isset($detail->short_title)) ? trans('fi.title') . ': ' . $detail->short_title : '';
                $info = noteFormatter($this->id,$title,$detail);
            }
            if ($this->action_type == 'deleted')
            {
                $description = trans('fi.transition.task.deleted');
                $info        = (isset($detail->short_title)) ? trans('fi.title') . ': ' . $detail->short_title : '';
            }
            if ($this->action_type == 'completed')
            {
                $description = trans('fi.transition.task.completed');
                $info        = ((isset($detail->short_title)) ? trans('fi.title') . ': ' . $detail->short_title : null) . '</br>' .
                               ((isset($detail->note) && ($detail->note != null)) ? (trans('fi.note') . ': ' . self::trimText($detail->note, 50)) : null);
            }
        }

        if ($this->transitionable_type == 'FI\Modules\Attachments\Models\Attachment')
        {
            if ($this->action_type == 'created')
            {
                if (Gate::check('attachments.view'))
                {
                    if (isset($detail->attachment_link)) 
                    {
                        $fileName = '<a target="_blank" href="' . $detail->attachment_link . '">' . $detail->filename . ' </a>';
                    } 
                    else 
                    {
                        $fileName = $detail->filename;
                    }
                }
                else
                {
                    $fileName = $detail->filename;
                }

                $description = trans('fi.transition.attachment.created', ['filename' => $fileName]);

            }

            if ($this->action_type == 'deleted')
            {
                $description = trans('fi.transition.attachment.deleted', ['filename' => $detail->filename]);
            }

        }

        if ($this->transitionable_type == 'Addons\Commission\Models\InvoiceItemCommission')
        {
            if ($this->action_type == 'invoice_commission_created')
            {
                $description = trans('Commission::lang.invoice_commission_created', ['commission_id' => $detail->id, 'item' => $detail->name, 'invoice_number' => $detail->number]);
                $info        = trans('Commission::lang.commission_amount', ['amount' => CurrencyFormatter::format($detail->amount)]);
            }

            if ($this->action_type == 'invoice_commission_updated')
            {
                $description = trans('Commission::lang.invoice_commission_updated', ['commission_id' => $detail->id, 'item' => $detail->name, 'invoice_number' => $detail->number]);
                $info        = trans('Commission::lang.commission_amount', ['amount' => CurrencyFormatter::format($detail->amount)]);
            }

        }

        if ($this->transitionable_type == 'Addons\Commission\Models\RecurringInvoiceItemCommission')
        {
            if ($this->action_type == 'recurring_invoice_commission_created')
            {
                $description = trans('Commission::lang.recurring_invoice_commission_created', ['commission_id' => $detail->id, 'item' => $detail->name, 'invoice_number' => $detail->number]);
                $info        = trans('Commission::lang.commission_amount', ['amount' => CurrencyFormatter::format($detail->amount)]);
            }

            if ($this->action_type == 'recurring_invoice_commission_updated')
            {
                $description = trans('Commission::lang.recurring_invoice_commission_updated', ['commission_id' => $detail->id, 'item' => $detail->name, 'invoice_number' => $detail->number]);
                $info        = trans('Commission::lang.commission_amount', ['amount' => CurrencyFormatter::format($detail->amount)]);
            }

        }

        if ($this->transitionable_type == 'FI\Modules\Tags\Models\Tag')
        {
            if ($this->action_type == 'created')
            {
                $description = trans('fi.transition.tag.created', ['tag' => $detail->name, 'module' => $detail->module]);

            }

            if ($this->action_type == 'deleted')
            {
                $description = trans('fi.transition.tag.deleted', ['tag' => $detail->name, 'module' => $detail->module]);
            }

        }

        if ($description === '')
        {
            return $info;
        }
        else
        {
            return $description . '<br />' . $info;
        }


    }

    public static function trimText($text, $limit)
    {
        if (str_word_count(trim(strip_tags($text))) > $limit)
        {
            if (str_word_count($text, 0) > $limit)
            {
                $words = str_word_count($text, 2);
                $pos   = array_keys($words);
                $text  = substr($text, 0, $pos[$limit]) . '...';
            }
            return $text;
        }
        else
        {
            return nl2br($text);
        }
    }

    public function getTransitionEntityIconAttribute()
    {
        if ($this->transitionable_type == 'FI\Modules\Clients\Models\Client')
        {
            return 'fa fa-user';
        }
        if ($this->transitionable_type == 'FI\Modules\Invoices\Models\Invoice')
        {
            if ($this->action_type == 'email_sent')
            {
                return 'fa fa-envelope';
            }
            elseif ($this->action_type == 'email_opened')
            {
                return 'fa fa-envelope';
            }
            else
            {
                return 'fa fa-file-invoice';
            }

        }
        if ($this->transitionable_type == 'FI\Modules\Quotes\Models\Quote')
        {
            if ($this->action_type == 'email_sent')
            {
                return 'fa fa-envelope';
            }
            elseif ($this->action_type == 'email_opened')
            {
                return 'fa fa-envelope';
            }
            else
            {
                return 'fa fa-file-alt';
            }

        }
        if ($this->transitionable_type == 'FI\Modules\Payments\Models\PaymentInvoice')
        {
            return 'fa fa-credit-card';
        }
        if ($this->transitionable_type == 'FI\Modules\Payments\Models\Payment')
        {
            return 'fa fa-money-check-alt';
        }
        if ($this->transitionable_type == 'FI\Modules\Notes\Models\Note')
        {
            return 'fa fa-comments';
        }
        if ($this->transitionable_type == 'FI\Modules\TaskList\Models\Task')
        {
            return 'fa fa-tasks';
        }
        if ($this->transitionable_type == 'FI\Modules\Expenses\Models\Expense')
        {
            return 'fa fa-credit-card';
        }
        if ($this->transitionable_type == 'FI\Modules\RecurringInvoices\Models\RecurringInvoice')
        {
            return 'fa fa-sync';
        }
        if ($this->transitionable_type == 'FI\Modules\Attachments\Models\Attachment')
        {
            return 'fa fa-paperclip';
        }
        if ($this->transitionable_type == 'Addons\Commission\Models\InvoiceItemCommission')
        {
            return 'fa fa-money';
        }
        if ($this->transitionable_type == 'Addons\Commission\Models\RecurringInvoiceItemCommission')
        {
            return 'fa fa-money';
        }
        if ($this->transitionable_type == 'FI\Modules\Tags\Models\Tag')
        {
            return 'fa fa-tags';
        }
    }

    public function getSalesPersonNameAttribute()
    {
        $detail = json_decode($this->detail);

        if ($this->transitionable_type == 'Addons\Commission\Models\InvoiceItemCommission' || $this->transitionable_type == 'Addons\Commission\Models\RecurringInvoiceItemCommission')
        {
            return $detail->sales_person;
        }
    }

    public function getSalesPersonIdAttribute()
    {
        $detail = json_decode($this->detail);

        if ($this->transitionable_type == 'Addons\Commission\Models\InvoiceItemCommission' || $this->transitionable_type == 'Addons\Commission\Models\RecurringInvoiceItemCommission')
        {
            return $detail->sales_person_id;
        }
    }

    public function getTransitionEntityNameAttribute()
    {
        if ($this->transitionable_type == 'FI\Modules\Clients\Models\Client')
        {
            return trans('fi.client');
        }
        if ($this->transitionable_type == 'FI\Modules\Invoices\Models\Invoice')
        {
            if (($this->transitionable) && ($this->transitionable->type == 'credit_memo'))
            {
                return trans('fi.credit_memo');
            }
            else
            {
                return trans('fi.invoice');
            }
        }
        if ($this->transitionable_type == 'FI\Modules\Quotes\Models\Quote')
        {
            return trans('fi.quote');
        }
        if ($this->transitionable_type == 'FI\Modules\Payments\Models\PaymentInvoice')
        {
            if (($this->transitionable) && ($this->transitionable->payment->credit_memo_id))
            {
                return trans('fi.credit_applied');
            }
            else
            {
                return trans('fi.payments');
            }
        }
        if ($this->transitionable_type == 'FI\Modules\Payments\Models\Payment')
        {
            return trans('fi.payments');
        }
        if ($this->transitionable_type == 'FI\Modules\Notes\Models\Note')
        {
            return trans('fi.note');
        }
        if ($this->transitionable_type == 'FI\Modules\TaskList\Models\Task')
        {
            return trans('fi.task');
        }
        if ($this->transitionable_type == 'FI\Modules\Expenses\Models\Expense')
        {
            return trans('fi.expense');
        }
        if ($this->transitionable_type == 'FI\Modules\RecurringInvoices\Models\RecurringInvoice')
        {
            return trans('fi.recurring_invoice');
        }
        if ($this->transitionable_type == 'FI\Modules\Attachments\Models\Attachment')
        {
            return trans('fi.attachment');
        }
        if ($this->transitionable_type == 'Addons\Commission\Models\InvoiceItemCommission')
        {
            return trans('Commission::lang.invoice_commission');
        }
        if ($this->transitionable_type == 'Addons\Commission\Models\RecurringInvoiceItemCommission')
        {
            return trans('Commission::lang.recurring_invoice_commission');
        }
        if ($this->transitionable_type == 'FI\Modules\Tags\Models\Tag')
        {
            return trans('fi.tags');
        }
    }

    public function getFormattedActionTypeAttribute()
    {
        $actionType = explode('_', $this->action_type);

        if ($this->action_type == 'quote_to_invoice')
        {
            return trans('fi.transition.' . $this->action_type);
        }
        else
        {
         return isset($actionType[1]) ? trans('fi.transition.' . end($actionType)) : trans('fi.transition.' . $actionType[0]);
        }
    }

    public function getFormattedActionCountAttribute()
    {
        $detail = json_decode($this->detail);

        return isset($detail->action_count) && $detail->action_count > 0 ? $detail->action_count : '';
    }

    public static function getModulesList()
    {
        $moduleMap  = self::mapModule();

        $moduleList = collect($moduleMap)->map(function ($name , $key)
        {
            return  trans('fi.' . $key);
        });

        return $moduleList;
    }

    public static function mapModule()
    {
        return [
            'attachment'                    => 'FI\Modules\Attachments\Models\Attachment',
            'clients'                       => 'FI\Modules\Clients\Models\Client',
            'credit_applied'                => 'FI\Modules\Payments\Models\PaymentInvoice',
            'credit_memo'                   => 'FI\Modules\Invoices\Models\Invoice',
            'expenses'                      => 'FI\Modules\Expenses\Models\Expense',
            'invoices'                      => 'FI\Modules\Invoices\Models\Invoice',
            'notes'                         => 'FI\Modules\Notes\Models\Note',
            'payments'                      => 'FI\Modules\Payments\Models\Payment',
            'quotes'                        => 'FI\Modules\Quotes\Models\Quote',
            'recurring_invoices'            => 'FI\Modules\RecurringInvoices\Models\RecurringInvoice',
            'tasks'                         => 'FI\Modules\TaskList\Models\Task',
            'recurring_invoice_commissions' => 'Addons\Commission\Models\RecurringInvoiceItemCommission',
            'invoice_commissions'           => 'Addons\Commission\Models\InvoiceItemCommission',
            'tags'                          => 'FI\Modules\Tags\Models\Tag',
        ];
    }

    public function scopeModules($query, $modules = [])
    {
        $moduleMap = self::mapModule();

        $query->where(function ($q) use ($modules, $moduleMap)
        {
            foreach ($modules as $module)
            {
                if ($module == 'credit_memo')
                {
                    $q->orWhere('action_type', 'like', 'credit_memo%');
                }
                elseif ($module == 'invoices')
                {
                    $q->orWhere([
                        ['transitionable_type', $moduleMap[$module]],
                        ['action_type', 'not like', 'credit_memo%'],
                    ]);
                }
                else
                {
                    $q->orWhere('transitionable_type', $moduleMap[$module]);
                }
            }
        });

        return $query;
    }

    public static function getPaginatedTransitions($filterUsers = [], $filterModules = [], $customSearch = null, $clientId = null)
    {
        $loginUser   = auth()->user();
        $transitions = Transitions::query()->modules($filterModules);

        if ($clientId)
        {
            $transitions->where('client_id', $clientId);
        }
        if ((!empty($filterUsers)) && ($filterUsers[0] != ''))
        {
            $transitions->whereIn('user_id', $filterUsers);
        }
        elseif ($loginUser->user_type == 'standard_user')
        {
            if (!$loginUser->hasPermission('timeline_scope_all_user', 'is_view'))
            {
                $transitions->where('user_id', $loginUser->id);
            }
        }

        if ($customSearch)
        {
            $transitions->where('detail', 'like', '%' . $customSearch . '%');
        }

        if (!config('commission_enabled'))
        {
            $transitions->whereNotIn('transitionable_type', ['Addons\Commission\Models\InvoiceItemCommission', 'Addons\Commission\Models\RecurringInvoiceItemCommission']);
        }

        return $transitions->orderBy('created_at', 'desc')
                           ->groupBy('id')
                           ->paginate(config('fi.resultsPerPage'));
    }

    public static function getInvoicePaginatedTransitions($filterUsers = [], $filterModules = [], $customSearch = null, $clientId = null, $invoiceId = null, $modalType = null)
    {
        $loginUser   = auth()->user();
        $transitions = Transitions::query()->modules($filterModules);
        $moduleMap   = self::mapModule();

        if ((!empty($filterUsers)) && ($filterUsers[0] != ''))
        {
            $transitions->whereIn('user_id', $filterUsers);
        }
        elseif ($loginUser->user_type == 'standard_user')
        {
            if (!$loginUser->hasPermission('timeline_scope_all_user', 'is_view'))
            {
                $transitions->where('user_id', $loginUser->id);
            }
        }
        if ($invoiceId != null && $modalType != null && $modalType == 'invoice')
        {
            $paymentInvoiceIds = PaymentInvoice::select('id')->whereInvoiceId($invoiceId)->get()->toArray();
            $invoiceNoteIds    = Note::select('id')->whereNotableId($invoiceId)->whereNotableType('FI\Modules\Invoices\Models\Invoice')->get()->toArray();

            $transitions->where(function ($q) use ($invoiceId, $paymentInvoiceIds)
            {
                $q->where('transitionable_id', $invoiceId);
            })->orWhere(function ($q) use ($paymentInvoiceIds, $moduleMap)
            {
                $q->whereIn('transitionable_id', $paymentInvoiceIds);
                $q->where('transitionable_type', $moduleMap['credit_applied']);
            })->orWhere(function ($q) use ($invoiceNoteIds, $moduleMap)
            {
                $q->whereIn('transitionable_id', $invoiceNoteIds);
                $q->where('transitionable_type', $moduleMap['notes']);
            });
        }

        if ($customSearch)
        {
            $transitions->where('detail', 'like', '%' . $customSearch . '%');
        }

        return $transitions->orderBy('created_at', 'desc')
                           ->groupBy('id')
                           ->paginate(5);
    }

}