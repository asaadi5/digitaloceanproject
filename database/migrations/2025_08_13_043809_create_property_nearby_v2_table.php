<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_nearby_v2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_v2_id')->index();
            $table->enum('kind', ['مدرسة','جامعة','روضه','مشفى','صيدلية','حديقة','سوق','سوبرماركت','مسجد','كنيسة','موقف','بحر','أوتوستراد','مركز حكومي'])->nullable();
            $table->string('name', 150)->nullable();
            $table->integer('distance_m')->nullable();       // المسافة بالمتر
            $table->smallInteger('walking_minutes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('property_nearby_v2');
    }
};
