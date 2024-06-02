<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\MailQueue\Models;

use FI\Support\DateFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class MailQueue extends Model
{
    use Sortable;

    protected $table = 'mail_queue';

    protected $sortable = ['created_at', 'from', 'to', 'cc', 'bcc', 'subject', 'sent'];

    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function mailable()
    {
        return $this->morphTo();
    }

    public function getFormattedCreatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['created_at'], true);
    }

    public function getFormattedFromAttribute()
    {
        $from = json_decode($this->attributes['from']);

        return $from->email;
    }

    public function getFormattedToAttribute()
    {
        return implode(', ', json_decode($this->attributes['to'], true));
    }

    public function getFormattedCcAttribute()
    {
        $cc = json_decode($this->attributes['cc'], true);

        if (json_last_error() === JSON_ERROR_NONE)
        {
            return implode(', ', $cc);
        }
        else
        {
            return false;
        }

    }

    public function getFormattedBccAttribute()
    {
        $bcc = json_decode($this->attributes['bcc'], true);

        if (json_last_error() === JSON_ERROR_NONE)
        {
            return implode(', ', $bcc);
        }
        else
        {
            return false;
        }

    }

    public function getFormattedSentAttribute()
    {
        return ($this->attributes['sent']) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-exclamation text-danger"></i>';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeKeywords($query, $keywords = null)
    {
        if ($keywords)
        {
            $keywords = strtolower($keywords);

            $query->where('created_at', 'like', '%' . $keywords . '%')
                  ->orWhere('from', 'like', '%' . $keywords . '%')
                  ->orWhere('to', 'like', '%' . $keywords . '%')
                  ->orWhere('cc', 'like', '%' . $keywords . '%')
                  ->orWhere('bcc', 'like', '%' . $keywords . '%')
                  ->orWhere('subject', 'like', '%' . $keywords . '%');
        }

        return $query;
    }

}