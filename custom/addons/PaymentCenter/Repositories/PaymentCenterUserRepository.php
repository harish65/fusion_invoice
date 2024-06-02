<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PaymentCenter\Repositories;

use FI\Modules\Users\Models\User;

class PaymentCenterUserRepository
{
    public function find($id)
    {
        return User::find($id);
    }

    public function paginate()
    {
        return User::whereUserType('paymentcenter_user')->orderBy('name')->paginate(15);
    }

    public function create($input)
    {
        $user = new User();

        $user->name              = $input['name'];
        $user->email             = $input['email'];
        $user->password          = $input['password'];
        $user->status            = $input['status'];
        $user->initials_bg_color = $input['initials_bg_color'];
        $user->user_type         = 'paymentcenter_user';

        $user->save();

        return $user;
    }

    public function update($input, $id)
    {
        $user = User::find($id);

        $user->name              = $input['name'];
        $user->email             = $input['email'];
        $user->status            = $input['status'];
        $user->initials_bg_color = $input['initials_bg_color'];
        $user->save();

        return $user;
    }

    public function updatePassword($password, $id)
    {
        $user = User::find($id);

        $user->password = $password;
        $user->save();

        return $user;
    }

    public function delete($id)
    {
        User::destroy($id);
    }
}