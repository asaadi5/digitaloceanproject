<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('property_rental_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();

            // نوع القاعدة وقيمتها
            // أمثلة rule_key: deposit_amount, payment_cycle, early_termination_fee,
            // utilities_included, contract_registration, handover_time, damages_policy, stamp_fee
            $table->string('rule_key');             // اسم القاعدة
            $table->string('rule_value')->nullable(); // قيمتها (نص/رقم)
            $table->boolean('is_enforced')->default(true); // مُلزم؟
            $table->text('notes')->nullable();      // شرح إضافي

            $table->timestamps();

            $table->unique(['property_id','rule_key']); // قاعدة واحدة من كل نوع لكل عقار
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_rental_rules');
    }
};
