<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\BaseHelper;
use DoanQuang\AiContent\Http\Controllers\AiContentController;
use DoanQuang\AiContent\Http\Controllers\AiContentSettingController;
use Illuminate\Support\Facades\Route;

Route::group([
    'controller' => AiContentController::class,
    'middleware' => ['web', 'core'],
], function () {
    Route::group([
        'prefix' => BaseHelper::getAdminPrefix() . '/ai-contents',
        'as' => 'ai-content.',
    ], function () {

        Route::resource('', AiContentController::class)->parameters(['' => 'ai-content']);

        Route::controller(AiContentController::class)->group(function (): void {
            Route::get('stream', [
                'as' => 'stream',
                'uses' => 'stream',
            ]);
        });
    });
});

Route::group([
    'controller' => AiContentSettingController    ::class,
    'middleware' => ['web', 'core'],
], function () {
    Route::group([
        'prefix' => BaseHelper::getAdminPrefix() . '/ai-contents',
        'as' => 'ai-content.',
    ], function () {

        Route::controller(AiContentSettingController::class)->group(function (): void {
            Route::get('settings', [
                'as' => 'settings',
                'uses' => 'edit',
            ]);

            Route::put('settings', [
                'as' => 'settings.update',
                'uses' => 'update',
            ]);
        });
    });
});


