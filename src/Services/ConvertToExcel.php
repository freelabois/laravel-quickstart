<?php
/**
 * Created for Schaffen IT.
 * User: Thiago Traczykowski
 */

namespace Freelabois\LaravelQuickstart\Services;

use Freelabois\LaravelQuickstart\Exports\ExcelExport;
use Freelabois\LaravelQuickstart\Interfaces\DataConverter;
use Maatwebsite\Excel\Facades\Excel;

class ConvertToExcel implements DataConverter
{

    /**
     * @param $name
     * @param $data
     * @param $path
     * @return bool
     */
    public function convert($name, $data, $path)
    {
        if (count($data) > 0)
            $items = collect([array_keys((array) $data[0])]);
        $data = collect($data);
        $data = $items->merge($data);
        return Excel::store(new ExcelExport($data), $path, 'local');
    }
}
