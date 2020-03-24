<?php
/**
 * Created for Schaffen IT.
 * User: Thiago Traczykowski
 */

namespace Freelabois\LaravelQuickstart\Services;

use Carbon\Carbon;
use Freelabois\LaravelQuickstart\Interfaces\DataConverter;
use Illuminate\Support\Facades\Storage;

class ConvertToPdf implements DataConverter
{

    public function convert($name, $data, $path)
    {
        $data = collect($data);
        if (isset($data['print_type'])) {
            $file = $data['print_type'];
            unset($data['print_type']);
        }
        if (isset($data['headers'])) {
            $headers = $data['headers'];
            unset($data['headers']);
        } else {
            foreach (array_keys($data[0]) as $header) {
                $headers[] = $header;
            }
        }
        if (isset($data['footers'])) {
            $footers = $data['footers'];
            unset($data['footers']);
        } else {
            $span = 1;
            foreach (array_keys($data[0]) as $footer) {
                if (isset($data[0][$footer]['totalize'])
                    && (is_bool($data[0][$footer]['totalize']) ? $data[0][$footer]['totalize'] : $data[0][$footer]['totalize'] == 'true')
                ) {
                    $footers[$footer] = ['totalize' => true, 'type' => $data[0][$footer]['type'] ?? 'default', 'colspan' => $span];
                    $span = 1;
                } else
                    $span++;
            }
        }

        $filters = request()->all();
        $pdf = app()->make('snappy.pdf.wrapper');
        $load = [
            'data' => collect($data),
            'filter' => $filters,
            'headers' => $headers ?? [],
            'footers' => $footers ?? [],
            'reportName' => isset($filters['report_front_name']) ? $filters['report_front_name'] : 'Relatório',
            'dateTimeNow' => Carbon::now()->format('d/m/Y H:i:s'),
            'user' => auth()->user()->username . ' - ' . auth()->user()->name
        ];
        if (isset($filters['created_at_begin']) || isset($filters['date_time_begin']))
            $load['dataTimeBegin'] = Carbon::createFromFormat('Y-m-d H:i:s', $filters['created_at_begin'] ?? $filters['date_time_begin'])->format('d/m/Y H:i:s');
        if (isset($filters['created_at_end']) || isset($filters['date_time_end']))
            $load['dataTimeEnd'] = Carbon::createFromFormat('Y-m-d H:i:s', $filters['created_at_end'] ?? $filters['date_time_end'])->format('d/m/Y H:i:s');

        $pdf->loadView('print.' . ($file ?? 'pdf'), $load);
        $converted = $pdf
            ->setPaper('A4', 'landscape')
            ->download()
            ->getOriginalContent();
        Storage::drive('local')->put($path, $converted);
        return true;
    }
}
