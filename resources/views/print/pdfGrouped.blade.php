<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
<header
    style="width: 100%; padding-bottom:10px; display: flex; height: 80px; margin-bottom: 10px;">
    <div style="float: left; width: 10%;">
        <img src="{{ asset(env("MIX_LOGO_LOGIN")) }}" style="max-height: 60px; max-width: 100px;">
    </div>
    <div style="float:left; padding-left: 20px;  width: 90%;">
        <div style="width: 100%; height: 10px; margin: 0; display: block;">
            <h6 style="font-family: Arial; margin: 0; float: left; font-size: 9px;">
                {{env("ESTAR_DIGITAL_IMPRESSAO_NAME")}}
            </h6>
            <h6 style="font-family: Arial; margin: 0 0 5px 0; float: right; font-size: 9px;">
                Emitido por {{$user}} | {{$dateTimeNow}}
            </h6>
        </div>
        <div style="width: 100%; display: block; margin: 0; height: 15px; margin: 5px 0 15px 0;">
            <h6 style="width:100%;display:block;font-size:14px; font-family: Arial; font-weight: bold; margin: 0; text-transform: uppercase;">
                {{$reportName}}
            </h6>
        </div>
        @if(isset($dataTimeBegin) && isset($dataTimeEnd))
            <div>
                <h6 style="font-family: Arial; font-size: 9px; margin: 0; height: 10px">
                    Período: {{$dataTimeBegin}} até {{$dataTimeEnd}}
                </h6>
            </div>
        @endif
    </div>
</header>
@if(count($data) > 0 )
    <div>
        <table class="table table-bordered" style="font-size: 9px;">
            <thead>
            <tr>
                @foreach($headers ?? array_keys($data[0]) as $header)
                    <th style="padding-top: 5px">
                        {{$header}}
                    </th>
                @endforeach
            </tr>
            </thead>

            <tbody>

            @foreach($data as $line)
                <tr style="width: 100% !important;">
                    @foreach($line as $columns)
                        @switch($columns['type'] ?? 'default')
                            @case('money')
                            <td style="padding:5px; text-align: right"
                                @if(isset($columns['colspan'])) colspan="{{$columns['colspan']}}" @endif>
                                R$ {{number_format($columns['value']/100, 2, ',', '') }}</td>
                            @break
                            @case('date-time')
                            <td style="padding:5px;"
                                @if(isset($columns['colspan'])) colspan="{{$columns['colspan']}}" @endif>
                                {{\Carbon\Carbon::createFromTimestamp($columns['value'])->format('d/m/Y H:i:s')}}</td>
                            @break
                            @case('center')
                            <td style="padding:5px; text-align: center"
                                @if(isset($columns['colspan'])) colspan="{{$columns['colspan']}}" @endif>
                                {{$columns['value']}}</td>
                            @break
                            @case('title')
                            <td style="padding:10px;"
                                @if(isset($columns['colspan'])) colspan="{{$columns['colspan']}}" @endif>
                                <b>{{$columns['value']}}</b></td>
                            @break
                            @default
                            <td style="padding:5px;"
                                @if(isset($columns['colspan'])) colspan="{{$columns['colspan']}}" @endif>
                                {{$columns['value']}}</td>
                        @endswitch
                    @endforeach
                </tr>
            @endforeach
            </tbody>
            @if(count($footers) > 0)
                <tfoot>
                <tr>
                    @foreach(array_keys($footers) as $footer)
                        @if(isset($footers[$footer]['totalize']) && $footers[$footer]['totalize'])
                            @switch($footers[$footer]['type'] ?? 'default')
                                @case('money')
                                <td style="padding:5px; text-align: right"
                                    @if(isset($footers[$footer]['colspan'])) colspan="{{$footers[$footer]['colspan']}}" @endif>
                                    R$ {{number_format($data->sum(function ($item) use($footer){ return $item[$footer]['value'];})/100, 2, ',', '')}}</td>
                                @break
                                @case('count')
                                <td style="padding:5px; text-align: right"
                                    @if(isset($footers[$footer]['colspan'])) colspan="{{$footers[$footer]['colspan']}}" @endif>
                                    1
                                </td>
                                @break
                                @default
                                <td style="padding:5px; text-align: right"
                                    @if(isset($footers[$footer]['colspan'])) colspan="{{$footers[$footer]['colspan']}}" @endif>
                                    {{$data->sum(function ($item) use($footer){ return $item[$footer]['value'];})}}</td>
                                @break
                            @endswitch
                        @else
                            <td style="padding:5px"></td>
                        @endif
                    @endforeach
                </tr>
                </tfoot>
            @endif
        </table>
    </div>
@endif
</body>
</html>
