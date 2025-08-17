<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('property_documents_v2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_v2_id')->index();
            $table->enum('kind', ['سند ملكية','كشف طابو','مخطط تنظيمي','عقد بيع','عقد إيجار','بيان قيد','ترخيص بناء','مخطط معماري','إفادة'])->nullable();
            $table->string('file'); // مسار الملف
            $table->string('issued_by', 150)->nullable();
            $table->date('issue_date')->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('property_documents_v2');
    }
};
