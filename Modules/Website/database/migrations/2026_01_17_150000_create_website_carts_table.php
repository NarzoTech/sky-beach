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
        Schema::create('website_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable()->index();
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('menu_variants')->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->json('addons')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'session_id']);
        });
    }

    /**
     * Rollback the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_carts');
    }
};
