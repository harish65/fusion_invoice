<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PaymentCenter\Validators;

use Validator;

class PaymentCenterUserValidator
{
    public function getValidator($input)
    {
        return Validator::make($input, [
                'name'     => 'required',
                'email'    => 'required|unique:users',
                'password' => 'required|confirmed|min:6',
            ]
        );
    }

    public function getUpdateValidator($input, $id)
    {
        return Validator::make($input, [
                'name'  => 'required',
                'email' => 'required|unique:users,email,' . $id,
            ]
        );
    }

    public function getUpdatePasswordValidator($input)
    {
        return Validator::make($input, [
                'password' => 'required|confirmed|min:6',
            ]
        );
    }
}