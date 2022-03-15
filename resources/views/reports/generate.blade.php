<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Reporte</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid black;
            text-align: left;
            padding: 2px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>
<body class="antialiased">

<div>
    <div style="border: 1px solid black; margin-bottom: 15px">
        <div style="text-align: center">
            <h3>
                Relación horas monitoría de {{$userName}} en la dependencia {{$dependencyName}}
            </h3>
        </div>
    </div>
    <table class="items-center bg-transparent border-collapse mx-auto ">
        <thead>
        <tr>
            @foreach($header as $headerItem)
                <th>
                    {{$headerItem}}
                </th>
            @endforeach
        </tr>
        </thead>

        <tbody>
        @foreach($records as $record)
            <tr>

                <td>
                    {{$record->start_planned_date}}
                </td>

                <td>
                    {{$record->end_planned_date}}
                </td>

                <td>
                    {{$record->start_monitor_date}}
                </td>
                <td>
                    {{$record->end_monitor_date}}
                </td>
                <td>
                    {{$record->start_approved_date}}
                </td>
                <td>
                    {{$record->end_approved_date}}
                </td>

            </tr>

        @endforeach

        </tbody>
    </table>

    <p>
        Mostrando el periodo comprendido entre: {{$records[0]->start_planned_date}}
        y {{$records[count($records)-1]->start_planned_date}}.
    </p>
    <p>
        Durante este periodo, {{$userName}} realizó: {{number_format($totalHours,0)}} horas y {{$minutes}} minutos.
    </p>
    <p>
        Este reporte ha sido generado por cronos el {{new \Carbon\Carbon()}}.
    </p>
</div>
</body>
</html>







