<?php

namespace App\Http\Controllers\Api;

use App\Postcode;
use App\Values\SpatialTypes\Point;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostcodeResource;
use App\Http\Requests\PostcodePositionRequest;

class PostcodesPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\Response
     */
    public function index(PostcodePositionRequest $request)
    {
        $postcodes = Postcode::nearestByLatAndLng(
            new Point($request->input('lat'), $request->input('lng'))
        )->paginate();

        return PostcodeResource::collection($postcodes);
    }
}
