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

class SBInvoice extends Model
{
    protected $table = 'sb_invoices';

    protected $fillable = ['id', 'created_at', 'updated_at', 'fi_invoice_id', 'fi_client_id', 'site_invoice_id', 'site_user_id', 'total'];

    public function sbInvoiceItems()
    {
        return $this->hasMany('Addons\SiteBridge\Models\SBInvoiceItems', 'sb_invoice_id', 'id');
    }
}