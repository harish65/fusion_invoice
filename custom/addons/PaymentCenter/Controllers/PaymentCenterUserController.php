<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PaymentCenter\Controllers;

use Addons\PaymentCenter\Repositories\PaymentCenterUserRepository;
use Addons\PaymentCenter\Validators\PaymentCenterUserValidator;
use FI\Http\Controllers\Controller;
use FI\Modules\Users\Models\User;

class PaymentCenterUserController extends Controller
{
    public function __construct(
        PaymentCenterUserRepository $paymentCenterUserRepository,
        PaymentCenterUserValidator $paymentCenterUserValidator
    )
    {
        $this->paymentCenterUserRepository = $paymentCenterUserRepository;
        $this->paymentCenterUserValidator  = $paymentCenterUserValidator;
    }

    public function index()
    {
        $users = $this->paymentCenterUserRepository->paginate();

        return view('paymentcenter.users.index')
            ->with('users', $users)->with('status', User::getStatus());
    }

    public function create()
    {
        return view('paymentcenter.users.form')
            ->with('editMode', false)->with('status', User::getStatus());
    }

    public function store()
    {
        $input = request()->all();

        $validator = $this->paymentCenterUserValidator->getValidator($input);

        if ($validator->fails())
        {
            return redirect()->route('paymentCenter.users.create')
                ->with('editMode', false)
                ->withErrors($validator)
                ->withInput();
        }

        $this->paymentCenterUserRepository->create($input);

        return redirect()->route('paymentCenter.users.index')
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
        $user = $this->paymentCenterUserRepository->find($id);

        return view('paymentcenter.users.form')
            ->with('editMode', true)
            ->with('user', $user)
            ->with('status', User::getStatus());
    }

    public function update($id)
    {
        $input = request()->all();

        $validator = $this->paymentCenterUserValidator->getUpdateValidator($input, $id);

        if ($validator->fails())
        {
            return redirect()->route('paymentCenter.users.edit', [$id])
                ->with('editMode', true)
                ->withErrors($validator)
                ->withInput();
        }

        $this->paymentCenterUserRepository->update($input, $id);

        return redirect()->route('paymentCenter.users.index')
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        $this->paymentCenterUserRepository->delete($id);

        return redirect()->route('paymentCenter.users.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function editPassword($id)
    {
        return view('paymentcenter.users.password_form')
            ->with('user', $this->paymentCenterUserRepository->find($id));
    }

    public function updatePassword($id)
    {
        $validator = $this->paymentCenterUserValidator->getUpdatePasswordValidator(request()->all());

        if ($validator->fails())
        {
            return redirect()->route('paymentCenter.users.password.edit', [$id])
                ->withErrors($validator);
        }

        $this->paymentCenterUserRepository->updatePassword(request('password'), $id);

        return redirect()->route('paymentCenter.users.index')
            ->with('alertSuccess', trans('fi.password_successfully_reset'));
    }
}