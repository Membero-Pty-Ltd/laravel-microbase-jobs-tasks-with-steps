<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('code', 30)->unique();
            $table->string('description')->nullable();
            $table->unsignedTinyInteger('default_retries')->default(3);
            $table->string('default_queue')->default('default');
            $table->json('steps');
            $table->json('payload');
            $table->boolean('is_enabled')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_types');
    }
};
