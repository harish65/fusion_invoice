<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Tags\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    protected $guarded = ['id'];

    // Always order tags by name
    protected static function boot()
    {
        parent::boot();
    
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('name', 'asc');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function clientLeadSourceTags()
    {
        return $this->hasMany('FI\Modules\Clients\Models\Client', 'lead_source_tag_id');
    }

    public function clientTags()
    {
        return $this->hasMany('FI\Modules\Clients\Models\ClientTag', 'tag_id');
    }

    public function invoiceTags()
    {
        return $this->hasMany('FI\Modules\Invoices\Models\InvoiceTag', 'tag_id');
    }

    public function noteTags()
    {
        return $this->hasMany('FI\Modules\Notes\Models\NoteTag', 'tag_id');
    }

    public function recurringInvoiceTags()
    {
        return $this->hasMany('FI\Modules\RecurringInvoices\Models\RecurringInvoiceTag', 'tag_id');
    }

    /*
  |--------------------------------------------------------------------------
  | Other
  |--------------------------------------------------------------------------
  */
    public static function getTagsCategory()
    {
        return Tag::groupBy('tag_entity')->pluck('tag_entity', 'tag_entity')->toArray();
    }

    public static function getTagsCategoryWiseRecords($category)
    {
        return Tag::whereTagEntity($category)->groupBy('name')->pluck('name', 'id')->toArray();
    }


}