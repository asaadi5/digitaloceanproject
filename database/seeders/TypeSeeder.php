<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;
use Illuminate\Support\Str;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $flat = [
            'شقة','بيت عربي','فيلا','روف/ملحق',
            'أرض زراعية','أرض للبناء',
            'مكتب','مستودع','محل تجاري',
            'مزرعة','شاليه',
        ];

        foreach ($flat as $name) {
            Type::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            );
        }
    }
}
