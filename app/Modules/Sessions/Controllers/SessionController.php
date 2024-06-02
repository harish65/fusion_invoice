<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Sessions\Controllers;

use FI\Http\Controllers\Controller;
use FI\Jobs\PostLogin;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Sessions\Requests\SessionRequest;
use FI\Modules\Settings\Models\Setting;
use FI\Modules\Users\Models\User;
use Illuminate\Support\Facades\File;

class SessionController extends Controller
{
    public function login()
    {
        if (!User::where('user_type', '<>', 'system')->count() || !Setting::where('setting_key', 'key')->count())
        {
            return redirect()->route('setup.postVerify.key');
        }

        $profileCode = request('profile', '');
        if ($profileCode)
        {
            try
            {
                $company_profile = CompanyProfile::whereUuid($profileCode)->where('logo', '!=', null)->select('logo')->first();
                $logo            = $company_profile != '' ? "data:image/png;base64," . base64_encode(file_get_contents(company_profile_logo_path($company_profile->logo))) : '';
            }
            catch (\Exception $e)
            {
                $logo = '';
            }
        }
        else
        {
            try
            {
                $company_profile = CompanyProfile::whereIsDefault(1)->where('logo', '!=', null)->select('logo')->first();
                $logo            = $company_profile != '' ? "data:image/png;base64," . base64_encode(file_get_contents(company_profile_logo_path($company_profile->logo))) : '';
            }
            catch (\Exception $e)
            {
                $logo = '';
            }
        }
        deleteTempFiles();
        deleteViewCache();
        return view('sessions.login', compact('logo'));
    }

    public function attempt(SessionRequest $request)
    {
        $rememberMe = ($request->input('remember_me')) ? true : false;

        $user = User::whereEmail($request->input('email'))->where('user_type', '<>', 'system')->first();

        if (isset($user))
        {
            if (File::exists(storage_path('logs/laravel.log')))
            {
                $filePath   = storage_path('logs/laravel.log');
                $arrayBytes = ['UNIT' => 'MB', 'VALUE' => pow(1024, 2)];
                $fileSize   = strval(round(floatval(filesize($filePath)) / $arrayBytes['VALUE'], 0));

                if ($fileSize >= 5)
                {
                    $fp = fopen($filePath, 'a+');
                    ftruncate($fp, 1048576);
                    clearstatcache();
                    fclose($fp);
                }
            }

            if ($user->status == 0)
            {
                return redirect()->route('session.login')->with('alert', trans('fi.user_not_active'));
            }

            if ((!auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password'), 'status' => 1, 'user_type' => function ($query) {
                $query->where('user_type', '<>', 'paymentcenter_user');
            }], $rememberMe)))
            {
                return redirect()->route('session.login')->with('error', trans('fi.invalid_credentials'));
            }

            PostLogin::dispatch(auth()->user());

            if (!auth()->user()->client_id)
            {
                return redirect()->route('dashboard.index');
            }

            return redirect()->route('clientCenter.dashboard');
        }
        else
        {
            return redirect()->route('session.login')->with('error', trans('fi.invalid_credentials'));
        }

    }

    public function logout()
    {
        if (config('time_tracking_enabled'))
        {
            event(new \Addons\TimeTracking\Events\StopTimeTrackerTasks(auth()->user()->id));
        }

        auth()->logout();

        session()->flush();

        return redirect()->route('session.login');
    }

    public function refreshCaptcha()
    {
        return captcha_img('math');
    }
}