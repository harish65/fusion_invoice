<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Settings\Models\Setting;
use GuzzleHttp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateChecker
{
    protected $currentVersion;
    protected $agreementDate;
    protected $httpClient;
    protected $piracyStatus;

    public function checkVersion($request_type)
    {
        $companyProfile = CompanyProfile::find(config('fi.defaultCompanyProfile'));
        if (!$companyProfile)
        {
            $companyProfile = CompanyProfile::query()->first();
            if ($companyProfile)
            {
                Setting::saveByKey('defaultCompanyProfile', $companyProfile->id);
                request()->session()->put('alertInfo', trans('fi.default_company_profile_set'));
            }
        }
        if ($companyProfile)
        {
            $this->currentVersion = Http::get('https://www.fusioninvoice.com/current-version/' . config('app.key') . '/' . urlencode($companyProfile->company) . '/' . $request_type . '/' . config('fi.version'))->body();
        }
        else
        {
            $this->currentVersion = null;
        }
    }

    public function checkAgreementDate($request_type)
    {
        $companyProfile = CompanyProfile::find(config('fi.defaultCompanyProfile'));
        if (!$companyProfile)
        {
            $companyProfile = CompanyProfile::query()->first();
            if ($companyProfile)
            {
                Setting::saveByKey('defaultCompanyProfile', $companyProfile->id);
                request()->session()->put('alertInfo', trans('fi.default_company_profile_set'));
            }
        }
        if ($companyProfile)
        {
            $this->agreementDate = Http::get('https://www.fusioninvoice.com/agreement-date/' . config('app.key') . '/' . urlencode($companyProfile->company) . '/' . $request_type . '/' . config('fi.version'))->body();
        }
        else
        {
            $this->agreementDate = null;
        }
    }

    public function checkAgreementDateAndVersion($requestType, $key)
    {
        $companyProfile = CompanyProfile::find(config('fi.defaultCompanyProfile'));
        if (!$companyProfile)
        {
            $companyProfile = CompanyProfile::query()->first();
            if ($companyProfile)
            {
                Setting::saveByKey('defaultCompanyProfile', $companyProfile->id);
                request()->session()->put('alertInfo', trans('fi.default_company_profile_set'));
            }
        }

        if ($companyProfile)
        {
            try
            {
                $results      = DB::select(DB::raw("select version()"));
                $mysqlVersion = isset($results[0]->{'version()'}) ? $results[0]->{'version()'} : 'UNKNOWN';
                $data         = [
                    'key'               => base64_encode($key),
                    'domain'            => base64_encode($_SERVER["HTTP_HOST"]),
                    'company'           => base64_encode($companyProfile->company),
                    'address'           => base64_encode($companyProfile->address),
                    'city'              => base64_encode($companyProfile->city),
                    'state'             => base64_encode($companyProfile->state),
                    'postal_code'       => base64_encode($companyProfile->zip),
                    'country'           => base64_encode($companyProfile->country),
                    'mail_from_address' => base64_encode(config('fi.mailFromAddress')),
                    'mail_from_name'    => base64_encode(config('fi.mailFromName')),
                    'php_version'       => base64_encode(PHP_VERSION),
                    'db_name'           => base64_encode(config('database.connections.mysql.database')),
                    'db_type'           => base64_encode(config('database.default')),
                    'db_version'        => base64_encode($mysqlVersion),
                    'web_server'        => base64_encode($_SERVER["SERVER_SOFTWARE"]),
                    'request_type'      => base64_encode($requestType),
                    'version'           => base64_encode(config('fi.version')),
                ];

                $apiClient  = $this->getApiClient();
                $apiRequest = [
                    'form_params' => $data,
                ];

                $apiResponse = $apiClient->post('https://www.fusioninvoice.com/admin/agreement/check', $apiRequest);
                // The above request is getting a 401 unauthorized response, so piracy check is always null. 
                $responseData = json_decode($apiResponse->getBody());
                if (isset($responseData) && $responseData->success == true)
                {
                    $this->agreementDate  = $responseData->expire_at;
                    $this->piracyStatus   = $responseData->pirated;
                    $this->currentVersion = $responseData->currentVersion;
                }

            }
            catch (\Exception $e)
            {
                $this->agreementDate = null;
            }

        }
        else
        {
            $this->agreementDate = null;
        }
    }

    public function keyVerification($data)
    {
        try
        {
            $apiClient  = $this->getApiClient();
            $apiRequest = [
                'form_params' => $data,
            ];

            $apiResponse  = $apiClient->post('https://www.fusioninvoice.com/api/key/verification', $apiRequest);
            $responseData = json_decode($apiResponse->getBody());

            if (isset($responseData) && $responseData->success == true)
            {
                return $responseData->verify;
            }

        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    /**
     * Check to see if there is a newer version available for download.
     *
     * @return boolean
     */
    public function updateAvailable()
    {
        $currentVersion = str_replace('-', '', trim($this->currentVersion));
        if (is_numeric($currentVersion) && $currentVersion > str_replace('-', '', config('fi.version')))
        {
            return true;
        }

        return false;
    }

    /**
     * Getter for current version.
     *
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    public function getPiracyStatus()
    {
        return $this->piracyStatus;
    }

    public function getAgreementExpireDate()
    {
        return $this->agreementDate;
    }

    protected function getApiClient()
    {
        $this->httpClient = new GuzzleHttp\Client(['verify' => false]);

        return $this->httpClient;
    }

}