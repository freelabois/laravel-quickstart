<?php

namespace Freelabois\LaravelQuickstart\Traits;

use Freelabois\LaravelQuickstart\Interfaces\ManipulationManagerInterface;
use Freelabois\LaravelQuickstart\Interfaces\RepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

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
        $sanitized = request()->all();
        return $this->resource::collection($this->repository->list($sanitized));
    }
}
