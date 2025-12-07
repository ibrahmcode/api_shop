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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('translatable_type'); // Model name (Category, Item, etc.)
            $table->unsignedBigInteger('translatable_id'); // Model ID
            $table->string('locale', 5); // ku, ar, en
            $table->string('field'); // name, description, etc.
            $table->text('value');
            $table->timestamps();

            $table->index(['translatable_type', 'translatable_id']);
            $table->index('locale');
            $table->unique(['translatable_type', 'translatable_id', 'locale', 'field'], 'trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
