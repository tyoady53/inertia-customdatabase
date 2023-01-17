<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Apps\Master\FormController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//route home
Route::get('/', function () {
    return \Inertia\Inertia::render('Auth/Login');
})->middleware('guest');

//prefix "apps"
Route::prefix('apps')->group(function() {

    //middleware "auth"
    Route::group(['middleware' => ['auth']], function () {

        //route dashboard
        Route::get('dashboard', \App\Http\Controllers\Apps\DashboardController::class)->name('apps.dashboard');

        Route::resource('/roles', \App\Http\Controllers\Apps\RoleController::class, ['as' => 'apps'])
            ->middleware('permission:roles.index|roles.create|roles.show|roles.edit|roles.delete');

        Route::resource('/users', \App\Http\Controllers\Apps\UserController::class, ['as' => 'apps']);
            // ->middleware('permission:users.index|users.create|users.edit|roles.delete');

        Route::prefix('forms')->group( function() {

            Route::get('index', [FormController::class, 'index'])->name('forms.index');

            Route::get('create', [FormController::class, 'create'])->name('forms.create');

            Route::get('{form:slug}/edit', [FormController::class, 'edit'])->name('forms.edit');

            Route::get('{form:slug}/show', [FormController::class, 'show']);

            Route::get('{form:slug}/get_relation', [FormController::class, 'get_relation'])->name('forms.get_relation');

            Route::get('get_relateto', [FormController::class, 'get_relateto'])->name('forms.get_relateto');

            Route::get('get_parent', [FormController::class, 'get_parent'])->name('forms.get_parent');

            Route::get('{form:slug}/get_tables', [FormController::class, 'get_tables']);

            Route::get('{form:slug}/manage-relation', [FormController::class, 'manage_relation'])->name('forms.manage-relation');

            Route::post('create', [FormController::class, 'store']);

            Route::post('add_record', [FormController::class, 'add_record'])->name('forms.add_record');

            Route::post('update', [FormController::class, 'update'])->name('forms.update');

            Route::post('manage', [FormController::class, 'manage'])->name('forms.manage');

            Route::post('delete', [FormController::class, 'delete'])->name('forms.delete');

            Route::post('remove_relation', [FormController::class, 'remove_relation'])->name('forms.remove_relation');

            Route::post('set_relation', [FormController::class, 'set_relation'])->name('forms.set_relation');

            Route::post('set_parent', [FormController::class, 'set_parent'])->name('forms.set_parent');

            Route::post('{form:slug}/addcolumn', [FormController::class, 'addcolumn']);

            Route::post('{form:slug}/update', [FormController::class, 'update']);

        });
    });

});