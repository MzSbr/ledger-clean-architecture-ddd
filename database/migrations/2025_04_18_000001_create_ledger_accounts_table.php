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
        Schema::create('ledger_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('type', 20);
            $table->string('status', 20);
            $table->uuid('parent_id')->nullable();
            $table->boolean('is_category')->default(false);
            $table->string('tax_code', 50)->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
            
            $table->foreign('parent_id')
                ->references('id')
                ->on('ledger_accounts')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_accounts');
    }
};
