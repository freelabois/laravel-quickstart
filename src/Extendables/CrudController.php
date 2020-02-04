<?php

namespace Freelabois\LaravelQuickstart\Extendables;

use Freelabois\LaravelQuickstart\Interfaces\ManipulationManagerInterface;
use Freelabois\LaravelQuickstart\Interfaces\RepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class CrudController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * @var ManipulationManagerInterface
     */
    protected $manager;
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    protected $resource = Resource::class;

    /**
     * CrudController constructor.
     * @param ManipulationManagerInterface $manager
     * @param RepositoryInterface $repository
     */
    public function __construct(
        ManipulationManagerInterface $manager,
        RepositoryInterface $repository
    )
    {

        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $sanitized = $request->all();
        return $this->resource::collection($this->repository->list($sanitized));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $sanitized = $request->all();
        return new $this->resource($this->manager->storeOrUpdate($sanitized));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return new $this->resource($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $sanitized = $request->all();
        return new $this->resource($this->manager->storeOrUpdate($sanitized, $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        return $this->manager->destroy($id);
    }

}
