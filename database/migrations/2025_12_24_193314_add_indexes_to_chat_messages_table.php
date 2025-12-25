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
        Schema::table('chat_messages', function (Blueprint $table) {
            // This makes searching for a conversation between two people instant
            $table->index(['sender_id', 'receiver_id', 'created_at'], 'chat_lookup_index');
            
            // This makes counting unread messages for the sidebar instant
            $table->index(['receiver_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('chat_lookup_index');
            $table->dropIndex(['receiver_id', 'is_read']);
        });
    }
};