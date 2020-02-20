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

trait DoUpdate
{
    /**
     * Update the specified resource in storage.
     *
     * @param
     * @param int $id
     * @return Response
     */
    public function update($id)
    {
        $sanitized = request()->all();
        return new $this->resource($this->manager->storeOrUpdate($sanitized, $id));
    }
}
