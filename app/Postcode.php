<?php

namespace App;

use App\Values\SpatialTypes\Point;
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
        'lng',
        'point'
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
            $postcode->forceFill(['escaped_postcode' => $postcode->escape($postcode->postcode)]);
        });
    }

    /**
     * Set point attribute.
     *
     * @param  App\Values\SpatialTypes\Point  $point
     * @return void
     */
    public function setPointAttribute(Point $point)
    {
        $this->attributes['point'] = DB::raw($point->toSpatial());
    }

    /**
     * Get point attribute.
     *
     * @param  string                         $point
     * @return App\Values\SpatialTypes\Point
     */
    public function getPointAttribute($point)
    {
        return Point::fromSpatial($point);
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
     * Get nearest postcodes by the given point.
     *
     * @param  Illuminate\Database\Eloquent\Builder  $builder
     * @param  App\Values\SpatialTypes\Point         $point
     * @param  numeric                               $maximumDistance
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeNearestByLatAndLng(Builder $builder, Point $point, float $maximumDistance = null)
    {
        $builder = $builder->orderBy(DB::raw("st_distance(point, {$point->toSpatial()})"), 'asc');

        if (null !== $maximumDistance) {
            $builder->whereRaw("(st_distance_sphere(point, {$point->toSpatial()}) * .000621371192) <= {$maximumDistance}");
        }
    }

    /**
     * Escape postcode, remove all spaces.
     *
     * @param  string  $postcode
     * @return string
     */
    public function escape($postcode)
    {
        return preg_replace('/\s+/', '', $postcode);
    }
}
