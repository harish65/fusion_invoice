<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Settings\Controllers;

use Exception;
use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Currencies\Models\Currency;
use FI\Modules\DocumentNumberSchemes\Models\DocumentNumberScheme;
use FI\Modules\Invoices\Support\InvoiceTemplates;
use FI\Modules\MailQueue\Support\MailQueue;
use FI\Modules\MailQueue\Support\MailSettings;
use FI\Modules\Merchant\Support\MerchantFactory;
use FI\Modules\PaymentMethods\Models\PaymentMethod;
use FI\Modules\Quotes\Support\QuoteTemplates;
use FI\Modules\Settings\Models\Setting;
use FI\Modules\Settings\Models\UserSetting;
use FI\Modules\Settings\Requests\SettingUpdateRequest;
use FI\Modules\Setup\Requests\KeyVerificationRequest;
use FI\Modules\TaxRates\Models\TaxRate;
use FI\Modules\Users\Models\User;
use FI\Support\DashboardWidgets;
use FI\Support\DateFormatter;
use FI\Support\Languages;
use FI\Support\PDF\PDFFactory;
use FI\Support\Skins;
use FI\Support\Statuses\InvoiceStatuses;
use FI\Support\Statuses\QuoteStatuses;
use FI\Support\UpdateChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Session;
use Storage;

class SettingController extends Controller
{

    private $mailQueue;

    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function index()
    {
        try
        {
            $mailPassword = Crypt::decrypt(config('fi.mailPassword'));
            session()->forget('error');
        }
        catch (Exception $e)
        {
            $mailPassword = '';
        }

        return view('settings.index')
            ->with([
                'languages'                => Languages::listLanguages(),
                'dateFormats'              => DateFormatter::dropdownArray(),
                'invoiceTemplates'         => InvoiceTemplates::lists(),
                'quoteTemplates'           => QuoteTemplates::lists(),
                'documentNumberSchemes'    => DocumentNumberScheme::getList(),
                'taxRates'                 => TaxRate::getList(),
                'paymentMethods'           => PaymentMethod::getList(),
                'emailSendMethods'         => MailSettings::listSendMethods(),
                'emailEncryptions'         => MailSettings::listEncryptions(),
                'yesNoArray'               => ['0' => trans('fi.no'), '1' => trans('fi.yes')],
                'timezones'                => array_combine(timezone_identifiers_list(), timezone_identifiers_list()),
                'paperSizes'               => ['letter' => trans('fi.letter'), 'A4' => trans('fi.a4'), 'legal' => trans('fi.legal')],
                'paperOrientations'        => ['portrait' => trans('fi.portrait'), 'landscape' => trans('fi.landscape')],
                'currencies'               => Currency::getList(),
                'exchangeRateModes'        => ['automatic' => trans('fi.automatic'), 'manual' => trans('fi.manual')],
                'pdfDrivers'               => PDFFactory::getDrivers(),
                'convertQuoteOptions'      => ['quote' => trans('fi.convert_quote_option1'), 'invoice' => trans('fi.convert_quote_option2')],
                'colWidthArray'            => array_combine(range(1, 12), range(1, 12)),
                'displayOrderArray'        => array_combine(range(1, 24), range(1, 24)),
                'merchant'                 => config('fi.merchant'),
                'skins'                    => Skins::lists(),
                'resultsPerPage'           => array_combine(range(15, 100, 5), range(15, 100, 5)),
                'amountDecimalOptions'     => ['0' => '0', '2' => '2', '3' => '3', '4' => '4'],
                'roundTaxDecimalOptions'   => ['2' => '2', '3' => '3', '4' => '4'],
                'companyProfiles'          => CompanyProfile::getList(),
                'merchantDrivers'          => MerchantFactory::getDrivers(),
                'invoiceStatuses'          => ['all' => trans('fi.all_statuses')] + InvoiceStatuses::lists(),
                'quoteStatuses'            => ['all' => trans('fi.all_statuses')] + QuoteStatuses::lists(),
                'invoiceWhenDraftOptions'  => [0 => trans('fi.keep_invoice_date_as_is'), 1 => trans('fi.change_invoice_date_to_todays_date')],
                'quoteWhenDraftOptions'    => [0 => trans('fi.keep_quote_date_as_is'), 1 => trans('fi.change_quote_date_to_todays_date')],
                'taskResultsPerPage'       => array_combine(range(5, 20), range(5, 20)),
                'mailPassword'             => $mailPassword,
                'customFieldColWidthArray' => ['12' => '1', '6' => '2', '4' => '3'],
                'settings'                 => Setting::getSettingKeyValuePair(),
                'numberOfTaxFieldsArray'   => [1 => trans('fi.tax_1_entry'), 2 => trans('fi.tax_2_entries')],
                'showInvoicesFrom'         => ['userWhoCreatedInvoice' => trans('fi.user_who_created_invoice'), 'systemMailFromAddress' => trans('fi.system_mail_from_address')],
                'topBarColor'              => Skins::topBarColorLists(),
                'clientTypes'              => ['' => trans('fi.show_all_types')] + Client::getTypesList(),
            ]);
    }

