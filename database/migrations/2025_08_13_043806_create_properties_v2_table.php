<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('properties_v2', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();

            // ربط مرن مع جداولك الحالية (بلا مفاتيح خارجية الآن لتجنب أي تعارض أنواع)
            $table->unsignedBigInteger('agent_id')->nullable()->index();
            $table->unsignedBigInteger('location_id')->nullable()->index();
            $table->unsignedBigInteger('type_id')->nullable()->index();

            // أساسيّات العرض
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // التسعير
            $table->decimal('price', 14, 2)->nullable();
            $table->enum('price_currency', ['SYP','USD'])->default('USD');
            $table->enum('price_type', ['ثابت','قابل للتفاوض'])->default('قابل للتفاوض');
            $table->string('price_note', 255)->nullable();

            // الغرض/الإيجار
            $table->enum('purpose', ['بيع','إيجار','استثمار','مزاد'])->nullable();
            $table->enum('rent_period', ['شهري','سنوي'])->nullable();
            $table->integer('deposit_amount')->nullable();
            $table->boolean('furnished')->default(false);
            $table->boolean('pets_allowed')->default(false);

            // المواصفات
            $table->decimal('area_m2', 10, 2)->nullable();      // المساحة الصافية أو العامة
            $table->decimal('land_area_m2', 10, 2)->nullable();  // إن كان عنده أرض
            $table->decimal('building_area_m2', 10, 2)->nullable(); // مسطح بناء
            $table->tinyInteger('bedroom')->nullable();
            $table->tinyInteger('bathroom')->nullable();
            $table->tinyInteger('floor')->nullable();
            $table->tinyInteger('total_floors')->nullable();
            $table->tinyInteger('parking_spaces')->nullable();
            $table->boolean('elevator')->default(false);

            // تشطيب/بنية وتجهيزات
            $table->enum('finishing', ['عظم','قيد الإكساء','جاهز','سوبر ديلوكس'])->nullable();
            $table->enum('structure', ['بيتوني','حجري','طوب/بلوك','معدني'])->nullable();
            $table->enum('heating', ['مازوت','كهرباء','غاز','مركزي','شومينيه','شمسي','لا يوجد'])->nullable();
            $table->enum('ac', ['لا يوجد','شباك','سبلت','مركزي'])->nullable();
            $table->set('orientation', ['شمال','جنوب','شرق','غرب'])->nullable();
            $table->enum('view', ['مدينة','بحر','جبل','حديقة','شارع','داخلية'])->nullable();
            $table->set('utilities', ['كهرباء','ماء','صرف صحي','هاتف','إنترنت','غاز'])->nullable();

            // قانوني
            $table->enum('legal_title', ['طابو أخضر','سجل مؤقت','وكالة غير قابلة للعزل','قيد الإنشاء','عقد بيع'])->nullable();
            $table->set('legal_flags', ['رهن','حجز','منع بيع','دعوى'])->nullable();
            $table->string('title_notes', 255)->nullable();

            // العنوان والإحداثيات
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->string('street', 150)->nullable();
            $table->integer('built_year')->nullable();
            $table->decimal('geo_lat', 10, 7)->nullable();
            $table->decimal('geo_lng', 10, 7)->nullable();

            // صور أساسية / حالة
            $table->string('featured_photo', 255)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status', 20)->default('Active');

            // للتتبّع
            $table->timestamp('listed_at')->nullable();

            $table->timestamps();

            // فهرس للبحث السريع
            $table->index(['location_id', 'type_id', 'price'], 'idx_properties_v2_lookup');

            // فهرس نصّي (لو MySQL/MariaDB يدعم FULLTEXT مع InnoDB عندك)
            // ملاحظة: على بعض إصدارات MariaDB القديمة قد يلزم MyISAM.
            $table->fullText(['name','description'], 'ft_properties_v2_text');
        });
    }

    public function down(): void {
        Schema::dropIfExists('properties_v2');
    }
};
