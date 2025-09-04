<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {

            $table->foreignId('type_id')
                ->nullable()
                ->after('photo')
                ->constrained('types')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->index(['type_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['type_id', 'created_at']);
            $table->dropConstrainedForeignId('type_id');
        });
    }
};
