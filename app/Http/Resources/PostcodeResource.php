<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PostcodeResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'postcode' => $this->postcode,
            'lat'      => $this->lat,
            'lng'      => $this->lng,
            'distance' => $this->when(isset($this->distance), $this->distance)
        ];
    }
}
