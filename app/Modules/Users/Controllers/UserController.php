<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Users\Controllers;

use Exception;
use FI\Http\Controllers\Controller;
use FI\Modules\Addons\Models\Addon;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CustomFields\Models\UserCustom;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\CustomFields\Support\CustomFieldsTransformer;
use FI\Modules\Settings\Models\Setting;
use FI\Modules\Settings\Models\UserSetting;
use FI\Modules\Users\Models\User;
use FI\Modules\Users\Models\UserPermissions;
use FI\Modules\Users\Requests\UserDashboardRequest;
use FI\Modules\Users\Requests\UserRequest;
use FI\Modules\Users\Requests\UserStoreRequest;
use FI\Modules\Users\Requests\UserUpdateRequest;
use FI\Support\DashboardWidgets;
use FI\Support\Skins;
use FI\Traits\ReturnUrl;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $users = User::sortable(['name' => 'asc'])
            ->userType(request('userType'))
            ->whereNotIn('user_type', ['system', 'paymentcenter_user'])
            ->leftJoin('users_custom', 'users_custom.user_id', '=', 'users.id')
            ->paginate(config('fi.resultsPerPage'));

        return view('users.index')
            ->with('users', $users)
            ->with('userTypes', User::getUserTypes())
            ->with('allUserTypes', User::getAllUserTypes());
    }

    public function create($userType)
    {
        $permissionsCopiedFrom = User::where('user_type', 'standard_user')->get()->pluck('name', 'id')->toArray();

        $view = view('users.admin_form')
            ->with('editMode', false)
            ->with('customFields', CustomFieldsParser::getFields('users'))
            ->with('userTypes', User::getAllUserTypes())
            ->with('enabledAddons', Addon::getEnabledAddons())
            ->with('userType', $userType)
            ->with('permissibleItems', UserPermissions::getAllPermissibleItems())
            ->with('permissionsCopiedFrom', ['' => trans('fi.select_user')] + $permissionsCopiedFrom)
            ->with('returnUrl', $this->getReturnUrl())
            ->with('yesNoArray', ['0' => trans('fi.no'), '1' => trans('fi.yes')])
            ->with('displayOrderArray', array_combine(range(1, 24), range(1, 24)))
            ->with('colWidthArray', array_combine(range(1, 12), range(1, 12)))
            ->with('status', User::getStatus())
            ->with('dashboardWidgets', DashboardWidgets::listsByOrder())
            ->with('kpiCardsSettings', ['draft_invoices' => 'DraftInvoices', 'sent_invoices' => 'SentInvoices', 'overdue_invoices' => 'OverdueInvoices', 'payments_collected' => 'PaymentsCollectedInvoices', 'draft_quotes' => 'DraftQuotes', 'sent_quotes' => 'SentQuotes', 'rejected_quotes' => 'RejectedQuotes', 'approved_quotes' => 'ApprovedQuotes']);

        return $view;
    }

    public function store(UserStoreRequest $request)
    {
        $user = new User($request->except('custom', 'permissions', 'check-all', 'permissions_copied_from', 'setting'));

        $user->password = $request->input('password');

        $user->save();

        $userSettings = $request->post('setting', []);

        $widgetEnables  = User::userDefaultSetting('widgetEnables');
        $widgetKpiCards = User::userDefaultSetting('widgetKpiCards');
        $systemSettings = Setting::pluck('setting_value', 'setting_key');

        foreach ($widgetEnables as $widget)
        {
            $userSettings[$widget] = isset($systemSettings[$widget]) ? $systemSettings[$widget] : 0;
        }

        if (!empty($userSettings))
        {
            foreach ($widgetKpiCards as $kpiCard)
            {
                $userSettings[$kpiCard] = isset($systemSettings[$kpiCard]) ? $systemSettings[$kpiCard] : 0;
            }

            foreach ($userSettings as $key => $userSetting)
            {
                UserSetting::saveByKey($key, $userSetting, $user);
            }
        }

        $permissions = $request->post('permissions', []);
        if (!empty($permissions))
        {
            foreach ($permissions as $module => $permission)
            {
                UserPermissions::create(array_merge(['user_id' => $user->id, 'module' => $module], $permission));
            }
        }

        // Save the custom fields.
        $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'users', $user);
        $user->custom->update($customFieldData);

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id, $userType)
    {
        $role = $userType;
        if ('client' !== $userType)
        {
            $userType = 'admin';
        }
        $user = User::find($id);

        $permissions = $user->permissions->keyBy('module')->toArray();

        $permissionsCopiedFrom = User::where('user_type', 'standard_user')->where('id', '!=', $id)->get()->pluck('name', 'id')->toArray();

        $userSettings = UserSetting::whereUserId($id)->pluck('setting_value', 'setting_key')->all();

        return view('users.' . $userType . '_form')
            ->with(['editMode' => true, 'user' => $user])
            ->with('customFields', CustomFieldsParser::getFields('users'))
            ->with('userTypes', User::getUserTypes())
            ->with('userType', $role)
            ->with('permissibleItems', UserPermissions::getAllPermissibleItems())
            ->with('permissions', $permissions)
            ->with('enabledAddons', Addon::getEnabledAddons())
            ->with('returnUrl', $this->getReturnUrl())
            ->with('permissionsCopiedFrom', ['' => trans('fi.select_user')] + $permissionsCopiedFrom)
            ->with('dashboardWidgets', DashboardWidgets::listsByOrder())
            ->with('yesNoArray', ['0' => trans('fi.no'), '1' => trans('fi.yes')])
            ->with('displayOrderArray', array_combine(range(1, 24), range(1, 24)))
            ->with('status', User::getStatus())
            ->with('colWidthArray', array_combine(range(3, 12), range(3, 12)))
            ->with('userSettings', $userSettings)
            ->with('kpiCardsSettings', ['draft_invoices' => 'DraftInvoices', 'sent_invoices' => 'SentInvoices', 'overdue_invoices' => 'OverdueInvoices', 'payments_collected' => 'PaymentsCollectedInvoices', 'draft_quotes' => 'DraftQuotes', 'sent_quotes' => 'SentQuotes', 'rejected_quotes' => 'RejectedQuotes', 'approved_quotes' => 'ApprovedQuotes']);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::find($id);

        $adminCount = User::whereUserType('admin')->get()->count();
        if ($adminCount == 1 && $user->user_type == 'admin' && $request->user_type != 'admin')
        {
            return redirect()->route('users.index')
                ->with('alert', trans('fi.can-not-change-default-user-role'));
        }

        if ($adminCount == 1 && $user->user_type == 'admin' && isset($request->status) && $request->status != 1)
        {
            return redirect()->route('users.index')
                ->with('alert', trans('fi.can-not-change-default-user-status'));
        }

        if (isset($request->status) && $request->status != 1 && auth()->user()->id == $id)
        {
            return redirect()->route('users.index')
                ->with('alert', trans('fi.can-not-inactive-your-own-status'));
        }

        $userSettings = $request->post('setting', []);

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
        $user->fill($request->except('custom', 'permissions', 'check-all', 'permissions_copied_from', 'setting'));

        $user->save();

        $defaultPermissions = ['is_view' => 0, 'is_create' => 0, 'is_update' => 0, 'is_delete' => 0];
        $permissions        = $request->post('permissions', []);

        if (!empty($permissions))
        {
            foreach ($user->permissions as $userPermission)
            {

                $permission     = $permissions[$userPermission->module] ?? $defaultPermissions;
                $permissionData = array_merge($defaultPermissions, $permission);
                unset($permissions[$userPermission->module]);
                $userPermission->update($permissionData);

                if ($userPermission->module == 'allow_time_period_change' && $userPermission->is_view == 0)
                {
                    UserSetting::deleteByKey('dashboardWidgetsDateOption', $user);
                }

            }

            foreach ($permissions as $module => $permission)
            {
                UserPermissions::create(array_merge(['user_id' => $user->id, 'module' => $module], $permission));
            }
        }
        else
        {
            $user->permissions->each(function ($userPermission) use ($defaultPermissions) {
                $userPermission->update($defaultPermissions);
            });
        }

        //If user type is changed from standard users to another type then we have to delete all permissions
        if ($user->user_type != 'standard_user')
        {
            UserPermissions::whereUserId($user->id)->delete();
        }

        //To avoid issues with manually created users, add the related custom fields entry automatically. 
        if (!$user->custom()->exists())
        {
            $user->custom()->save(new UserCustom());
        }

        // Save the custom fields.
        $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'users', $user);
        $user->custom->update($customFieldData);

        return redirect()->route('users.index')
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        $adminCount = User::whereUserType('admin')->get()->count();
        $isAdmin    = User::whereId($id)->whereUserType('admin')->first();
        if ($adminCount == 1 && $isAdmin)
        {
            return redirect()->route('users.index')
                ->with('alert', trans('fi.can-not-delete-all-users'));
        }

        User::destroy($id);

        return redirect()->route('users.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function getClientInfo()
    {
        return Client::find(request('id'));
    }

    public function deleteImage($id, $columnName)
    {
        $customFields = UserCustom::whereUserId($id)->first();

        $existingFile = 'users' . DIRECTORY_SEPARATOR . $customFields->{$columnName};
        if (Storage::disk(CustomFieldsTransformer::STORAGE_DISK_NAME)->exists($existingFile))
        {
            try
            {
                Storage::disk(CustomFieldsTransformer::STORAGE_DISK_NAME)->delete($existingFile);
                $customFields->{$columnName} = null;
                $customFields->save();
            }
            catch (Exception $e)
            {

            }
        }
    }

    public function getPermissions($id)
    {
        $permissions = User::find($id)->permissions->toArray();

        return response()->json($permissions);
    }

    public function updateStatus($id)
    {
        $user = User::find($id);

        $adminCount = User::whereUserType('admin')->get()->count();

        if ($adminCount == 1 && $user->user_type == 'admin')
        {
            return redirect()->route('users.index')
                ->with('alert', trans('fi.can-not-change-default-user-status'));
        }

        if (auth()->user()->id == $id)
        {
            return redirect()->route('users.index')
                ->with('alert', trans('fi.can-not-inactive-your-own-status'));
        }

        $user->status = 0;

        $user->save();

        return redirect()->route('users.index')
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function defaultSetting($id)
    {
        $userDefaultSetting = User::userDefaultSetting();
        $user               = User::whereId($id)->first();
        foreach ($userDefaultSetting['widgetColumnsWidth'] as $widgetColumnWidth)
        {
            userSettingUpdate($widgetColumnWidth, 'dynamic_width', $id);
        }

        foreach ($userDefaultSetting['widgetEnables'] as $widgetEnable)
        {
            userSettingUpdate($widgetEnable, 1, $id);
        }
        foreach ($userDefaultSetting['widgetOtherSettings'] as $widgetEnable)
        {
            userSettingUpdate($widgetEnable, 0, $id);
        }

        foreach ($userDefaultSetting['widgetDisplayOrders'] as $index => $widgetDisplayOrder)
        {
            userSettingUpdate($widgetDisplayOrder, $index + 2, $id);
        }

        foreach ($userDefaultSetting['widgetPositions'] as $widgetPosition)
        {
            UserSetting::deleteByKey($widgetPosition, $user);
        }

        return response()->json(['success' => true, 'tab' => 'dashboard', 'message' => trans('fi.default_configuration_set')], 200);
    }

    public function getUsers()
    {
        $id = request('userParentId');
        return view('users._modal_user_assign_setting')
            ->with('users', User::getDropDownList($id))
            ->with('userParentId', $id);
    }

    public function setUsersSetting(UserRequest $request)
    {
        $userDefaultSetting = User::userDefaultSetting();
        $userParentId       = request('userParentId');
        foreach (request('userId') as $id)
        {
            $authUserSetting = UserSetting::whereUserId($userParentId)->pluck('setting_value', 'setting_key')->all();

            foreach ($userDefaultSetting['widgetColumnsWidth'] as $widgetColumnWidth)
            {
                userSettingUpdate($widgetColumnWidth, isset($authUserSetting[$widgetColumnWidth]) ? $authUserSetting[$widgetColumnWidth] : 'dynamic_width', $id);
            }
            foreach ($userDefaultSetting['widgetEnables'] as $widgetEnable)
            {
                userSettingUpdate($widgetEnable, isset($authUserSetting[$widgetEnable]) ? $authUserSetting[$widgetEnable] : 0, $id);
            }
            foreach ($userDefaultSetting['widgetOtherSettings'] as $widgetEnable)
            {
                userSettingUpdate($widgetEnable, isset($authUserSetting[$widgetEnable]) ? $authUserSetting[$widgetEnable] : 0, $id);
            }
            foreach ($userDefaultSetting['widgetDisplayOrders'] as $index => $widgetDisplayOrder)
            {
                userSettingUpdate($widgetDisplayOrder, isset($authUserSetting[$widgetDisplayOrder]) ? $authUserSetting[$widgetDisplayOrder] : $index + 2, $id);
            }
            foreach ($userDefaultSetting['widgetKpiCards'] as $widgetKpiCard)
            {
                userSettingUpdate($widgetKpiCard, isset($authUserSetting[$widgetKpiCard]) ? $authUserSetting[$widgetKpiCard] : 1, $id);
            }
            foreach ($userDefaultSetting['widgetPositions'] as $widgetPosition)
            {
                $userSettings = UserSetting::whereSettingKey($widgetPosition)->whereUserId($id)->first();
                if ($userSettings == null)
                {
                    $userSetting                = new UserSetting();
                    $userSetting->user_id       = $id;
                    $userSetting->setting_key   = $widgetPosition;
                    $userSetting->setting_value = json_encode([]);
                    $userSetting->save();
                }
                else
                {
                    $userSettings->setting_value = isset($authUserSetting[$widgetPosition]) ? $authUserSetting[$widgetPosition] : json_encode([]);
                    $userSettings->save();
                }
            }
        }

        return response()->json(['success' => true, 'message' => trans('fi.configuration_assigned')], 200);
    }

    public function userWidthSetting()
    {
        $id = request('id');
        if ($id != null)
        {
            $user      = User::whereId($id)->first();
            $widthType = request('value');
            $widthName = request('name');
            $center    = (UserSetting::getByKey('widgetPositionCenter', $user) != null) ? json_decode(UserSetting::getByKey('widgetPositionCenter', $user), true) : null;
            $left      = (UserSetting::getByKey('widgetPositionLeft', $user) != null) ? json_decode(UserSetting::getByKey('widgetPositionLeft', $user), true) : null;
            $right     = (UserSetting::getByKey('widgetPositionRight', $user) != null) ? json_decode(UserSetting::getByKey('widgetPositionRight', $user), true) : null;
            if ($widthType == 'full_width')
            {
                $center[] = $widthName;

                if ($left != null)
                {
                    if (in_array($widthName, $left) == true)
                    {
                        foreach (array_keys($left, $widthName) as $key)
                        {
                            unset($left [$key]);
                        }
                    }
                }

                if ($right != null)
                {
                    if (in_array($widthName, $right) == true)
                    {
                        foreach (array_keys($right, $widthName) as $key)
                        {
                            unset($right [$key]);
                        }
                    }
                }

            }
            else
            {
                $left[] = $widthName;
                if ($center != null)
                {
                    if (in_array($widthName, $center) == true)
                    {
                        foreach (array_keys($center, $widthName) as $key)
                        {
                            unset($center [$key]);
                        }
                    }
                }

            }
            $data = [
                'widgetPositionCenter' => $center,
                'widgetPositionLeft'   => ($left != null) ? array_unique($left) : [],
                'widgetPositionRight'  => ($right != null) ? array_unique($right) : [],
            ];

            $widgetPositions = [];
            foreach ($data as $key => $value)
            {
                $widgetPositions[$key] = json_encode(array_values($value), true);
            }
            $widgetPositions['widgetColumnWidth' . $widthName] = $widthType;

            foreach ($widgetPositions as $key => $value)
            {
                userSettingUpdate($key, $value, $id);
            }
            return response()->json(['success' => true, 'message' => trans('fi.dashboard_width', ['widthName' => $widthName])], 200);

        }
    }

    public function deleteModal()
    {
        try
        {
            return view('users._delete_modal_details')->with('url', request('action'))->with('message', request('message'))->with('returnURL', request('returnURL'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function dashboardUserModal()
    {
        try
        {
            $user = User::find(request('userId'));

            return view('users._dashboard_user_modal')
                ->with('skins', Skins::lists())
                ->with('modalName', request('modalName'))->with('user', $user);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function dashboardUserUpdateModal(UserDashboardRequest $request)
    {
        try
        {
            $user = User::find(request('userId'));

            if (request('password') != null)
            {
                $user->password = $request->input('password');
            }
            $user->fill($request->except('skin', '_token', 'userId', 'password_confirmation', 'password'));
            $user->save();

            UserSetting::saveByKey('skin', request('skin'), $user);

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_updated')], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }


    }
}