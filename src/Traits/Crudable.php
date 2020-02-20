<?php

namespace Freelabois\LaravelQuickstart\Traits;

use Freelabois\LaravelQuickstart\Extendables\Resource;
use Freelabois\LaravelQuickstart\Interfaces\ManipulationManagerInterface;
use Freelabois\LaravelQuickstart\Interfaces\RepositoryInterface;

trait Crudable
{
    use
        DoIndex,
        DoStore,
        DoUpdate,
        DoShow,
        DoDestroy;

    /**
     * @var ManipulationManagerInterface
     */
    protected $manager;
    /**
     * @var RepositoryInterface
     */
    protected $repository;


    /**
     * CrudController constructor.
     * @param ManipulationManagerInterface $manager
     * @param RepositoryInterface $repository
     */
    public function setup(
        ManipulationManagerInterface $manager,
        RepositoryInterface $repository
    )
    {
        $this->manager = $manager;
        $this->repository = $repository;

        if(!isset($this->resource)){
            $this->resource = Resource::class;
        }
    }
}
