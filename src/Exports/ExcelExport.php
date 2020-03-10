<?php

namespace Freelabois\LaravelQuickstart\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExcelExport implements FromCollection
{

    /**
     * @var Collection
     */
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
    * @return Collection
    */
    public function collection()
    {
        return $this->collection;
    }
}