    public function update(SettingUpdateRequest $request)
    {
        $settings                                       = $request->input('setting');
        $settings['overdueAttachInvoice']               = $settings['overdueAttachInvoice'] ?? 0;
        $settings['upcomingPaymentNoticeAttachInvoice'] = $settings['upcomingPaymentNoticeAttachInvoice'] ?? 0;
        $settings['paymentAttachInvoice']               = $settings['paymentAttachInvoice'] ?? 0;
        $settings['quoteAttachPDF']                     = $settings['quoteAttachPDF'] ?? 0;
        $settings['invoiceAttachPDF']                   = $settings['invoiceAttachPDF'] ?? 0;
        $settings['secure_link']                        = $settings['secure_link'] ?? 0;

        $alert = false;
        $error = [];
        foreach ($settings as $key => $value)
        {
            $skipSave = false;

            if ($key == 'mailPassword' and $value)
            {
                $value = Crypt::encrypt($value);
            }

            if ($key == 'merchant')
            {
                $value = json_encode($value);
            }

            if ($key == 'mailFromAddress' && $value)
            {
                $user = User::whereUserType('system')->first();

                if ($user && $user->email !== $value)
                {
                    $user->email = $value;
                    $user->save();
                }
            }

            if ($key == 'paymentReceiptBody' && $value == "custom")
            {
                if (!view()->exists('email_templates.payment.paymentReceiptBody'))
                {
                    $alert    = true;
                    $skipSave = true;
                    $error[]  = trans('fi.custom_payment_receipt_body_not_exists');
                }
            }

            if (!$skipSave)
            {
                Setting::saveByKey($key, $value);
            }
        }

        Setting::writeEmailTemplates();

        session()->forget('error');

        return $alert == true ?
            redirect()->route('settings.index')
                ->with('alertSuccess', trans('fi.settings_successfully_saved'))
                ->with('alert', implode(',', $error))
            : redirect()->route('settings.index')->with('alertSuccess', trans('fi.settings_successfully_saved'));
    }

    public function updateCheck()
    {
        $updateChecker = new UpdateChecker;

        $updateChecker->checkVersion('manual');
        $updateAvailable = $updateChecker->updateAvailable();
        $currentVersion  = $updateChecker->getCurrentVersion();

        if ($updateAvailable)
        {
            $message = trans('fi.update_available', ['version' => $currentVersion]);
        }
        else
        {
            $message = trans('fi.update_not_available');
        }

        return response()->json(
            [
                'success' => true,
                'message' => $message,
            ], 200
        );
    }

    public function saveTab()
    {
        session(['settingTabId' => request('settingTabId')]);
    }

