<?php

namespace App\Services;

use App\Models\Integration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JiraService
{
    public function getAccessToken()
    {
        $integration = Integration::where('user_id', Auth::id())
            ->where('provider', 'jira')
            ->first();

        if (!$integration) {
            return null;
        }

        // If token is still valid, return it
        if ($integration->expires_at && $integration->expires_at->isFuture()) {
            return $integration->access_token;
        }

        // Otherwise, refresh it
        $response = Http::asForm()->post('https://auth.atlassian.com/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => config('services.jira.client_id'),
            'client_secret' => config('services.jira.client_secret'),
            'refresh_token' => $integration->refresh_token,
        ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        $integration->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $integration->refresh_token,
            'token_type' => $data['token_type'] ?? 'Bearer',
            'scope' => $data['scope'] ?? $integration->scope,
            'expires_at' => Carbon::now()->addSeconds($data['expires_in'] ?? 3600),
        ]);

        return $integration->access_token;
    }
}
