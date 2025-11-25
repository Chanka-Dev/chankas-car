<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Pago</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info-empleado {
            margin-bottom: 20px;
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
        }
        .info-empleado p {
            margin: 5px 0;
        }
        .fecha-grupo {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .fecha-header {
            background-color: #333;
            color: white;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #666;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        .subtotal-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .total-final {
            margin-top: 30px;
            background-color: #28a745;
            color: white;
            padding: 15px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .servicios-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .servicios-list li {
            font-size: 10px;
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CHANKAS CAR</h1>
        <p>Detalle de Pago - Técnico</p>
    </div>

    <div class="info-empleado">
        <p><strong>Técnico:</strong> {{ $empleado->nombre }} {{ $empleado->apellido }}</p>
        <p><strong>Cargo:</strong> {{ $empleado->cargo->nombre }}</p>
        <p><strong>CI:</strong> {{ $empleado->ci }}</p>
        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
        <p><strong>Fecha de Emisión:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    @foreach($trabajosPorFecha as $fecha => $trabajosDia)
        <div class="fecha-grupo">
            <div class="fecha-header">
                {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }} - 
                {{ $trabajosDia->count() }} trabajo(s) - 
                Bs {{ number_format($trabajosDia->sum('total_tecnico'), 2) }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Fecha</th>
                        <th>Servicios Realizados</th>
                        <th style="text-align: right;">Comisión</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trabajosDia as $trabajo)
                        <tr>
                            <td><strong>{{ $trabajo->cliente ? $trabajo->cliente->placas : 'SIN PLACA' }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($trabajo->fecha_trabajo)->format('d/m/Y') }}</td>
                            <td>
                                <ul class="servicios-list">
                                    @foreach($trabajo->trabajoServicios as $ts)
                                        <li>
                                            • {{ $ts->servicio->nombre }}
                                            @if($ts->cantidad > 1)
                                                (x{{ $ts->cantidad }})
                                            @endif
                                            - Bs {{ number_format($ts->importe_tecnico, 2) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-right">Bs {{ number_format($trabajo->total_tecnico, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="2" class="text-right">Subtotal del día:</td>
                        <td class="text-right">Bs {{ number_format($trabajosDia->sum('total_tecnico'), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="total-final">
        TOTAL A PAGAR: Bs {{ number_format($totalComision, 2) }}
    </div>

    <div class="footer">
        <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        <p>Chankas Car - Sistema de Gestión</p>
    </div>
</body>
</html>