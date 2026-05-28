<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Ex: 'site_name'
            $table->text('value')->nullable(); // Ex: 'Gestão de Custos Premium'
            $table->timestamps();
        });

        // Criar definições iniciais padrão
        DB::table('site_settings')->insert([
            ['key' => 'site_name', 'value' => 'Finance Pro IA'],
            ['key' => 'maintenance_mode', 'value' => '0'],
            ['key' => 'allow_registration', 'value' => '1'],
            ['key' => 'default_currency', 'value' => 'EUR'],
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('site_settings');
    }
};
