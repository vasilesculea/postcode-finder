<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Postcode extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'postcodes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'postcode',
        'lat',
        'lng'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($postcode) {
            $postcode->forceFill(['escaped_postcode' => $this->escape($postcode->postcode)]);
        });
    }

    /**
     * Search postcodes.
     *
     * @param  Illuminate\Database\Eloquent\Builder  $builder
     * @param  string                                $postcode
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $builder, $postcode)
    {
        $builder->where('escaped_postcode', 'LIKE', "%{$this->escape($postcode)}%");
    }

    /**
     * Get nearest postcodes by lat and lng.
     * Distance is calculating in miles for KM we should change number 3959 to 6371 (maybe there should be a configuration file?)
     *
     * @param  Illuminate\Database\Eloquent\Builder  $builder
     * @param  decimal                               $lat
     * @param  decimal                               $lng
     * @param  numeric                               $maximumDistance
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeNearestByLatAndLng(Builder $builder, $lat, $lng, $maximumDistance = null)
    {
        $distanceFormula = DB::raw("(3959 * acos(cos(radians({$lat})) * cos(radians(`lat`)) * cos(radians(`lng`) - radians({$lng})) + sin(radians({$lat})) * sin(radians(`lat`))))");

        $builder = $builder->select(['*', DB::raw("{$distanceFormula} AS `distance`")])
            ->orderBy('distance', 'asc');

        if (null !== $maximumDistance) {
            $builder->where($distanceFormula, '<=', $maximumDistance);
        }
    }

    /**
     * Escape postcode, remove all spaces.
     *
     * @param  string  $postcode
     * @return string
     */
    protected function escape($postcode)
    {
        return preg_replace('/\s+/', '', $postcode);
    }
}
