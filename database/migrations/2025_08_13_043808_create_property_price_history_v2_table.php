<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_price_history_v2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_v2_id')->index();
            $table->decimal('price', 14, 2);
            $table->enum('price_currency', ['SYP','USD'])->default('USD');
            $table->enum('price_type', ['ثابت','قابل للتفاوض'])->default('قابل للتفاوض');
            $table->string('note', 255)->nullable();
            $table->timestamp('effective_from')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('property_price_history_v2');
    }
};
