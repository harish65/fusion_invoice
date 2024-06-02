<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FI\Modules\Transitions\Models\Transitions;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {

    public function up()
    {
        $transitions = Transitions::whereTransitionableType('FI\Modules\Attachments\Models\Attachment')->get();
        if ($transitions)
        {
            foreach ($transitions as $transition)
            {
                $detail = json_decode($transition->detail, TRUE);
                if (!isset($detail['attachment_link']))
                {
                    $detail['attachment_link'] = '';
                    $transition->detail        = json_encode($detail);
                    $transition->save();
                }
            }
        }
    }
};
