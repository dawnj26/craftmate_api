<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    public function getLatestRelease()
    {
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
        // dd($release->assets[1]->browser_download_url);
        return redirect($release->assets[1]->browser_download_url);
    }
}
