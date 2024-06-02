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

abstract class AbstractStatuses
{
    /**
     * Returns an array of statuses to populate dropdown list.
     *
     * @return array
     */
    public static function lists()
    {
        $statuses = array_combine(static::$statuses, static::$statuses);

        foreach ($statuses as $key => $status)
        {
            $statuses[$key] = trans('fi.' . $status);
        }

        return $statuses;
    }

    public static function creditMemoLists()
    {
        $creditMemoStatuses = array_combine(static::$creditMemoStatuses, static::$creditMemoStatuses);

        foreach ($creditMemoStatuses as $key => $creditMemoStatus)
        {
            $creditMemoStatuses[$key] = trans('fi.' . $creditMemoStatus);
        }

        return $creditMemoStatuses;
    }

    public static function indexPageLists()
    {
        $indexPageStatuses = array_combine(static::$indexPageStatuses, static::$indexPageStatuses);

        foreach ($indexPageStatuses as $key => $indexPageStatus)
        {
            $indexPageStatuses[$key] = trans('fi.' . $indexPageStatus);
        }

        return $indexPageStatuses;
    }
}