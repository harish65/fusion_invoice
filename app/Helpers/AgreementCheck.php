<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\Carbon;
use FI\Support\DateFormatter;
use FI\Support\UpdateChecker;
use Illuminate\Support\Facades\Log;

function fiVersionCheck()
{
    // Weekly basis we have to auto check version upgrade
    try
    {
        $lastVersionCheckDate = Cookie::get('versionCheckDate');
        $versionCheck         = Cookie::get('versionCheck');

        if ($lastVersionCheckDate == null && ($versionCheck == null || $versionCheck == 1))
        {
            checkUpdates();
        }
        else
        {
            $lastVersionCheckDays = Carbon::parse($lastVersionCheckDate)->diffInDays(Carbon::now());
            if ($lastVersionCheckDays >= 7 && ($versionCheck == null || $versionCheck == 1))
            {
               checkUpdates();
            }
        }
    }
    catch (\Exception $e)
    {
        Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
    }
}

function fiAgreementCheck($user, $key)
{
    try
    {
        if ($user->user_type == 'admin' && $key != null)
        {
            $agreementCheckDate = Cookie::get('agreementCheckDate');
            $agreementCheck     = Cookie::get('agreementCheck');

            if ($agreementCheckDate == null && ($agreementCheck == null || $agreementCheck == 1))
            {
                return checkFiAgreement($key);
            }
            else
            {
                $agreementCheckDate = Carbon::parse($agreementCheckDate)->diffInDays(Carbon::now());
                if ($agreementCheckDate >= 7 && ($agreementCheck == null || $agreementCheck == 1))
                {
                    return checkFiAgreement($key);
                }
            }
        }

        return true;
    }
    catch (\Exception $e)
    {
        Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        return false;
    }
}

function checkUpdates()
{
    try
    {
        $updateChecker = new UpdateChecker;
        $updateChecker->checkVersion('auto');
        $updateAvailable = $updateChecker->updateAvailable();
        $currentVersion = $updateChecker->getCurrentVersion();

        if ($updateAvailable)
        {
            Cookie::queue(Cookie::forever('versionAlert', trans('fi.update_available', ['version' => $currentVersion])));
        }
    }
    catch (\Exception $e)
    {
        Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
    }
}

function checkFiAgreement($key)
{

    try
    {
        $updateChecker = new UpdateChecker;
        $updateChecker->checkAgreementDateAndVersion('auto', $key);
        $piracyStatus = $updateChecker->getPiracyStatus();
        $date         = $updateChecker->getAgreementExpireDate();

        Cookie::queue(Cookie::forever('agreementCheckDate', Carbon::now()));

        if ($piracyStatus == true)
        {
            session(['piracyAlert' => trans('fi.piracy_message')]);
            return false;
        }
        else
        {
            $updateAvailable = $updateChecker->updateAvailable();
            $currentVersion  = $updateChecker->getCurrentVersion();

            if ($updateAvailable)
            {
                Cookie::queue(Cookie::forever('versionAlert', trans('fi.update_available', ['version' => $currentVersion])));
            }

            session(['piracyAlert' => '']);

            $agreementExpireDays = explode(' ', Carbon::parse($date)->diffForHumans(Carbon::now()));

            if ($agreementExpireDays[0] >= 1 && $agreementExpireDays[0] <= 30 && $agreementExpireDays[2] == 'after' && $agreementExpireDays[1] != 'month' && $agreementExpireDays[1] != 'months' && $agreementExpireDays[1] != 'year' && $agreementExpireDays[1] != 'years')
            {
                session(['agreementExpireAlert' => trans('fi.agreement_expire', ['date' => DateFormatter::format($date)])]);
                session(['agreementExpiredAlert' => null]);
            }
            elseif ($agreementExpireDays[2] == 'before')
            {
                session(['agreementExpireAlert' => null]);
                session(['agreementExpiredAlert' => trans('fi.agreement_expired', ['date' => DateFormatter::format($date)])]);
            }

            Session::save();
            return true;
        }
    }
    catch (\Exception $e)
    {
        Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        return false;
    }
}

