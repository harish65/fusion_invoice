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

use FI\Modules\Settings\Models\UserSetting;

class DashboardWidgets
{
    public static function lists()
    {
        return Directory::listContents(__DIR__ . '/../Widgets/Dashboard');
    }

    public static function listsByOrder()
    {
        $widgets    = self::lists();
        $return     = [];
        $unassigned = 100;

        foreach ($widgets as $widget)
        {
            if (!$displayOrder = config('fi.widgetDisplayOrder' . $widget))
            {
                $displayOrder = $unassigned;
                $unassigned++;
            }

            if ($widget == str_contains($widget, 'KpiCards'))
            {
                $displayOrder = 1;
            }

            $return[str_pad($displayOrder, 3, 0, STR_PAD_LEFT) . '-' . $widget] = $widget;
        }

        ksort($return);

        return $return;
    }

    public static function dashboardCenterWidgetListsByOrder()
    {
        $centerWidget                 = [];
        $centerWidget['fullWidth']    = [];
        $centerWidget['dynamicWidth'] = [];
        $centerWidget['right']        = [];
        $widgets                      = self::listsByOrder();

        foreach ($widgets as $keys => $widget)
        {
            if ($widget != 'KpiCards')
            {
                $key          = 'widgetColumnWidth' . $widget;
                $userSettings = UserSetting::whereUserId(auth()->user()->id)->whereSettingKey($key)->first();
                if ($userSettings != null)
                {
                    if ($userSettings->setting_value == 'full_width')
                    {
                        $centerWidget['fullWidth'][] = $widget;
                    }
                    else
                    {
                        $centerWidget['dynamicWidth'][] = $widget;
                    }
                }
                else
                {
                    $centerWidget['dynamicWidth'][] = $widget;
                }
            }
        }

        $widgetCenter = UserSetting::whereUserId(auth()->user()->id)->whereSettingKey('widgetPositionCenter')->first();
        if ($widgetCenter == null)
        {
            $userSetting                = new UserSetting();
            $userSetting->user_id       = auth()->user()->id;
            $userSetting->setting_key   = 'widgetPositionCenter';
            $userSetting->setting_value = json_encode($centerWidget['fullWidth']);
            $userSetting->save();
        }
        else
        {
            $widgetCenter->setting_value = $centerWidget['fullWidth'];
            $widgetCenter->save();
        }
        return $centerWidget;
    }

    public static function dashboardWidgetListsByOrder()
    {
        $widgets              = (self::dashboardCenterWidgetListsByOrder()['dynamicWidth'] != null) ? array_values(self::dashboardCenterWidgetListsByOrder()['dynamicWidth']) : [];
        $widgetList['center'] = (self::dashboardCenterWidgetListsByOrder()['fullWidth'] != null) ? self::dashboardCenterWidgetListsByOrder()['fullWidth'] : [];
        $index                = 1;
        $widgetList['right']  = [];
        $widgetList['left']   = [];
        $user                 = auth()->user();
        $left                 = (UserSetting::getByKey('widgetPositionLeft', $user) != null) ? array_values(json_decode(UserSetting::getByKey('widgetPositionLeft', $user), true)) : null;
        $right                = (UserSetting::getByKey('widgetPositionRight', $user) != null) ? array_values(json_decode(UserSetting::getByKey('widgetPositionRight', $user), true)) : null;
        if ($left == null && $right == null)
        {
            if ($widgets != null)
            {
                foreach ($widgets as $key => $widget)
                {
                    if ($index <= intval(round(count($widgets) / 2)))
                    {
                        $widgetList['left'][] = $widget;
                    }
                    else
                    {
                        $widgetList['right'][] = $widget;
                    }
                    $index++;
                }
                userSettingUpdate('widgetPositionRight', json_encode(array_values($widgetList['right']), true), auth()->user()->id);
                userSettingUpdate('widgetPositionLeft', json_encode(array_values($widgetList['left']), true), auth()->user()->id);
            }
        }
        if ($left != null && $right != null)
        {
            $widgetList['left']  = json_decode(config('fi.widgetPositionLeft'), true);
            $widgetList['right'] = json_decode(config('fi.widgetPositionRight'), true);
        }

        if ($left == null && $right != null)
        {
            $widgetList['left']  = null;
            $widgetList['right'] = $widgets;
        }

        if ($left != null && $right == null)
        {
            $widgetList['right'] = null;
            $widgetList['left']  = $widgets;
        }

        return $widgetList;
    }
}