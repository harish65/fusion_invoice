<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ResetPassword\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\ResetPassword\Validators\ResetPasswordValidator;
use FI\Modules\Users\Models\User;

class ResetPasswordController extends Controller
{
    private $resetPasswordValidator;

    public function __construct(ResetPasswordValidator $resetPasswordValidator)
    {
        $this->resetPasswordValidator = $resetPasswordValidator;
    }

    public function index()
    {
        return view('resetpassword.index');
    }

    public function update()
    {
        $validator = $this->resetPasswordValidator->getValidator(request()->all());

        if ($validator->fails())
        {
            return redirect()->route('resetPassword.index')
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::where('email', request('email'))->first();

        $user->password = request('password');

        $user->save();

        return redirect()->route('resetPassword.success');
    }

    public function success()
    {
        return view('resetpassword.success');
    }
}