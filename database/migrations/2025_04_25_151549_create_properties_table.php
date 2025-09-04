<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            // علاقات
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('type_id');

            // المرافق (سيُستبدل لاحقاً بجدول Pivot)
            $table->string('amenities')->nullable();

            // بيانات أساسية
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('price');
            $table->string('featured_photo');
            $table->string('purpose'); // بيع / إيجار

            // تفاصيل العقار
            $table->integer('bedroom')->nullable();
            $table->integer('bathroom')->nullable();
            $table->integer('size')->nullable();
            $table->integer('floor')->nullable();
            $table->integer('garage')->nullable();
            $table->integer('balcony')->nullable();
            $table->string('address')->nullable();
            $table->integer('built_year')->nullable();
            $table->text('map')->nullable();

            // حالة
            $table->string('is_featured');
            $table->string('status');

            // 🔹 إضافات سورية/قانونية
            $table->string('registry_number')->nullable();   // رقم العقار
            $table->string('registry_zone')->nullable();     // المنطقة العقارية
            $table->enum('ownership_type',['full','masha','hikr','waqf','usufruct'])->nullable();
            $table->string('zoning_class')->nullable();      // سكني / تجاري / زراعي ...
            $table->string('building_permit_no')->nullable();
            $table->boolean('build_code_compliance')->default(false);
            $table->boolean('earthquake_resistance')->default(false);
            $table->text('legal_notes')->nullable();

            // 🔹 التحقق والمراجعة
            $table->enum('verification_status',['pending','verified','rejected'])->default('pending');
            $table->text('moderation_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
