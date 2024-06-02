<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ResetPassword\Validators;

use Illuminate\Support\Facades\Validator;

class ResetPasswordValidator
{
    public function getValidator($input)
    {
        return Validator::make($input, [
                'email'    => 'required|email|exists:users',
                'password' => 'required',
            ]
        );
    }
}