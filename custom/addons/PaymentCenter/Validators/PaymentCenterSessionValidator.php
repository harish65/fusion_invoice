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

class PaymentCenterSessionValidator
{
    public function getValidator($input)
    {
        return Validator::make($input, [
                'email'    => 'required',
                'password' => 'required',
            ]
        );
    }
}