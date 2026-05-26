<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. GESTÃO DE PROJETOS
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('budget', 15, 2)->default(0); // Orçamento previsto
            $table->string('status')->default('planeamento'); // planeamento, em_curso, concluido, pausado
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamps();
        });

        // 2. GESTÃO DE INVENTÁRIO (STOCK)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('sku')->unique()->nullable(); // Código de barras/referência
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(5); // Alerta de stock baixo
            $table->decimal('unit_cost', 15, 2)->default(0); // Preço de compra
            $table->decimal('unit_price', 15, 2)->default(0); // Preço de venda
            $table->timestamps();
        });

        // 3. LIGAÇÕES (Relacionar faturas e despesas a projetos)
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('client_id')->constrained()->onDelete('set null');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('category_id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) { $table->dropColumn('project_id'); });
        Schema::table('invoices', function (Blueprint $table) { $table->dropColumn('project_id'); });
        Schema::dropIfExists('products');
        Schema::dropIfExists('projects');
    }
};
