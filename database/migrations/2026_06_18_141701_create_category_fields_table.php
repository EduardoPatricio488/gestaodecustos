<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('label');           // nome visível do campo, ex: "Quilometragem"
            $table->string('key');              // identificador interno, ex: "km"
            $table->enum('type', ['text', 'number', 'date', 'select', 'checkbox']);
            $table->json('options')->nullable(); // só usado se type = select
            $table->boolean('required')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_fields');
    }
};
