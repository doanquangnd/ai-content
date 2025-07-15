<?php

use Botble\Base\Facades\AdminHelper;
use DoanQuang\AiContent\Http\Controllers\AiContentController;
use DoanQuang\AiContent\Http\Controllers\AiContentSettingController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'ai-contents', 'as' => 'ai-content.'], function () {
        Route::resource('', AiContentController::class)->parameters(['' => 'ai-content']);

        Route::controller(AiContentController::class)->group(function (): void {
            Route::get('stream', [
                'as' => 'stream',
                'uses' => 'stream',
            ]);
        });

        Route::controller(AiContentSettingController::class)->group(function (): void {
            Route::get('settings', [
                'as' => 'settings',
                'uses' => 'edit',
                'permission' => 'ai-content.settings',
            ]);

            Route::put('settings', [
                'as' => 'settings.update',
                'uses' => 'update',
                'permission' => 'ai-content.settings',
            ]);
        });
    });
});
