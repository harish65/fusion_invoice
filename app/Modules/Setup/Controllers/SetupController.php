<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Setup\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Countries\Models\Country;
use FI\Modules\Settings\Models\Setting;
use FI\Modules\Setup\Requests\LicenseRequest;
use FI\Modules\Setup\Requests\KeyVerificationRequest;
use FI\Modules\Setup\Requests\ProfileRequest;
use FI\Modules\Users\Models\User;
use FI\Support\Migrations;
use FI\Support\UpdateChecker;
use FI\Traits\ReturnUrl;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SetupController extends Controller
{
    use ReturnUrl;
    private $migrations;

    public function __construct(Migrations $migrations)
    {
        $this->migrations = $migrations;
    }

    public function index()
    {
        return view('setup.index')
            ->with('license', file_get_contents(public_path('LICENSE')));
    }

    public function postIndex(LicenseRequest $request)
    {
        return redirect()->route('setup.prerequisites');
    }

    public function prerequisites()
    {
        $errors          = [];
        $versionRequired = '7.2.5';
        $dbDriver        = config('database.default');
        $dbConfig        = config('database.connections.' . $dbDriver);

        if (version_compare(phpversion(), $versionRequired, '<'))
        {
            $errors[] = sprintf(trans('fi.php_version_error'), $versionRequired);
        }

        if (!$dbConfig['host'] or !$dbConfig['database'] or !$dbConfig['username'] or !$dbConfig['password'])
        {
            $errors[] = trans('fi.database_not_configured');
        }

        if (!$errors)
        {
            return redirect()->route('setup.migration');
        }

        return view('setup.prerequisites')
            ->with('errors', $errors);
    }

    public function migration()
    {
        return view('setup.migration');
    }

    public function postMigration()
    {
        if ($this->migrations->runMigrations(database_path('migrations')))
        {
            return response()->json([], 200);
        }

        return response()->json(['exception' => $this->migrations->getException()->getMessage()], 400);
    }

    public function account()
    {
        if (!User::where('user_type', '<>', 'system')->count())
        {
            return view('setup.account')->with('countries', Country::getAll());
        }

        return redirect()->route('setup.complete');
    }

    public function postAccount(ProfileRequest $request)
    {
        if (!User::where('user_type', '<>', 'system')->count())
        {
            $input = $request->all();

            unset($input['user']['password_confirmation']);

            $user = new User($input['user']);

            $user->password = $input['user']['password'];

            $user->user_type = 'admin';

            $user->save();

            $input['company_profile']['is_default'] = 1;

            $companyProfile = CompanyProfile::create($input['company_profile']);

            Setting::saveByKey('defaultCompanyProfile', $companyProfile->id);

            Setting::saveByKey('mailFromAddress', $user->email);
            User::whereUserType('system')->update(['email' => $user->email]);

        }

        return redirect()->route('setup.verify.key');
    }

    public function verifyKey()
    {
        if (!Setting::where('setting_key', 'key')->count())
        {
            return view('setup.verify-key');
        }
        else if (!User::where('user_type', '<>', 'system')->count())
        {
            return redirect()->route('setup.account');
        }

        return redirect()->route('setup.complete');
    }

    public function postVerifyKey(KeyVerificationRequest $request)
    {
        $key        = trim($request->get('key'));
        $settingKey = Setting::where('setting_key', 'key')->first();

        if (isset($settingKey) && strlen($settingKey->setting_value) >= 32)
        {
            return redirect()->route('setup.complete')->with('alertSuccess', trans('fi.key_verified'));
        }
        else
        {
            $companyProfile = CompanyProfile::whereIsDefault(1)->first();
            $results        = DB::select(DB::raw("select version()"));
            $mysqlVersion   = isset($results[0]->{'version()'}) ? $results[0]->{'version()'} : 'UNKNOWN';
            $data           = [
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
                'request_type'      => base64_encode('auto'),
                'version'           => base64_encode(config('fi.version')),
            ];

            $updateChecker      = new UpdateChecker;
            $verificationStatus = $updateChecker->keyVerification($data);

            if ($verificationStatus == true)
            {
                Setting::create(
                    [
                        'setting_key'   => 'key',
                        'setting_value' => $key,
                    ]
                );

                Config::write('app.key', $key);

                return redirect()->route('setup.complete')->with('alertSuccess', trans('fi.key_verified'));
            }
        }

        return redirect()->route('setup.verify.key')->with('alert', trans('fi.invalid_key'));

    }

    public function complete()
    {
        if (!Setting::where('setting_key', 'key')->count())
        {
            return redirect()->route('setup.verify.key');
        }
        else if (!User::where('user_type', '<>', 'system')->count())
        {
            return redirect()->route('setup.account');
        }

        return view('setup.complete');
    }
}