<?php


namespace Freelabois\LaravelQuickstart\Traits;

use Freelabois\LaravelQuickstart\Exceptions\BadMethodCall;
use Freelabois\LaravelQuickstart\Services\ConvertToPdf;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

trait ResolveResource
{
    public static $conversion_types = [
        'pdf' => ConvertToPdf::class
    ];


    public function resolve($request = null)
    {
        $request = $request ?: Container::getInstance()->make('request');
        $function = 'toArray';

        $export = optional(request())->header('accept');
        $export_type = $export ? explode('/', $export)[1] : null;
        if ($export && in_array($export_type, array_keys(self::$conversion_types))) {
            $function = 'to'. ucfirst($export_type);
        }

        if (method_exists(($this->collects ?? static::class), $function)) {
            $data = $this->functionResolve($request, $function);
        } else {
            throw new BadMethodCall("Não encontrado!");
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        } elseif ($data instanceof \JsonSerializable) {
            $data = $data->jsonSerialize();
        }

        $res = $this->filter((array)$data);

        if ($export && in_array($export_type, array_keys(self::$conversion_types))) {
            $name = 'report_'.now()->format('Ymd_Hisu');
            $converter = app()->make(self::$conversion_types[$export_type]);
            //Retornar o arquivo para download

            $converted = $converter->convert($name, $res);
            $path = 'public/temp/report/'.$name.'.'.$export_type;
            Storage::drive('local')->put($path, $converted);
            $res = [
                'type' => $export_type,
                'path' => env('APP_URL').'/report/download?dl='. Crypt::encrypt($path)
            ];
        }
        return $res;
    }

    abstract function functionResolve($request, $function);
}
