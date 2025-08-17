<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyWarehouseV2 extends Model
{
    protected $table = 'property_warehouses_v2';

    protected $fillable = [
        'property_v2_id', 'warehouse_area_m2', 'ceiling_height_m', 'floor_type',
        'has_loading_dock', 'has_cold_storage', 'electricity_capacity_kw', 'security_system', 'notes'
    ];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
