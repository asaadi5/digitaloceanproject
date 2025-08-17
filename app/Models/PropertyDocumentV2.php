<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyDocumentV2 extends Model
{
    protected $table = 'property_documents_v2';

    protected $fillable = ['property_v2_id', 'document_name', 'file_path', 'document_type'];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
