<?php

namespace Freelabois\LaravelQuickstart\Extendables;

use Freelabois\LaravelQuickstart\Interfaces\ManipulationManagerInterface;
use Freelabois\LaravelQuickstart\Interfaces\RepositoryInterface;
use Freelabois\LaravelQuickstart\Traits\Crudable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CrudController
{
    use
        AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests,
        Crudable;

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
        $this->setup($manager, $repository);
    }
}
