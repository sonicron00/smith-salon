<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('google');
            $table->string('external_id')->nullable(); // Google review name/id for dedup
            $table->string('author');
            $table->string('author_photo')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('text');
            $table->string('relative_time')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique(['source', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
