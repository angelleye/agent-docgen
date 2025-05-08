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
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('provider'); // e.g. 'jira', 'google', 'paypal'
            $table->string('access_token');
            $table->string('refresh_token')->nullable();
            $table->string('token_type')->nullable(); // e.g. Bearer
            $table->string('scope')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->string('external_id')->nullable(); // Atlassian ID, PayPal merchant ID, etc.
            $table->json('metadata')->nullable(); // Optional provider-specific config/settings

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
