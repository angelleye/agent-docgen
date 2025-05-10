<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jira_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jira_ticket_id')->constrained()->onDelete('cascade');

            $table->string('jira_comment_id'); // Jira's ID for the comment
            $table->text('body')->nullable();
            $table->string('author')->nullable();
            $table->timestamp('commented_at')->nullable();

            $table->json('raw')->nullable(); // Raw comment data for context
            $table->timestamps();

            $table->unique(['jira_ticket_id', 'jira_comment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jira_comments');
    }
};
