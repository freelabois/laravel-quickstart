<?php


namespace Freelabois\LaravelQuickstart\Overridden;

use Freelabois\LaravelQuickstart\Traits\ResolveResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class AnonymousResource extends AnonymousResourceCollection
{
    use ResolveResource;

    public function functionResolve($request, $function)
    {
        return $this->collection->map->$function($request)->all();
    }
}
