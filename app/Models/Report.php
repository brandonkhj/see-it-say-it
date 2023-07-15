<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Report extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const MEDIA_COLLECTION_ATTACHMENT = 'attachment';

    protected $guarded = [];

    protected $appends = [
        'map', 'report_id'
    ];

    public function getReportIdAttribute(): string
    {
        return str_pad(0, 4, '0', STR_PAD_LEFT) . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT) ;
    }

    public function getMapAttribute(): array
    {
        return [
            "lat" => (float)$this->latitude,
            "lng" => (float)$this->longitude,
        ];
    }

    public function setMapAttribute(?array $location): void
    {
        if (is_array($location))
        {
            $this->attributes['latitude'] = $location['lat'];
            $this->attributes['longitude'] = $location['lng'];
            unset($this->attributes['map']);
        }
    }

    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'latitude',
            'lng' => 'longitude',
        ];
    }

    public static function getComputedLocation(): string
    {
        return 'map';
    }
}
