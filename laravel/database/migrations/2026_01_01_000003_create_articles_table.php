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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->text('title')->nullable();
            $table->text('url')->nullable();
            $table->string('source', 100)->nullable();
            $table->text('thumbnail')->nullable();

            $table->dateTime('published_at')->nullable();

            $table->string('sentiment', 20)->nullable();
            $table->float('sentiment_score')->nullable();

            $table->string('coin_symbol', 10)->nullable();

            $table->timestamp('inserted_at')->useCurrent();

            $table->boolean('is_pinned')->default(false);
            $table->boolean('hidden_from_trending')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};