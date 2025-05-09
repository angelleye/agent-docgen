<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Integration;
use Illuminate\Support\Facades\Auth;

class IntegrationController extends Controller
{
    public function index()
    {
        $jira = Integration::where('user_id', Auth::id())
            ->where('provider', 'jira')
            ->first();

        $jiraConnected = $jira !== null;

        return view('integrations.index', compact('jiraConnected', 'jira'));
    }
}
