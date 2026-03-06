<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->char('hash', 26)->unique();

            $table->foreignId('task_type_id')->constrained('task_types');
            $table->foreignId('access_id')->nullable()->constrained('accesses');

            $table->enum('role', ['create', 'mirror']);
            $table->enum('status', ['queued', 'running', 'success', 'failed', 'canceled'])->default('queued');
            $table->string('step', 120)->nullable();
            $table->unsignedTinyInteger('progress')->default(0);

            $table->json('payload');
            $table->json('result')->nullable();
            $table->json('error')->nullable();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
