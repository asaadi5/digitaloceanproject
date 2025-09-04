<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('property_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->enum('purpose', ['sale','rent']);     // بيع أو إيجار — يحدد نوع السعر
            $table->integer('price');                     // السعر الجديد
            $table->string('currency', 10)->default('USD'); // غالباً ليرة سورية
            $table->timestamp('effective_from')->useCurrent(); // تاريخ بدء سريان السعر
            $table->timestamp('effective_to')->nullable();     // تاريخ انتهاء (إن انتهى)
            $table->string('reason')->nullable();         // سبب التغيير: "تعديل سوق"، "تفاوض"، ...
            $table->unsignedBigInteger('changed_by')->nullable(); // admin_id أو agent_id
            $table->timestamps();

            $table->index(['property_id','purpose','effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_price_history');
    }
};
