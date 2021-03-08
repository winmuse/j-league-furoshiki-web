<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* authentication */
Route::group([
//  'middleware' => 'api',
  'namespace' => 'Api',
], function() {
  Route::post('login', 'Auth\AuthController@login');
  Route::post('verify', 'Auth\AuthController@smsVerify');
  Route::get('refresh', 'Auth\AuthController@refresh');
  Route::post('register', 'Auth\AuthController@register');
});

Route::group([
  'middleware' => ['jwt.verify'],
  'namespace' => 'Api',
], function() {
    // auth
  Route::get('logout', 'Auth\AuthController@logout');
  Route::get('user', 'Auth\AuthController@getAuthenticatedUser');

  // sns credentials
  Route::get('credential/all', 'CredentialController@all');
  
  Route::post('save-fb-account', 'SNS\FacebookController@save');
  Route::post('save-tw-account', 'SNS\TwitterController@save');

  Route::get('credential/remove/{sns}/{id}', 'CredentialController@remove');
  Route::get('line_credential', 'SNS\LineController@get');
  Route::get('ig_credential', 'SNS\InstagramController@get');
  Route::post('save-ig-account', 'SNS\InstagramController@save');
  Route::post('save-line-url', 'SNS\LineController@save');

  // profile
  Route::post('change_password', 'ProfileController@updatePassword');
  Route::post('change_mobile', 'ProfileController@updatePhoneNumber');

  // media
  Route::get('media_list', 'MediaController@getMediaList');
  Route::get('search_media', 'MediaController@searchMediaList');

  // club & player
  Route::get('club_list', 'MediaController@getClubList');
  Route::get('player_list', 'MediaController@getPlayerList');

  // post sns
  Route::get('default_tags', 'ArticleController@getDefaultTags');
  Route::get('default_comment', 'ArticleController@getDefaultComment');
  Route::post('new_post', 'ArticleController@create');
  Route::post('update_post', 'ArticleController@update');
  Route::get('post_list', 'ArticleController@list');
  Route::get('post/{id}', 'ArticleController@get');
  Route::get('check_video_posted/{mid}', 'ArticleController@checkVideoPosted');
});
