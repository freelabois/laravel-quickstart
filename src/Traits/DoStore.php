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

trait DoStore
{
    /**
     * Store a newly created resource in storage.
     *
     * @param
     * @return Response
     */
    public function store()
    {
        $sanitized = request()->all();
        return new $this->resource($this->manager->storeOrUpdate($sanitized));
    }
}
