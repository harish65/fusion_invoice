<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class EmailDefaultAndCustomTemplateSetting extends Migration
{
    public function up()
    {
        Setting::saveByKey('quoteUseCustomTemplate', 'default_mail_template');
        Setting::saveByKey('quoteApprovedUseCustomTemplate', 'default_mail_template');
        Setting::saveByKey('quoteRejectedUseCustomTemplate', 'default_mail_template');
        Setting::saveByKey('overdueInvoiceUseCustomTemplate', 'default_mail_template');
        Setting::saveByKey('invoiceUseCustomTemplate', 'default_mail_template');
        Setting::saveByKey('creditMemoUseCustomTemplate', 'default_mail_template');
        Setting::saveByKey('paymentReceiptUseCustomTemplate', 'default_mail_template');
        Setting::saveByKey('upcomingPaymentNoticeUseCustomTemplate', 'default_mail_template');
    }
}