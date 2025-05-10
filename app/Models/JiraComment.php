<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JiraComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'jira_ticket_id',
        'jira_comment_id',
        'body',
        'author',
        'commented_at',
        'raw',
    ];

    protected $casts = [
        'raw' => 'array',
        'commented_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(JiraTicket::class, 'jira_ticket_id');
    }
}
