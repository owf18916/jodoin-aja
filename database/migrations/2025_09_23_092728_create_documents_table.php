<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('documentable_type')->nullable()->index();
            $table->unsignedBigInteger('documentable_id')->nullable()->index();
            $table->string('invoice_number')->nullable()->index();
            $table->string('file_type')->nullable()->index();
            $table->string('file_name')->nullable();

            // keep as TEXT to allow long URLs, NAS paths, etc.
            $table->text('path')->nullable();

            // new: deterministic hash of path for indexing & uniqueness
            $table->string('path_hash', 64)->nullable()->index();

            $table->string('source')->nullable()->index(); // nas | ils_api
            $table->string('access_type')->nullable()->index(); // 'nas' | 'url'
            $table->string('storage_disk')->nullable();
            $table->integer('invoice_items_count')->nullable();
            $table->json('raw_api')->nullable();
            $table->timestamp('retrieved_at')->nullable();
            $table->timestamps();

            // unique by path_hash (safer). allow nulls, but path_hash should be set when inserting.
            $table->unique(['path_hash'], 'uniq_documents_path_hash');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
