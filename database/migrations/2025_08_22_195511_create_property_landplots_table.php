<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_landplots', function (Blueprint $t) {
            $t->id();
            $t->foreignId('property_id')->constrained()->cascadeOnDelete();
            $t->integer('plot_area_m2')->nullable();
            $t->decimal('frontage_m',8,2)->nullable();
            $t->decimal('depth_m',8,2)->nullable();
            $t->decimal('slope_percent',5,2)->nullable();
            $t->string('soil_type')->nullable();
            $t->string('irrigation_source')->nullable();
            $t->string('water_rights')->nullable();
            $t->string('agricultural_class')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('property_landplots');
    }
};
