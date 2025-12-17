<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

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
Route::post('/auth', function (Request $request) {

    $request->validate([
        'password' => 'required'
    ]);
   
    if(Auth::attempt(['username' => $request->username, 'password' => $request->password]) || 
       Auth::attempt(['email' => $request->email, 'password' => $request->password])
    ){ 

        $user = Auth::user(); 

        if($user->isActive()){
            
            $user->api_token = Str::random(60);

            $user->save();

            $user->load('role');
            
            $user->makeVisible('api_token');
            $user->role->makeVisible('rights');

            return response()->json($user, 200);
        }
    } 

    return response()->json(['error'=>'Username or password incorrect'], 401);
});

Route::get('/blogposts/slug/{slug}', function($slug){
  $blogpost = \App\Model\Blogpost::findBySlug($slug);

    foreach(request()->get('with', []) as $relation) {
        $blogpost->load($relation);
    }

  return response()->json($blogpost);
});

Route::get('/pages/slug/{slug}', function($slug){
    $page = \App\Model\Page::findBySlug($slug);
  
      foreach(request()->get('with', []) as $relation) {
          $page->load($relation);
      }
  
    return response()->json($page);
  });

Route::apiResource('blogposts', \App\Controllers\BlogpostController::class)
            ->only(['index', 'show']);

Route::apiResource('categories', \App\Controllers\BlogpostCategoryController::class)
            ->only(['index', 'show']);

Route::apiResource('pages', \App\Controllers\PageController::class)
            ->only(['index', 'show']);

Route::apiResource('header-images', \App\Controllers\HeaderImageController::class)
            ->only(['index', 'show']);

Route::apiResource('file-manager', \App\Controllers\FileManagerController::class)
            ->middleware('auth:api');

Route::apiResource('search', \App\Controllers\SearchController::class)
            ->only(['index', 'show']);


Route::get('/settings', function(Request $request){

    $settings = Cache::get('settings', function () {
        return \App\Model\Settings::group('website')->get();
    });

    return response()->json($settings);

});

Route::get('/users',function(Request $request){

    if(Gate::allows('view', 'user')){
        return response()->json(['message' => 'Permission denied!'], 403);
    }

    $users = \App\Model\User::all()->forPage($request->input('page'),$request->input('num'));

    return $users;

})->middleware('auth:api');

Route::get('/plugins',function(Request $request){

    if(Gate::allows('view', 'plugin')){
        return response()->json(['message' => 'Permission denied!'], 403);
    }

    $plugins = \App\Model\Plugin::all()->forPage($request->input('page'),$request->input('num'));

    return $plugins;

})->middleware('auth:api');

################################################

Route::put('pages/reorder', [\App\Controllers\PageController::class, 'reorder'])
                ->middleware('auth:api');

Route::post('lock-up',function(Request $request){

    $user = \App\Model\User::find($request->input('id'));
    
    if (isset($user) && $user->isActive() && \Hash::check($request->input('password'), $user->password)) {
        return response()->json(TRUE);
    }

    return response()->json(FALSE);

});


/**
 * @deprecated deprecated since version 1.3.0
 */
Route::get('slug/generate/{title}',function($title){
	 return response()->json(str_slug($title));
})->middleware('auth:api');
