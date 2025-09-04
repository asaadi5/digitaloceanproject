<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * PropertyResource
 * - Keeps your current JSON keys stable for Flutter.
 * - Relations are included only when eager-loaded to avoid N+1.
 * - Builds absolute URLs for photos/docs for direct mobile consumption.
 */
class PropertyResource extends JsonResource
{
    public function toArray($request)
    {
        // Photos list (only when relation is eager-loaded)
        $photos = $this->whenLoaded('photos', function () {
            return $this->photos->map(function ($p) {
                return [
                    'id'  => $p->id,
                    // Absolute URL to image file (adjust 'uploads' path if you change storage)
                    'url' => $p->photo ? url('uploads/'.$p->photo) : null,
                ];
            })->values();
        });

        // Videos list (only when relation is eager-loaded)
        $videos = $this->whenLoaded('videos', function () {
            return $this->videos->map(fn($v) => [
                'id'    => $v->id,
                'video' => $v->video, // could be URL or embed code as per your DB
            ])->values();
        });

        // Documents list (only when relation is eager-loaded)
        $documents = $this->whenLoaded('documents', function () {
            return $this->documents->map(fn($d) => [
                'id'        => $d->id,
                'doc_type'  => $d->doc_type,
                'issuer'    => $d->issuer,
                'doc_no'    => $d->doc_no,
                'issued_at' => $d->issued_at,
                'file_url'  => $d->file_path ? url('uploads/'.$d->file_path) : null,
            ])->values();
        });

        return [
            // Primitive fields (stable)
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'price'       => $this->price,
            'size'        => $this->size,
            'purpose'     => $this->purpose,     // sale|rent|wanted (as stored)
            'is_featured' => $this->is_featured, // keep raw value (Yes/No) to avoid breaking clients
            'status'      => $this->status,      // Active|Pending...
            'total_views' => $this->total_views,
            'address'     => $this->address,

            // Featured photo absolute URL (handy for cards in Flutter)
            'featured_photo_url' => $this->featured_photo ? url('uploads/'.$this->featured_photo) : null,

            // Optional timestamps (nice-to-have; won’t break older clients)
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),

            // Relations (only if eager-loaded to keep payload predictable)
            'location' => $this->whenLoaded('location', function () {
                return [
                    'id'   => $this->location->id,
                    'name' => $this->location->name,
                    'slug' => $this->location->slug,
                ];
            }),

            'type' => $this->whenLoaded('type', function () {
                return [
                    'id'        => $this->type->id,
                    'name'      => $this->type->name,
                    'slug'      => $this->type->slug,
                    'parent_id' => $this->type->parent_id,
                ];
            }),

            'agent' => $this->whenLoaded('agent', function () {
                return [
                    'id'    => $this->agent->id,
                    'name'  => $this->agent->name,
                    'email' => $this->agent->email,
                    'phone' => $this->agent->phone,
                    'city'  => $this->agent->city,
                    'photo' => $this->agent->photo ? url('uploads/'.$this->agent->photo) : null,
                ];
            }),

            'photos'    => $photos,
            'videos'    => $videos,
            'documents' => $documents,

            // Optional extras if controller sets them (kept optional to preserve old clients)
            'amenities'     => $this->when(isset($this->amenities_list), $this->amenities_list),
            'price_history' => $this->when(isset($this->price_history), $this->price_history),
            'rental_rules'  => $this->when(isset($this->rental_rules), $this->rental_rules),

            // If you use withWishlistedCountFor() in queries
            'wishlists_count' => $this->when(isset($this->wishlists_count), (int) $this->wishlists_count),
        ];
    }
}
