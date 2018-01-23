<?php

namespace App\Http\Controllers\Api;

use App\Postcode;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostcodeResource;
use App\Http\Requests\PostcodeSearchRequest;

class PostcodesSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\Response
     */
    public function index(PostcodeSearchRequest $request)
    {
        $postcodes = Postcode::search($request->input('postcode'))->paginate();

        return PostcodeResource::collection($postcodes);
    }
}
