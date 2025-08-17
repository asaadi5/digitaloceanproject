<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_units_v2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_v2_id')->index();
            $table->string('unit_label', 100); // مثال: A-3، مكتب 12
            $table->enum('unit_type', ['شقة','مكتب','محل','مخزن','مستودع','هنغار','قبو','روف','بيت ريفي'])->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->tinyInteger('bedrooms')->nullable();
            $table->tinyInteger('bathrooms')->nullable();
            $table->tinyInteger('floor')->nullable();
            $table->decimal('price', 14, 2)->nullable();
            $table->enum('price_currency', ['SYP','USD'])->default('USD');
            $table->enum('rent_period', ['شهري','سنوي'])->nullable();
            $table->boolean('furnished')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('property_units_v2');
    }
};
