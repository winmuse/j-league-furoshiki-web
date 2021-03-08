<?php

use Illuminate\Support\Facades\Route;

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

/* Admin */
Route::group(['middleware' => ['guest','basic.auth']], function() {
    Auth::routes(['register' => false]);
    Route::get('/', function() {
        return view('admin.auth.login');
    })->name('login');
});

Route::group(['middleware' => ['admin.auth:admin']], function() {

    Route::get('/password_reset', 'Profile\ProfileController@passwordReset')->name('password.reset');
    Route::post('/password_reset', 'Profile\ProfileController@passwordReset')->name('password.reset.save');
    Route::get('/dashboard', 'HomeController@index')->name('home');

    // 画像・動画の利用履歴
    Route::get('/media/usages', 'Media\MediaController@index')->name('media.usage.index');
    Route::get('/media/usages/detail/{id}', 'Media\MediaController@detail')->name('media.usage.detail');
    Route::post('/media/usages/export', 'Media\MediaController@export')->name('media.usage.export');
});

Route::group(['middleware' => [
    'admin.auth:admin',
    'auth.basic',
    'checkForManager.managers:j-league'
]], function() {

    // クラブチーム管理
    Route::get('/managers', 'Manager\ManagerController@index')->name('managers.index');
    Route::get('/managers/create', 'Manager\ManagerController@create')->name('managers.create');
    Route::get('/managers/edit/{id}', 'Manager\ManagerController@edit')->name('managers.edit');
    Route::put('/managers/update', 'Manager\ManagerController@update')->name('managers.update');
    Route::put('/managers/update/alternative', 'Manager\ManagerController@updateWithParent')->name('managers.update.alternative');
    Route::post('/managers/save', 'Manager\ManagerController@store')->name('managers.save');
    Route::post('/managers/save/alternative', 'Manager\ManagerController@storeWithParent')->name('managers.save.alternative');
    Route::delete('/managers/delete', 'Manager\ManagerController@delete')->name('managers.delete');

    // Balzアカウント管理
    Route::get('/balzs', 'Balz\BalzController@index')->name('balzs.index');
    Route::get('/balzs/create', 'Balz\BalzController@create')->name('balzs.create');
    Route::get('/balzs/edit/{id}', 'Balz\BalzController@edit')->name('balzs.edit');
    Route::put('/balzs/update', 'Balz\BalzController@update')->name('balzs.update');
    Route::post('/balzs/save', 'Balz\BalzController@store')->name('balzs.save');
    Route::delete('/balzs/delete', 'Balz\BalzController@delete')->name('balzs.delete');
});

Route::group(['middleware' => [
    'admin.auth:admin',
    'auth.basic',
    'checkForManager.managers:j-league;balz;clubs'
]], function() {
    // Jフォト素材管理
    Route::get('/medias', 'AWS\AWSController@index')->name('medias.index');
    Route::get('/medias/edit/{id}', 'AWS\AWSController@edit')->name('medias.edit');
    Route::put('/medias/update', 'AWS\AWSController@update')->name('medias.update');
    Route::delete('/medias/delete', 'AWS\AWSController@delete')->name('medias.delete');
    Route::post('/medias/export', 'AWS\AWSController@export')->name('medias.export');
    Route::post('/medias/import', 'AWS\AWSController@import')->name('medias.import');
    Route::post('/medias/toggle', 'AWS\AWSController@toggle')->name('medias.toggle');

    // LINE 連携
    Route::get('/lines', 'Line\LineController@index')->name('lines.index');
    Route::get('/lines/edit/{id}', 'Line\LineController@edit')->name('lines.edit');
    Route::put('/lines/update', 'Line\LineController@update')->name('lines.update');

});

Route::group(['middleware' => [
    'admin.auth:admin',
    'auth.basic',
    'checkForManager.managers:j-league;club'
]], function() {

    // 投稿者アカウント管理
    Route::get('/accounts', 'Account\AccountController@index')->name('accounts.index');
    Route::get('/accounts/create', 'Account\AccountController@create')->name('accounts.create');
    Route::get('/accounts/edit/{id}', 'Account\AccountController@edit')->name('accounts.edit');
    Route::put('/accounts/update', 'Account\AccountController@update')->name('accounts.update');
    Route::post('/accounts/save', 'Account\AccountController@store')->name('accounts.save');
    Route::delete('/accounts/delete', 'Account\AccountController@delete')->name('accounts.delete');
    Route::get('/accounts/export/template', 'Account\AccountController@exportTemplate')->name('accounts.export.template');
    Route::get('/accounts/export/all', 'Account\AccountController@exportAllAccounts')->name('accounts.export.all');
    Route::post('/accounts/import', 'Account\AccountController@import')->name('accounts.import');

    // 定期コメント管理
    Route::get('/comments', 'Comment\CommentController@index')->name('comments.index');
    Route::get('/comments/create', 'Comment\CommentController@create')->name('comments.create');
    Route::get('/comments/edit/{id}', 'Comment\CommentController@edit')->name('comments.edit');
    Route::put('/comments/update', 'Comment\CommentController@update')->name('comments.update');
    Route::post('/comments/save', 'Comment\CommentController@store')->name('comments.save');
    Route::delete('/comments/delete', 'Comment\CommentController@delete')->name('comments.delete');

    // 定期ハッシュタグ管理
    Route::get('/tags', 'Tag\TagController@index')->name('tags.index');
    Route::get('/tags/create', 'Tag\TagController@create')->name('tags.create');
    Route::get('/tags/edit/{id}', 'Tag\TagController@edit')->name('tags.edit');
    Route::put('/tags/update', 'Tag\TagController@update')->name('tags.update');
    Route::post('/tags/save', 'Tag\TagController@store')->name('tags.save');
    Route::delete('/tags/delete', 'Tag\TagController@delete')->name('tags.delete');

    // 不定期ハッシュタグ管理
    Route::get('/expire/tags', 'Tag\ExpireTagController@index')->name('expire.tags.index');
    Route::get('/expire/tags/create', 'Tag\ExpireTagController@create')->name('expire.tags.create');
    Route::get('/expire/tags/edit/{id}', 'Tag\ExpireTagController@edit')->name('expire.tags.edit');
    Route::put('/expire/tags/update', 'Tag\ExpireTagController@update')->name('expire.tags.update');
    Route::post('/expire/tags/save', 'Tag\ExpireTagController@store')->name('expire.tags.save');
    Route::delete('/expire/tags/delete', 'Tag\ExpireTagController@delete')->name('expire.tags.delete');
    // Dropbox
    Route::get('/dropbox', 'Dropbox\DropboxController@index')->name('dropbox.index');
    Route::put('/dropbox/update', 'Dropbox\DropboxController@update')->name('dropbox.update');
    Route::put('/dropbox/test', 'Dropbox\DropboxController@testDropboxAccount')->name('dropbox.test');
});
