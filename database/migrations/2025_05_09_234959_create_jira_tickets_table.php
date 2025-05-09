<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jira_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('integration_id')->constrained()->onDelete('cascade');

            $table->string('jira_id'); // the actual Jira issue ID
            $table->string('project_key');
            $table->string('issue_type')->nullable();
            $table->string('status')->nullable();

            $table->string('summary')->nullable();
            $table->longText('description')->nullable();
            $table->longText('comments')->nullable(); // flattened comment content

            $table->json('raw')->nullable(); // raw issue data for future use
            $table->timestamp('fetched_at')->nullable();

            $table->timestamps();

            $table->unique(['integration_id', 'jira_id']); // prevent duplicates
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jira_tickets');
    }
};
