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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->tinyInteger('type');
            $table->string('job_batches_id',)->nullable();
            $table->foreign('job_batches_id')
                ->references('id')
                ->on('job_batches')
                ->constrained()
                ->onDelete('cascade');
            $table->string('job_name', 50);
            $table->string('file', 50)->nullable();
            $table->smallInteger('total_chunk')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
