<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_documents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('property_id')->constrained()->cascadeOnDelete();
            $t->string('doc_type');      // سند / بيان قيد / عقد إيجار / مخطط ...
            $t->string('issuer')->nullable();
            $t->string('doc_no')->nullable();
            $t->date('issued_at')->nullable();
            $t->string('file_path');
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('property_documents');
    }
};
