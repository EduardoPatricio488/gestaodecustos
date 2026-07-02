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
    Schema::create('subscriptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('category_id')->constrained();
        $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');

        $table->string('name');
        $table->decimal('amount', 10, 2);
        $table->integer('billing_day');
        $table->string('cycle')->default('monthly');
        $table->string('status')->default('active');
        $table->string('payment_method')->nullable(); // A COLUNA QUE FALTAVA

        $table->boolean('is_active')->default(true);
        $table->date('started_at')->nullable();
        $table->date('renewal_date')->nullable();
        $table->text('notes')->nullable();
        $table->boolean('notify_before_billing')->default(false);
        $table->integer('notify_days_before')->nullable();

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
