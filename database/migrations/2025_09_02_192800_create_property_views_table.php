<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->string('session_id', 100)->nullable();
            $table->string('user_agent', 255)->nullable();

            // بصمة مجهولة للزائر + يوم المشاهدة
            $table->char('viewer_hash', 64);
            $table->date('viewed_on');

            // عدد ضربات نفس الزائر في نفس اليوم (للتقارير)
            $table->unsignedInteger('views')->default(1);

            $table->timestamps();

            // فريد: نفس الزائر لنفس العقار في نفس اليوم
            $table->unique(['property_id','viewer_hash','viewed_on'], 'pv_unique');

            // فهارس مساعدة للتقارير
            $table->index(['property_id','viewed_on'], 'pv_property_day_idx');
            $table->index('viewer_hash', 'pv_viewer_idx');
        });
    }

    public function down(): void {
        Schema::dropIfExists('property_views');
    }
};
