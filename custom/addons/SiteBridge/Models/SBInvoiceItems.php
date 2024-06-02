<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\SiteBridge\Models;

use Illuminate\Database\Eloquent\Model;

class SBInvoiceItems extends Model
{
    protected $table = 'sb_invoice_items';

    protected $fillable = ['id', 'created_at', 'updated_at', 'sb_invoice_id', 'site_invoice_item_id', 'license_key', 'item_name', 'item_description', 'price'];

    public function invoice()
    {
        return $this->belongsTo('Addons\SiteBridge\Models\SBInvoice');
    }
}