<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_videos_v2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_v2_id')->index();
            $table->string('provider')->nullable(); // youtube, vimeo...
            $table->string('url');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('property_videos_v2');
    }
};
