<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'registry_number')) $table->string('registry_number')->nullable()->after('address');
            if (!Schema::hasColumn('properties', 'registry_zone')) $table->string('registry_zone')->nullable()->after('registry_number');
            if (!Schema::hasColumn('properties', 'ownership_type')) $table->enum('ownership_type',['full','masha','hikr','waqf','usufruct'])->nullable()->after('registry_zone');
            if (!Schema::hasColumn('properties', 'zoning_class')) $table->string('zoning_class')->nullable()->after('ownership_type');
            if (!Schema::hasColumn('properties', 'building_permit_no')) $table->string('building_permit_no')->nullable()->after('zoning_class');
            if (!Schema::hasColumn('properties', 'build_code_compliance')) $table->boolean('build_code_compliance')->default(false)->after('building_permit_no');
            if (!Schema::hasColumn('properties', 'earthquake_resistance')) $table->boolean('earthquake_resistance')->default(false)->after('build_code_compliance');
            if (!Schema::hasColumn('properties', 'legal_notes')) $table->text('legal_notes')->nullable()->after('earthquake_resistance');
            if (!Schema::hasColumn('properties', 'verification_status')) $table->enum('verification_status',['pending','verified','rejected'])->default('pending')->after('status');
            if (!Schema::hasColumn('properties', 'moderation_notes')) $table->text('moderation_notes')->nullable()->after('verification_status');
        });
    }
    public function down(): void {
        Schema::table('properties', function (Blueprint $table) {
            foreach ([
                         'registry_number','registry_zone','ownership_type','zoning_class','building_permit_no',
                         'build_code_compliance','earthquake_resistance','legal_notes','verification_status','moderation_notes'
                     ] as $col) {
                if (Schema::hasColumn('properties', $col)) $table->dropColumn($col);
            }
        });
    }
};
