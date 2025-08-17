<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_meta_v2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_v2_id')->index();
            $table->string('meta_key', 100);
            $table->text('meta_value')->nullable();
            $table->json('meta_json')->nullable(); // في MariaDB تُخزَّن كلونغ تكست
            $table->timestamps();

            $table->index(['property_v2_id','meta_key'], 'idx_prop_meta_v2');
        });
    }
    public function down(): void {
        Schema::dropIfExists('property_meta_v2');
    }
};
