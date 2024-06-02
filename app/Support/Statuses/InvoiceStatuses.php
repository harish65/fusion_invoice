<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support\Statuses;

class InvoiceStatuses extends AbstractStatuses
{
    protected static array $statuses = [
        'draft',
        'sent',
        'canceled'
    ];

    protected static array $creditMemoStatuses = [
        'draft',
        'sent',
        'applied',
        'canceled'
    ];

    protected static array $indexPageStatuses = [
        'draft',
        'sent',
        'paid',
        'canceled',
        'unpaid',
        'mailed',
        'emailed',
        'overdue',
        'viewed',
    ];
}