<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Tareas</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f3f3f3;
            font-family: Arial, sans-serif;
            color: #1b1b1b;
            font-size: 12px;
        }
        .sheet {
            width: 650px;
            margin: 18px auto;
            background: #ffffff;
            border: 1px solid #cfcfcf;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
            padding: 18px 20px 24px;
        }
        .header-frame {
            border: 1px solid #c8ccd4;
            padding: 12px;
            margin-bottom: 14px;
        }
        .header-row {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .logo-col,
        .company-col,
        .filters-col {
            display: table-cell;
            vertical-align: top;
        }
        .logo-col {
            width: 150px;
        }
        .company-col {
            width: 250px;
            padding: 4px 10px 0;
        }
        .filters-col {
            width: 300px;
        }
        .logo {
            width: 140px;
            height: auto;
            display: block;
        }
        .company-line {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1f2c46;
            margin-top: 4px;
        }
        .filters-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .filters-grid td {
            border: 1px solid #9ba3b8;
            padding: 4px 6px;
            font-size: 11px;
        }
        .filters-grid .label {
            background: #08388f;
            color: #ffffff;
            text-transform: uppercase;
            font-weight: 700;
            width: 35%;
        }
        .filters-grid .value {
            background: #ffffff;
            color: #1b1b1b;
        }
        .report-title {
            margin: 12px auto 14px;
            border: 1px solid #9ba3b8;
            text-transform: uppercase;
            text-align: center;
            width: 420px;
            padding: 6px 8px;
            color: #08388f;
            font-size: 18px;
            font-weight: 700;
        }
        .project-box {
            margin-bottom: 16px;
        }
        .project-title {
            border: 1px solid #aeb6ca;
            border-bottom: 0;
            text-transform: uppercase;
            font-size: 13px;
            font-weight: 700;
            padding: 5px 8px;
            text-align: center;
            background: #f6f8fc;
        }
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.report-table th,
        table.report-table td {
            border: 1px solid #aeb6ca;
            padding: 5px 6px;
            font-size: 11px;
            word-break: break-word;
        }
        table.report-table th {
            background: #08388f;
            color: #ffffff;
            text-transform: uppercase;
            font-weight: 700;
        }
        table.report-table th:nth-child(1),
        table.report-table td:nth-child(1) { width: 8%; }
        table.report-table th:nth-child(2),
        table.report-table td:nth-child(2) { width: 20%; }
        table.report-table th:nth-child(3),
        table.report-table td:nth-child(3) { width: 20%; }
        table.report-table th:nth-child(4),
        table.report-table td:nth-child(4) { width: 10%; text-align: right; }
        table.report-table th:nth-child(5),
        table.report-table td:nth-child(5) { width: 15%; }
        table.report-table th:nth-child(6),
        table.report-table td:nth-child(6) { width: 27%; }
        .total-mins {
            margin: 8px auto 0;
            width: 220px;
            text-align: center;
            font-size: 12px;
            font-weight: 700;
        }
        .total-mins span {
            border: 1px solid #aeb6ca;
            padding: 2px 8px;
            margin-left: 8px;
            display: inline-block;
            min-width: 48px;
            background: #ffffff;
        }
    </style>
</head>
<body>
@php
    $logoPath = resource_path('img/LogoSIMJ.png');
    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
@endphp
<div class="sheet">
    <div class="header-frame">
        <div class="header-row">
            <div class="logo-col">
                @if($logoData)
                    <img class="logo" src="data:image/png;base64,{{ $logoData }}" alt="SIMJ" />
                @else
                    <div class="logo">SIMJ</div>
                @endif
            </div>
            <div class="company-col">
                <div class="company-line">1 - Soluciones Informáticas MJ S.C.A</div>
            </div>
            <div class="filters-col">
                <table class="filters-grid">
                    <tr>
                        <td class="label">Desde fecha</td>
                        <td class="value">{{ $appliedFilters['Desde'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Hasta fecha</td>
                        <td class="value">{{ $appliedFilters['Hasta'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Proyecto</td>
                        <td class="value">{{ $appliedFilters['Proyecto'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Usuario</td>
                        <td class="value">{{ $appliedFilters['Usuario'] }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="report-title">Informe de tareas realizadas</div>

    @foreach($projects as $project)
        <div class="project-box">
            <div class="project-title">{{ $project['project_name'] }}</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Min</th>
                        <th>Usuario</th>
                        <th>Tarea realizada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project['tasks'] as $task)
                        <tr>
                            <td>{{ $task['id'] }}</td>
                            <td>{{ $task['start'] }}</td>
                            <td>{{ $task['end'] }}</td>
                            <td>{{ $task['minutes'] }}</td>
                            <td>{{ $task['user'] }}</td>
                            <td>{{ $task['description'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="total-mins">
                TOTAL MINS:
                <span>{{ number_format($project['total_minutes'], 0) }}</span>
            </div>
        </div>
    @endforeach
</div>
</body>
</html>
