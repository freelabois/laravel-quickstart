<?php


namespace Freelabois\LaravelQuickstart\Overridden;

use Freelabois\LaravelQuickstart\Traits\ResolveResource;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource implements Responsable
{
    protected $collects = null;
    use ResolveResource;
    public static function collection($resource)
    {
        return tap(new AnonymousResource($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }

    public function functionResolve($request, $function)
    {
        return $this->$function($request);
    }
}
