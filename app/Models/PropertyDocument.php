<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyDocument extends Model
{
    protected $table = 'property_documents';

    protected $fillable = [
        'property_id',
        'doc_type',
        'issuer',
        'doc_no',
        'issued_at',
        'file_path',
    ];

    protected $casts = [
        'issued_at' => 'date:Y-m-d', // تنسيق عرض التاريخ
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }


    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;
        return str_starts_with($this->file_path, 'http')
            ? $this->file_path
            : asset($this->file_path);
    }
}
