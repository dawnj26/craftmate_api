<?php

use App\Http\Controllers\AppController;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $githubToken = config('services.github.token');

    $repo = 'dawnj26/craftmate_client';

    $client = new \GuzzleHttp\Client();
    $response = $client->get("https://api.github.com/repos/{$repo}/releases/latest", [
        'headers' => [
            'Authorization' => "Bearer {$githubToken}",
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28'
        ]
    ]);

    $release = json_decode($response->getBody());
    $downloadUrl = $release->assets[1]->browser_download_url;
    return view('welcome', compact('downloadUrl'));
});

Route::get('/project/{project}', function (Project $project) {
    return response('', 302)->header('Location', "craftmate://open.my.app/project/{$project->id}");
});

Route::get('/user/{user}', function (User $user) {
    return response('', 302)->header('Location', "craftmate://open.my.app/user/{$user->id}");
});

Route::get('/shop/{id}', function (string $id) {
    return response('', 302)->header('Location', "craftmate://open.my.app/shop/{$id}");
});

Route::get('/download/latest', [AppController::class, 'getLatestRelease'])
    ->name('download.latest');