    public function pdfCleanup()
    {
        if (!config('app.demo'))
        {
            $files = glob(storage_path('app/public/') . "*.pdf");
            foreach ($files as $file)
            {
                if (is_file($file))
                {
                    if (time() - filemtime($file) >= 60 * 60 * 24 * 7)
                    {
                        unlink($file);
                    }
                }
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => trans('fi.pdf_cleanup_success'),
                ], 200
            );
        }
        else
        {
            return response()->json(
                [
                    'success' => false,
                    'message' => trans('fi.functionality_not_available_on_demo'),
                ], 401
            );
        }
    }

    public function cacheCleanup(Request $request)
    {
        try
        {
            if (!config('app.demo'))
            {
                Artisan::call(' optimize:clear ');
                return response()->json(
                    [
                        'success' => true,
                        'message' => trans('fi.cache_clear_success'),
                    ], 200
                );
            }
            else
            {
                return response()->json(
                    [
                        'success' => false,
                        'message' => trans('fi.functionality_not_available_on_demo'),
                    ], 401
                );
            }
        }
        catch (Exception $e)
        {
            return response()->json(
                [
                    'error'   => true,
                    'message' => trans('fi.cache_clear_error'),
                ], 400
            );
        }
    }

    public function verifyAndUpdateKey(KeyVerificationRequest $request)
    {
        $key        = trim($request->get('key'));
        $settingKey = Setting::where('setting_key', 'key')->first();
        if (isset($settingKey) && strlen($settingKey->setting_value) >= 32 && $settingKey->setting_value != $key)
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
                'request_type'      => base64_encode('update_key'),
                'version'           => base64_encode(config('fi.version')),
            ];

            $updateChecker      = new UpdateChecker;
            $verificationStatus = $updateChecker->keyVerification($data);

            if ($verificationStatus == true)
            {
                Setting::where(['setting_key' => 'key'])->update(
                    [
                        'setting_value' => $key,
                    ]
                );

                Config::write('app.key', $key);

                return response()->json(
                    [
                        'success' => true,
                        'message' => trans('fi.key_updated'),
                    ], 200
                );
            }
            else
            {
                return response()->json(
                    [
                        'success' => false,
                        'message' => trans('fi.trying_invalid_key'),
                    ], 200
                );
            }

        }
        else
        {
            return response()->json(
                [
                    'success' => false,
                    'message' => trans('fi.same_key_error'),
                ], 200
            );
        }
    }

    public function generateTimelineModal()
    {
        try
        {
            return view('settings._modal_generate_timeline')
                ->with('url', request('action'))
                ->with('modalName', request('modalName'))
                ->with('message', request('message'))
                ->with('returnURL', request('returnURL'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function indexSystemDefaultDashboard()
    {
        return view('settings._dashboard')->with([
            'dashboardWidgetsDateOptions' => periods(),
            'settings'                    => Setting::getSettingKeyValuePair(),
            'dashboardWidgets'            => DashboardWidgets::listsByOrder(),
            'kpiCardsSettings'            => ['draft_invoices' => 'DraftInvoices', 'sent_invoices' => 'SentInvoices', 'overdue_invoices' => 'OverdueInvoices', 'payments_collected' => 'PaymentsCollectedInvoices', 'draft_quotes' => 'DraftQuotes', 'sent_quotes' => 'SentQuotes', 'rejected_quotes' => 'RejectedQuotes', 'approved_quotes' => 'ApprovedQuotes'],
            'yesNoArray'                  => ['0' => trans('fi.no'), '1' => trans('fi.yes')],
        ]);
    }

    public function updateSystemDefaultDashboard(Request $request)
    {
        $settings                                       = $request->input('setting');
        $settings['dashboardApprovedQuotes']            = $settings['dashboardApprovedQuotes'] ?? 0;
        $settings['dashboardRejectedQuotes']            = $settings['dashboardRejectedQuotes'] ?? 0;
        $settings['dashboardSentQuotes']                = $settings['dashboardSentQuotes'] ?? 0;
        $settings['dashboardDraftQuotes']               = $settings['dashboardDraftQuotes'] ?? 0;
        $settings['dashboardPaymentsCollectedInvoices'] = $settings['dashboardPaymentsCollectedInvoices'] ?? 0;
        $settings['dashboardOverdueInvoices']           = $settings['dashboardOverdueInvoices'] ?? 0;
        $settings['dashboardSentInvoices']              = $settings['dashboardSentInvoices'] ?? 0;
        $settings['dashboardDraftInvoices']             = $settings['dashboardDraftInvoices'] ?? 0;

        $alert = false;
        $error = [];
        foreach ($settings as $key => $value)
        {
            $skipSave = false;

            if (!$skipSave)
            {
                Setting::saveByKey($key, $value);
            }
        }

        session()->forget('error');

        return $alert == true ?
            redirect()->route('settings.system.default.dashboard.index')
                ->with('alertSuccess', trans('fi.settings_successfully_saved'))
                ->with('alert', implode(',', $error))
            : redirect()->route('settings.system.default.dashboard.index')->with('alertSuccess', trans('fi.settings_successfully_saved'));
    }

    public function indexUserSpecificDashboards()
    {
        $users = User::getAllUsersList();

        return view('settings._user_dashboard')
            ->with('users', $users)
            ->with('yesNoArray', ['0' => trans('fi.no'), '1' => trans('fi.yes')])
            ->with('dashboardWidgets', DashboardWidgets::listsByOrder())
            ->with('kpiCardsSettings', ['draft_invoices' => 'DraftInvoices', 'sent_invoices' => 'SentInvoices', 'overdue_invoices' => 'OverdueInvoices', 'payments_collected' => 'PaymentsCollectedInvoices', 'draft_quotes' => 'DraftQuotes', 'sent_quotes' => 'SentQuotes', 'rejected_quotes' => 'RejectedQuotes', 'approved_quotes' => 'ApprovedQuotes']);
    }

    public function indexUserSpecificSettings($id)
    {
        $user = User::find($id);

        if ($user)
        {
            $userSettings = UserSetting::whereUserId($id)->pluck('setting_value', 'setting_key')->all();
            $users        = User::getAllUsersList();

            return view('settings._user_dashboard')
                ->with('userType', $user->user_type)
                ->with('user', $user)
                ->with('users', $users)
                ->with('yesNoArray', ['0' => trans('fi.no'), '1' => trans('fi.yes')])
                ->with('dashboardWidgets', DashboardWidgets::listsByOrder())
                ->with('userSettings', $userSettings)
                ->with('kpiCardsSettings', ['draft_invoices' => 'DraftInvoices', 'sent_invoices' => 'SentInvoices', 'overdue_invoices' => 'OverdueInvoices', 'payments_collected' => 'PaymentsCollectedInvoices', 'draft_quotes' => 'DraftQuotes', 'sent_quotes' => 'SentQuotes', 'rejected_quotes' => 'RejectedQuotes', 'approved_quotes' => 'ApprovedQuotes']);
        }
        else
        {
            return redirect()->route('settings.user.specific.dashboard.index')->with('alert', trans('fi.invalid_user'));;
        }
    }

    public function updateUserSpecificDashboards($id)
    {
        $user = User::find($id);

        $userSettings = request()->post('setting', []);

        if (!empty($userSettings))
        {
            $userSettings['dashboardApprovedQuotes']            = $userSettings['dashboardApprovedQuotes'] ?? 0;
            $userSettings['dashboardRejectedQuotes']            = $userSettings['dashboardRejectedQuotes'] ?? 0;
            $userSettings['dashboardSentQuotes']                = $userSettings['dashboardSentQuotes'] ?? 0;
            $userSettings['dashboardDraftQuotes']               = $userSettings['dashboardDraftQuotes'] ?? 0;
            $userSettings['dashboardPaymentsCollectedInvoices'] = $userSettings['dashboardPaymentsCollectedInvoices'] ?? 0;
            $userSettings['dashboardOverdueInvoices']           = $userSettings['dashboardOverdueInvoices'] ?? 0;
            $userSettings['dashboardSentInvoices']              = $userSettings['dashboardSentInvoices'] ?? 0;
            $userSettings['dashboardDraftInvoices']             = $userSettings['dashboardDraftInvoices'] ?? 0;
            foreach ($userSettings as $key => $userSetting)
            {
                UserSetting::saveByKey($key, $userSetting, $user);
            }
        }

        return redirect()->route('settings.user.specific.dashboard.settings', [$id])
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }
}
