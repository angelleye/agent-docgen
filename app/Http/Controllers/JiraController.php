<?php

namespace App\Http\Controllers;

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
        return response('✅ Callback received. Code: ' . $request->query('code'));
    }
}
