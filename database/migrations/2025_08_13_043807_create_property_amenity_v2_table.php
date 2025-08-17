<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_amenity_v2', function (Blueprint $table) {
            $table->unsignedBigInteger('property_v2_id');
            $table->unsignedBigInteger('amenity_id');
            $table->primary(['property_v2_id','amenity_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('property_amenity_v2');
    }
};
