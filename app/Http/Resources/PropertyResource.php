<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray($request)
    {
        $photos = $this->whenLoaded('photos', function () {
            return $this->photos->map(function ($p) {
                return [
                    'id'  => (int) $p->id,
                    'url' => $p->photo ? url('uploads/'.$p->photo) : null,
                ];
            })->values();
        });

        $videos = $this->whenLoaded('videos', function () {
            return $this->videos->map(fn($v) => [
                'id'    => (int) $v->id,
                'video' => (string) $v->video, // YouTube ID أو رابط كما هو
            ])->values();
        });

        $documents = $this->whenLoaded('documents', function () {
            return $this->documents->map(fn($d) => [
                'id'        => (int) $d->id,
                'doc_type'  => $d->doc_type,
                'issuer'    => $d->issuer,
                'doc_no'    => $d->doc_no,
                'issued_at' => $d->issued_at, // ISO من DB لو محفوظ تاريخ
                'file_url'  => $d->file_path ? url('uploads/'.$d->file_path) : null,
            ])->values();
        });

        return [
            // Primaries
            'id'          => (int) $this->id,
            'name'        => (string) $this->name,
            'slug'        => (string) $this->slug,
            'price'       => $this->price !== null ? (float) $this->price : null,
            'size'        => $this->size !== null ? (int) $this->size : null,
            'bedroom'     => $this->bedroom !== null ? (int) $this->bedroom : null,
            'bathroom'    => $this->bathroom !== null ? (int) $this->bathroom : null,
            'floor'       => $this->floor,       // لو أرقام فقط: (int) $this->floor
            'garage'      => $this->garage,
            'balcony'     => $this->balcony,
            'built_year'  => $this->built_year,
            'area'        => $this->area,
            'purpose'     => (string) $this->purpose,     // buy|rent|sale|wanted
            'is_featured' => $this->is_featured,          // حافظ على القيم الخام (yes/Yes/no…)
            'status'      => (string) $this->status,      // active|Pending…
            'total_views' => $this->total_views !== null ? (int) $this->total_views : 0,
            'address'     => $this->address,

            'featured_photo_url' => $this->featured_photo ? url('uploads/'.$this->featured_photo) : null,

            // Timestamps (ISO-8601 لو متوفرة)
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),

            // Relations
            'location' => $this->whenLoaded('location', function () {
                return [
                    'id'   => (int) $this->location->id,
                    'name' => (string) $this->location->name,
                    'slug' => $this->location->slug,
                ];
            }),

            'type' => $this->whenLoaded('type', function () {
                return [
                    'id'        => (int) $this->type->id,
                    'name'      => (string) $this->type->name,
                    'slug'      => $this->type->slug,
                    'parent_id' => $this->type->parent_id !== null ? (int) $this->type->parent_id : null,
                ];
            }),

            'agent' => $this->whenLoaded('agent', function () {
                return [
                    'id'    => (int) $this->agent->id,
                    'name'  => (string) $this->agent->name,
                    'email' => $this->agent->email,
                    'phone' => $this->agent->phone,
                    'city'  => $this->agent->city,
                    'photo' => $this->agent->photo ? url('uploads/'.$this->agent->photo) : null,
                ];
            }),

            'photos'    => $photos,
            'videos'    => $videos,
            'documents' => $documents,

            // Optional extras (عبر controller إذا حابب تحقنها)
            'amenities'        => $this->when(isset($this->amenities_list), $this->amenities_list),
            'price_history'    => $this->when(isset($this->price_history), $this->price_history),
            'rental_rules'     => $this->when(isset($this->rental_rules), $this->rental_rules),
            'wishlists_count'  => $this->when(isset($this->wishlists_count), (int) $this->wishlists_count),
        ];
    }
}
