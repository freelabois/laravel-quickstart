<?php


namespace Freelabois\LaravelQuickstart\Extendables;

use Freelabois\LaravelQuickstart\Overridden\BaseResource;

class Resource extends BaseResource
{
   public function toArray($request)
   {
      return parent::toArray($request);
   }
}