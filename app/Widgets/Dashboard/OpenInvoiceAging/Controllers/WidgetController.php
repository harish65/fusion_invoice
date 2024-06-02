<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Widgets\Dashboard\OpenInvoiceAging\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Settings\Models\Setting;

class WidgetController extends Controller
{
    public function widgetUpdateOpenInvoiceAgingSetting($invoiceStatus = null)
    {
        Setting::saveByKey('widgetOpenInvoiceAging', (isset($invoiceStatus) && $invoiceStatus != null) ? $invoiceStatus : 'sent');

        return back();
    }
}