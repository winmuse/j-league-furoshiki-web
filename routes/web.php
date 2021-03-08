<?php

use Illuminate\Http\Request;
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

//Route::get('/', function () {
//    return view('welcome');
//});

//Auth::routes();

Route::get('/test_credit', 'Api\ArticleController@writeCredit');

// SNS
Route::group([
//  'middleware' => ['jwt.verify'],
  'namespace' => 'Api\SNS',
  'prefix' => 'sns'
], function() {
  /* Facebook */
  Route::get('/login/facebook', ['uses' => 'FacebookController@login', 'as' => 'facebook.login']);
  Route::get('/login/facebook/callback', ['uses' => 'FacebookController@callback', 'as' => 'facebook.callback']);
  Route::get('/facebook/user', ['uses' => 'FacebookController@retrieveUserProfile', 'as' => 'facebook.user']);

  /* Twitter */
  Route::get('/login/twitter', ['uses' => 'TwitterController@login', 'as' => 'twitter.login']);
  Route::get('/login/twitter/callback', ['uses' => 'TwitterController@callback', 'as' => 'twitter.callback']);
  Route::get('/twitter/user', ['uses' => 'TwitterController@retrieveUserProfile', 'as' => 'twitter.user']);

  /* Instagram */
  Route::get('/login/instagram', ['uses' => 'InstagramController@login', 'as' => 'instagram.login']);
  Route::get('/login/instagram/callback', ['uses' => 'InstagramController@callback', 'as' => 'instagram.callback']);
});

// Add this route last as a catch all for undefined routes.
 Route::get(
     '{path?}',
     function(Request $request) {
         // If the request expects JSON, it means that
         // someone sent a request to an invalid route.
         if ($request->expectsJson()) {
             abort(404);
         }

         // Fetch and display the page from the render path on nuxt dev server or fallback to static file
         return file_get_contents(getenv('NUXT_OUTPUT_PATH') ?: public_path('spa.html'));
     }
 )->where('path', '^(?!admin).*')
     // Redirect to Nuxt from within Laravel
     // by using Laravels route helper
     // e.g.: `route('nuxt', ['path' => '/<nuxtPath>'])`
     ->name('nuxt');
