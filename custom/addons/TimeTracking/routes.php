<?php

Route::group(['middleware' => ['web', 'auth.admin'], 'prefix' => 'time_tracking', 'namespace' => 'Addons\TimeTracking\Controllers'], function ()
{
    Route::group(['prefix' => 'projects'], function ()
    {
        Route::get('/', ['uses' => 'ProjectController@index', 'as' => 'timeTracking.projects.index'])->middleware('can:time_tracking.view');
        Route::get('create', ['uses' => 'ProjectController@create', 'as' => 'timeTracking.projects.create'])->middleware('can:time_tracking.create');
        Route::post('create', ['uses' => 'ProjectController@store', 'as' => 'timeTracking.projects.store'])->middleware('can:time_tracking.create');
        Route::get('{id}/edit', ['uses' => 'ProjectController@edit', 'as' => 'timeTracking.projects.edit'])->middleware('can:time_tracking.update');
        Route::post('{id}/edit', ['uses' => 'ProjectController@update', 'as' => 'timeTracking.projects.update'])->middleware('can:time_tracking.update');
        Route::get('{id}/delete', ['uses' => 'ProjectController@delete', 'as' => 'timeTracking.projects.delete'])->middleware('can:time_tracking.delete');
        Route::post('refresh_task_list', ['uses' => 'ProjectController@refreshTaskList', 'as' => 'timeTracking.projects.refreshTaskList'])->middleware('can:time_tracking.update');
        Route::post('refresh_totals', ['uses' => 'ProjectController@refreshTotals', 'as' => 'timeTracking.projects.refreshTotals'])->middleware('can:time_tracking.update');
        Route::post('delete/modal', ['uses' => 'ProjectController@deleteModal', 'as' => 'timeTracking.projects.delete.modal'])->middleware('can:time_tracking.delete');


        Route::group(['prefix' => 'preset_tasks'], function ()
        {

            Route::post('modal', ['uses' => 'ProjectController@presetTaskModal', 'as' => 'timeTracking.projects.preset.task.modal'])->middleware('can:time_tracking.update');
            Route::get('get', ['uses' => 'ProjectController@getPresetTasks', 'as' => 'timeTracking.projects.get.preset.tasks'])->middleware('can:time_tracking.update');
            Route::post('edit-update', ['uses' => 'ProjectController@presetTaskStore', 'as' => 'timeTracking.projects.preset.task.store'])->middleware('can:time_tracking.update');
            Route::post('delete/modal', ['uses' => 'ProjectController@deleteTasksModal', 'as' => 'timeTracking.projects.preset.task.delete.modal'])->middleware('can:time_tracking.delete');
            Route::get('{id}/delete', ['uses' => 'ProjectController@deletePresetTask', 'as' => 'timeTracking.projects.preset.task.delete'])->middleware('can:time_tracking.delete');

            Route::group(['prefix' => 'apply'], function ()
            {
                Route::post('modal', ['uses' => 'ProjectController@presetTaskApplyModal', 'as' => 'timeTracking.projects.preset.task.apply.modal'])->middleware('can:time_tracking.update');
                Route::post('apply', ['uses' => 'ProjectController@presetTaskApply', 'as' => 'timeTracking.projects.preset.task.apply'])->middleware('can:time_tracking.update');
                Route::get('get', ['uses' => 'ProjectController@getPresetTasksApply', 'as' => 'timeTracking.projects.get.preset.tasks.apply'])->middleware('can:time_tracking.update');
            });

            Route::group(['prefix' => 'items'], function ()
            {
                Route::post('modal', ['uses' => 'ProjectController@presetTaskItemModal', 'as' => 'timeTracking.projects.preset.task.item.modal'])->middleware('can:time_tracking.update');
                Route::post('get', ['uses' => 'ProjectController@getPresetTaskItems', 'as' => 'timeTracking.projects.get.preset.task.items'])->middleware('can:time_tracking.update');
                Route::post('edit-update', ['uses' => 'ProjectController@presetTaskItemStore', 'as' => 'timeTracking.projects.preset.task.item.store'])->middleware('can:time_tracking.update');
                Route::post('reorder', ['uses' => 'ProjectController@presetTaskItemsReorder', 'as' => 'timeTracking.projects.project.task.lists.reorder'])->middleware('can:time_tracking.update');
                Route::post('delete/modal', ['uses' => 'ProjectController@deleteTasksItemModal', 'as' => 'timeTracking.projects.preset.task.item.delete.modal'])->middleware('can:time_tracking.delete');
                Route::get('{id}/delete', ['uses' => 'ProjectController@deletePresetTaskItem', 'as' => 'timeTracking.projects.preset.task.item.delete'])->middleware('can:time_tracking.delete');
            });
        });


    });

    Route::group(['prefix' => 'tasks'], function ()
    {
        Route::post('create', ['uses' => 'TaskController@create', 'as' => 'timeTracking.tasks.create'])->middleware('can:time_tracking.create');
        Route::post('store', ['uses' => 'TaskController@store', 'as' => 'timeTracking.tasks.store'])->middleware('can:time_tracking.create');
        Route::post('edit', ['uses' => 'TaskController@edit', 'as' => 'timeTracking.tasks.edit'])->middleware('can:time_tracking.update');
        Route::post('update', ['uses' => 'TaskController@update', 'as' => 'timeTracking.tasks.update'])->middleware('can:time_tracking.update');
        Route::post('delete', ['uses' => 'TaskController@delete', 'as' => 'timeTracking.tasks.delete'])->middleware('can:time_tracking.delete');
        Route::post('update_display_order', ['uses' => 'TaskController@updateDisplayOrder', 'as' => 'timeTracking.tasks.updateDisplayOrder'])->middleware('can:time_tracking.update');
        Route::post('delete/modal', ['uses' => 'TaskController@deleteModal', 'as' => 'timeTracking.tasks.delete.modal'])->middleware('can:time_tracking.delete');

    });

    Route::group(['prefix' => 'timers'], function ()
    {
        Route::post('start', ['uses' => 'TimerController@start', 'as' => 'timeTracking.timers.start'])->middleware('can:time_tracking.create');
        Route::post('stop', ['uses' => 'TimerController@stop', 'as' => 'timeTracking.timers.stop'])->middleware('can:time_tracking.create');
        Route::post('show', ['uses' => 'TimerController@show', 'as' => 'timeTracking.timers.show'])->middleware('can:time_tracking.create');
        Route::post('seconds', ['uses' => 'TimerController@seconds', 'as' => 'timeTracking.timers.seconds'])->middleware('can:time_tracking.create');
        Route::post('store', ['uses' => 'TimerController@store', 'as' => 'timeTracking.timers.store'])->middleware('can:time_tracking.create');
        Route::post('delete', ['uses' => 'TimerController@delete', 'as' => 'timeTracking.timers.delete'])->middleware('can:time_tracking.delete');
        Route::post('refresh_list', ['uses' => 'TimerController@refreshList', 'as' => 'timeTracking.timers.refreshList'])->middleware('can:time_tracking.create');
        Route::post('delete/modal', ['uses' => 'TimerController@deleteModal', 'as' => 'timeTracking.timers.delete.modal'])->middleware('can:time_tracking.delete');

        Route::group(['prefix' => 'ajax'], function ()
        {
            Route::get('index', ['uses' => 'TimerController@index', 'as' => 'timeTracking.timers.ajax.index'])->middleware('can:time_tracking.create');
        });
        Route::group(['prefix' => 'setting'], function ()
        {
            Route::get('modal', ['uses' => 'TimerController@settingModal', 'as' => 'timeTracking.timers.setting.modal'])->middleware('can:time_tracking.create');
            Route::post('enable-disable', ['uses' => 'TimerController@enableDisable', 'as' => 'timeTracking.timers.enable.disable'])->middleware('can:time_tracking.create');
        });
    });

    Route::group(['prefix' => 'bill'], function ()
    {
        Route::post('create', ['uses' => 'TaskBillController@create', 'as' => 'timeTracking.bill.create'])->middleware('can:time_tracking.create');
        Route::post('store', ['uses' => 'TaskBillController@store', 'as' => 'timeTracking.bill.store'])->middleware('can:time_tracking.create');
    });

    Route::group(['prefix' => 'reports'], function ()
    {
        Route::get('timesheet', ['uses' => 'TimesheetReportController@index', 'as' => 'timeTracking.reports.timesheet'])->middleware('can:time_tracking.view');
        Route::post('timesheet/validate', ['uses' => 'TimesheetReportController@ajaxValidate', 'as' => 'timeTracking.reports.timesheet.validate'])->middleware('can:time_tracking.view');
        Route::get('timesheet/html', ['uses' => 'TimesheetReportController@html', 'as' => 'timeTracking.reports.timesheet.html'])->middleware('can:time_tracking.view');
        Route::get('timesheet/pdf', ['uses' => 'TimesheetReportController@pdf', 'as' => 'timeTracking.reports.timesheet.pdf'])->middleware('can:time_tracking.view');
        Route::get('timesheet/csv', ['uses' => 'TimesheetReportController@csv', 'as' => 'timeTracking.reports.timesheet.csv'])->middleware('can:time_tracking.view');
    });
});