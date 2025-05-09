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
            'offline_access',
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

        $tokenResponse = Http::asForm()->post('https://auth.atlassian.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.jira.client_id'),
            'client_secret' => config('services.jira.client_secret'),
            'code' => $code,
            'redirect_uri' => config('services.jira.redirect_uri'),
        ]);

        if ($tokenResponse->failed()) {
            return redirect()->route('integrations.index')->with('error', 'Failed to connect to Jira.');
        }

        $tokenData = $tokenResponse->json();

        // Fetch Jira cloud site info using access token
        $resourceResponse = Http::withToken($tokenData['access_token'])
            ->get('https://api.atlassian.com/oauth/token/accessible-resources');

        if ($resourceResponse->failed()) {
            return redirect()->route('integrations.index')->with('error', 'Failed to fetch Jira site information.');
        }

        $cloudSite = $resourceResponse->json()[0] ?? null;

        if (!$cloudSite) {
            return redirect()->route('integrations.index')->with('error', 'No accessible Jira site found.');
        }

        Integration::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'provider' => 'jira',
            ],
            [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'token_type' => $tokenData['token_type'] ?? 'Bearer',
                'scope' => $tokenData['scope'] ?? null,
                'expires_at' => now()->addSeconds($tokenData['expires_in'] ?? 3600),
                'external_id' => $cloudSite['id'],
                'metadata' => [
                    'name' => $cloudSite['name'],
                    'url' => $cloudSite['url'],
                    'avatarUrl' => $cloudSite['avatarUrl'],
                    'scopes' => $cloudSite['scopes'],
                ],
            ]
        );

        return redirect()->route('integrations.index')->with('success', '✅ Connected to Jira successfully.');
    }

    public function disconnect(Request $request)
    {
        Integration::where('user_id', Auth::id())
            ->where('provider', 'jira')
            ->delete();

        return redirect()->route('integrations.index')->with('success', '🔌 Jira disconnected successfully.');
    }
}
