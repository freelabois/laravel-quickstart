<?php

namespace Freelabois\LaravelQuickstart\Traits;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;

trait DoIndex
{
    /**
     * Display a listing of the resource.
     *
     * @param
     * @return Response
     */
    public function index()
    {
        $perPage = request()->get('perPage') ?? 45;
        $with = request()->get('with') ?? [];
        $sanitized = request()->except(['perPage', 'with']);
        return $this->resource::collection($this->repository->list($sanitized, $with, $perPage));
    }
}
