<?php

namespace App\Values\SpatialTypes;

class Point
{
    /**
     * Point latitude.
     *
     * @var float
     */
    protected $lat;

    /**
     * Point longitude
     *
     * @var foat
     */
    protected $lng;

    /**
     * Create new instance of Point.
     *
     * @param string|float  $lat
     * @param string|float  $lng
     */
    public function __construct($lat, $lng)
    {
        $this->setLat($lat);
        $this->setLng($lng);
    }

    /**
     * Get point latitude
     *
     * @return float
     */
    public function getLat(): float
    {
        return $this->lat;
    }

    /**
     * Set point latitude
     *
     * @param  string|float  $lat
     * @return void
     */
    public function setLat($lat)
    {
        $this->lat = (float) $lat;
    }

    /**
     * Get point longitude
     *
     * @return float
     */
    public function getLng(): float
    {
        return $this->lng;
    }

    /**
     * Set point longitude.
     *
     * @param string|float $lng
     */
    public function setLng($lng)
    {
        $this->lng = (float) $lng;
    }

    /**
     * Convert to spatial raw data.
     *
     * @return string
     */
    public function toSpatial(): string
    {
        return sprintf('POINT(%s)', (string) $this);
    }

    /**
     * Convert to Point from spatial raw data.
     *
     * @param  string                         $value
     * @return App\Values\SpatialTypes\Point
     */
    public static function fromSpatial(string $value): Point
    {
        $left  = strpos($value, '(');
        $right = strrpos($value, ')');

        list($lat, $lng) = explode(',', preg_replace('/[ ,]+/', ',', trim(substr($value, $left + 1, $right - $left - 1)), 1));

        return new static($lat, $lng);
    }

    /**
     * Convert point to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getLat() . ', ' . $this->getLng();
    }
}