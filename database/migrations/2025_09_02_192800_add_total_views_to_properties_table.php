<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties','total_views')) {
                $table->unsignedInteger('total_views')->default(0)->after('status');
            }
        });
    }

    public function down(): void {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties','total_views')) {
                $table->dropColumn('total_views');
            }
        });
    }
};

