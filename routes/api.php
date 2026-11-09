<?php

use App\Controllers\BlogpostCategoryController;
use App\Controllers\BlogpostController;
use App\Controllers\FileManagerController;
use App\Controllers\HeaderImageController;
use App\Controllers\PageController;
use App\Controllers\SearchController;
use App\Model\Blogpost;
use App\Model\Page;
use App\Model\Plugin;
use App\Model\Settings;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        'password' => 'required',
    ]);

    if (Auth::attempt(['username' => $request->username, 'password' => $request->password]) ||
       Auth::attempt(['email' => $request->email, 'password' => $request->password])
    ) {

        $user = Auth::user();

        if ($user->isActive()) {

            $user->api_token = Str::random(60);

            $user->save();

            $user->load('role');

            $user->makeVisible('api_token');
            $user->role->makeVisible('rights');

            return response()->json($user, 200);
        }
    }

    return response()->json(['error' => 'Username or password incorrect'], 401);
});

Route::get('/blogposts/slug/{slug}', function ($slug) {
    $blogpost = Blogpost::findBySlug($slug);

    foreach (request()->get('with', []) as $relation) {
        $blogpost->load($relation);
    }

    return response()->json($blogpost);
});

Route::get('/pages/slug/{slug}', function ($slug) {
    $page = Page::findBySlug($slug);

    foreach (request()->get('with', []) as $relation) {
        $page->load($relation);
    }

    return response()->json($page);
});

Route::get('/files', function () {

    $path = trim(request()->query('path', ''), '/');

    if (Str::contains($path, ['..', './', '\\'])) {
        return response()->json([
            'message' => 'Invalid path',
        ], 400);
    }

    if (! Storage::disk('public')->exists($path)) {
        return response()->json([
            'message' => 'Path not found',
        ], 404);
    }

    $directories = Storage::disk('public')->directories($path);
    $files = Storage::disk('public')->files($path);

    return response()->json([
        'path' => $path,
        'directories' => collect($directories)->map(fn ($dir) => [
            'name' => basename($dir),
            'path' => $dir,
            'type' => 'directory',
        ]),
        'files' => collect($files)->map(fn ($file) => [
            'name' => basename($file),
            'path' => $file,
            'type' => 'file',
            'size' => Storage::disk('public')->size($file),
            'url' => Storage::disk('public')->url($file),
        ]),
    ]);
});

Route::apiResource('blogposts', BlogpostController::class)
    ->only(['index', 'show']);

Route::apiResource('categories', BlogpostCategoryController::class)
    ->only(['index', 'show']);

Route::apiResource('pages', PageController::class)
    ->only(['index', 'show']);

Route::apiResource('header-images', HeaderImageController::class)
    ->only(['index', 'show']);

Route::apiResource('file-manager', FileManagerController::class)
    ->middleware('auth:api');

Route::apiResource('search', SearchController::class)
    ->only(['index', 'show']);

Route::get('/settings', function (Request $request) {

    $settings = Cache::get('settings', function () {
        return Settings::group('website')->get();
    });

    return response()->json($settings);

});

Route::get('/settings/{key}', function (string $key) {

    $settings = Cache::get('settings_'.$key, function () use ($key) {
        return Settings::group('website')->key($key)->first();
    });

    return response()->json($settings);

});

Route::get('/users', function (Request $request) {

    if (Gate::allows('view', 'user')) {
        return response()->json(['message' => 'Permission denied!'], 403);
    }

    $users = User::all()->forPage($request->input('page'), $request->input('num'));

    return $users;

})->middleware('auth:api');

Route::get('/plugins', function (Request $request) {

    if (Gate::allows('view', 'plugin')) {
        return response()->json(['message' => 'Permission denied!'], 403);
    }

    $plugins = Plugin::all()->forPage($request->input('page'), $request->input('num'));

    return $plugins;

})->middleware('auth:api');

// ###############################################

Route::put('pages/reorder', [PageController::class, 'reorder'])
    ->middleware('auth:api');

Route::post('lock-up', function (Request $request) {

    $user = User::find($request->input('id'));

    if (isset($user) && $user->isActive() && Hash::check($request->input('password'), $user->password)) {
        return response()->json(true);
    }

    return response()->json(false);

});

/**
 * @deprecated deprecated since version 1.3.0
 */
Route::get('slug/generate/{title}', function ($title) {
    return response()->json(str_slug($title));
})->middleware('auth:api');
