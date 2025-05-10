<?php

namespace App\Jobs;

use App\Models\Integration;
use App\Models\JiraTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class JiraFetchTicketsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $integration;

    public function __construct(Integration $integration)
    {
        $this->integration = $integration;
    }

    public function handle()
    {
        $accessToken = $this->integration->access_token;
        $cloudId = $this->integration->external_id;
        $projectKeys = $this->integration->metadata['selected_projects'] ?? [];

        foreach ($projectKeys as $projectKey) {
            $startAt = 0;
            $maxResults = 100;
            $total = null;

            do {
                $response = Http::withToken($accessToken)->get("https://api.atlassian.com/ex/jira/{$cloudId}/rest/api/3/search", [
                    'jql' => "project = {$projectKey}",
                    'startAt' => $startAt,
                    'maxResults' => $maxResults,
                    'fields' => 'summary,status,created,updated,description'
                ]);

                if ($response->failed()) {
                    \Log::error("Failed to fetch issues from Jira for project {$projectKey}: " . $response->body());
                    break;
                }

                $data = $response->json();
                $issues = $data['issues'] ?? [];
                $total = $data['total'] ?? null;

                foreach ($issues as $issue) {
                    JiraTicket::updateOrCreate(
                        ['ticket_key' => $issue['key']],
                        [
                            'project_key' => $projectKey,
                            'summary' => $issue['fields']['summary'] ?? '',
                            'status' => $issue['fields']['status']['name'] ?? 'Unknown',
                            'description' => $issue['fields']['description']['content'][0]['content'][0]['text'] ?? null,
                            'created_at' => $issue['fields']['created'] ?? now(),
                            'updated_at' => $issue['fields']['updated'] ?? now(),
                        ]
                    );

                    // 🔁 Up next: fetch and store comments per issue
                }

                $startAt += $maxResults;
            } while ($total !== null && $startAt < $total);
        }
    }
}
