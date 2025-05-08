<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Integration;
use Illuminate\Http\Request;

class JiraController extends Controller
{
    public function redirectToJira(Request $request)
    {
        $clientId = config('services.jira.client_id');
        $redirectUri = urlencode(config('services.jira.redirect_uri'));

        $scopes = implode(' ', [
            'read:jira-user',
            'read:jira-work',
            'read:me',
            'read:servicedesk-request',
        ]);

        $authUrl = "https://auth.atlassian.com/authorize?" . http_build_query([
                'audience' => 'api.atlassian.com',
                'client_id' => $clientId,
                'scope' => $scopes,
                'redirect_uri' => config('services.jira.redirect_uri'),
                'response_type' => 'code',
                'prompt' => 'consent',
            ]);

        return redirect($authUrl);
    }

    public function handleJiraCallback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return redirect()->route('integrations.index')->with('error', 'Authorization code missing.');
        }

        // Exchange code for access token
        $response = Http::asForm()->post('https://auth.atlassian.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.jira.client_id'),
            'client_secret' => config('services.jira.client_secret'),
            'code' => $code,
            'redirect_uri' => config('services.jira.redirect_uri'),
        ]);

        if ($response->failed()) {
            return redirect()->route('integrations.index')->with('error', 'Failed to connect to Jira.');
        }

        $data = $response->json();

        // Save or update integration
        Integration::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'provider' => 'jira',
            ],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'] ?? null,
                'token_type' => $data['token_type'] ?? 'Bearer',
                'scope' => $data['scope'] ?? null,
                'expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
                'metadata' => [],
            ]
        );

        return redirect()->route('integrations.index')->with('success', '✅ Connected to Jira successfully.');
    }
}
