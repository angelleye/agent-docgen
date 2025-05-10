<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JiraTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'integration_id',
        'jira_id',
        'project_key',
        'issue_type',
        'status',
        'summary',
        'description',
        'raw',
        'fetched_at',
    ];

    protected $casts = [
        'raw' => 'array',
        'fetched_at' => 'datetime',
    ];

    public function comments()
    {
        return $this->hasMany(JiraComment::class);
    }

    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
