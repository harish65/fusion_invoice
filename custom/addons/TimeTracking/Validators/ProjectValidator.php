<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Validators;

use Illuminate\Support\Facades\Validator;

class ProjectValidator
{
    private function getAttributeNames()
    {
        return [
            'company_profile_id' => trans('fi.company_profile'),
            'client_id'          => trans('fi.client'),
            'name'               => trans('TimeTracking::lang.project_name'),
            'hourly_rate'        => trans('TimeTracking::lang.hourly_rate'),
        ];
    }

    public function getValidator($input)
    {
        return Validator::make($input, [
            'company_profile_id' => 'required',
            'client_id'          => 'required',
            'name'               => 'required',
            'hourly_rate'        => 'required|numeric',
        ])->setAttributeNames($this->getAttributeNames());
    }

}