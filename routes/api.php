<?php

use App\Http\Controllers\MyRefsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
|
| Текущий мастер приходит в заголовке X-Master-Id и уже разложен
| в атрибуты запроса middleware'ом ResolveCurrentMaster:
|
|     $master = $request->attributes->get('current_master');
|
| Здесь нужно написать три роута — см. README.md.
|
*/

Route::get('/ping', fn() => ['ok' => true]);





Route::prefix('referrals')->group(
    function () {
        Route::get('/my', [MyRefsController::class, 'myrefs']);
        Route::post('/attach', [MyRefsController::class, 'create']);
        Route::get('/earnings', [MyRefsController::class, 'earnings']);

    }
);
// TODO: POST /api/referrals/attach
// TODO: GET  /api/referrals/my
// TODO: GET  /api/referrals/earnings
