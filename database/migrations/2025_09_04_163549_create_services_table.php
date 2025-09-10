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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // published, draft
            $table->string('visibility')->default('public'); // public, private
            $table->timestamp('publish_at')->nullable();
            $table->string('category')->nullable();
            $table->string('tags')->nullable();
            $table->string('short_description', 255)->nullable();
            $table->string('manufacturer')->nullable();
            $table->integer('stocks')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->integer('orders')->default(0);
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
