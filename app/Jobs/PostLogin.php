<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Jobs;

use Carbon\Carbon;
use Cookie;
use FI\Modules\Settings\Models\Setting;
use FI\Modules\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Session;

class PostLogin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->fiWritableFoldersTest();

        fiAgreementCheck(auth()->user(), Setting::getByKey('key'));

        try
        {
            $this->user->update(['last_login_at' => Carbon::now()]);
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        }
    }

    public function fiWritableFoldersTest()
    {
        $error         = [];
        $success       = [];
        $baseStorage   = storage_path();
        $baseMedia     = media_path();
        $baseBootStrap = base_path('bootstrap');

        $aFolders = [$baseStorage,
                     $baseStorage . DIRECTORY_SEPARATOR . 'app',
                     $baseStorage . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public',
                     $baseStorage . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'email_templates',
                     $baseStorage . DIRECTORY_SEPARATOR . 'framework',
                     $baseStorage . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache',
                     $baseStorage . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'data',
                     $baseStorage . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions',
                     $baseStorage . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views',
                     $baseStorage . DIRECTORY_SEPARATOR . 'logs',
                     $baseMedia,
                     $baseMedia . DIRECTORY_SEPARATOR . 'company_profile',
                     $baseBootStrap . DIRECTORY_SEPARATOR . 'cache'];

        foreach ($aFolders as $reqPath)
        {

            if (!is_dir($reqPath))
            {
                if (@mkdir($reqPath, 0777, true))
                {
                    Log::info('Created missing required folder: ' . $reqPath);
                    $success[] = trans('fi.create_missing_folder_success');
                }
                else
                {
                    Log::info('*ERROR* Attempt to create writable folder failed: ' . $reqPath);
                    $error['create'][] = $reqPath;
                }
            }

            if (!is_writable($reqPath))
            {
                $error['permission'][] = $reqPath;
                Log::info('*ERROR* Folder is not writable: ' . $reqPath);
            }
        }

        if (!empty($error))
        {
            Session::flash('errorFolderCreate', collect($error));
        }

        if (!empty($success))
        {
            Session::flash('successFolderCreate', collect($success));
        }

        $emailTemplateDirectory = $baseStorage . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'email_templates';

        if (is_dir($emailTemplateDirectory) && is_writable($emailTemplateDirectory) && count(scandir($emailTemplateDirectory)) <= 3)
        {
            Setting::writeEmailTemplates();
        }

    }
}
