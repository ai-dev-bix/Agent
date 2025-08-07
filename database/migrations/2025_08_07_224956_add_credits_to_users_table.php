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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('credits')->default(0);
            $table->integer('free_messages_used')->default(0);
            $table->integer('free_messages_limit')->default(3);
            $table->timestamp('last_free_message_reset')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->json('preferences')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'credits',
                'free_messages_used',
                'free_messages_limit',
                'last_free_message_reset',
                'is_admin',
                'preferences'
            ]);
        });
    }
};
