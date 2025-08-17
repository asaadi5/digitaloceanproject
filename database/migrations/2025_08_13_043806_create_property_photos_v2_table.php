<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_photos_v2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_v2_id')->index();
            $table->string('file');               // path/filename
            $table->string('alt')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('property_photos_v2');
    }
};
