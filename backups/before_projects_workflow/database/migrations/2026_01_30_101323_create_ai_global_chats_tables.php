<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Tenant support: Chats belong to the user's tenant context
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade'); 
            $table->string('title')->nullable(); // Auto-generated or user defined
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_chat_id')->constrained('ai_chats')->cascadeOnDelete();
            $table->string('role'); // user, assistant, system
            $table->longText('content');
            // For future file attachments if needed
            $table->json('attachments')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chats');
    }
};
