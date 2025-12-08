<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Semanal</title>
    <style>
        @page {
            margin: 10mm 8mm;
            size: letter;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 1.5px solid #000;
            padding-bottom: 4px;
        }
        .header h1 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 8px;
        }
        .info-empleado {
            margin-bottom: 6px;
            background-color: #f0f0f0;
            padding: 4px 6px;
            display: flex;
            justify-content: space-between;
        }
        .info-empleado div {
            margin: 0;
            line-height: 1.3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        table th {
            background-color: #333;
            color: white;
            padding: 3px 4px;
            text-align: left;
            font-size: 7px;
            font-weight: bold;
            border: 1px solid #000;
        }
        table td {
            padding: 2px 4px;
            border: 1px solid #ddd;
            font-size: 7px;
            vertical-align: top;
        }
        .fecha-header {
            background-color: #555;
            color: white;
            padding: 2px 4px;
            font-weight: bold;
            font-size: 7px;
            border: 1px solid #000;
        }
        .subtotal-row {
            background-color: #e8e8e8;
            font-weight: bold;
            font-size: 7px;
        }
        .total-final {
            margin-top: 6px;
            background-color: #000;
            color: white;
            padding: 6px;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .servicios-compact {
            font-size: 6.5px;
            line-height: 1.2;
        }
        .small {
            font-size: 6px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CHANKAS CAR</h1>
        <p>DETALLE DE PAGO SEMANAL</p>
    </div>

    <div class="info-empleado">
        <div>
            <strong>Técnico:</strong> {{ $empleado->nombre }} {{ $empleado->apellido }} | 
            <strong>CI:</strong> {{ $empleado->ci }}
        </div>
        <div>
            <strong>Período:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }} | 
            <strong>Emisión:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 12%;">Placa</th>
                <th style="width: 58%;">Servicios Realizados</th>
                <th style="width: 10%;" class="text-right">Com. (Bs)</th>
                <th style="width: 10%;" class="text-right">Total (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDia = 0;
                $fechaActual = null;
                $trabajosDelDia = 0;
            @endphp
            
            @foreach($trabajosPorFecha as $fecha => $trabajosDia)
                @foreach($trabajosDia as $index => $trabajo)
                    @php
                        $esPrimeraFilaDelDia = ($index === 0);
                        $esUltimaFilaDelDia = ($index === count($trabajosDia) - 1);
                    @endphp
                    
                    <tr>
                        @if($esPrimeraFilaDelDia)
                            <td rowspan="{{ count($trabajosDia) }}" class="text-center" style="background-color: #f9f9f9; font-weight: bold;">
                                {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                            </td>
                        @endif
                        
                        <td><strong>{{ $trabajo->cliente ? $trabajo->cliente->placas : 'S/P' }}</strong></td>
                        <td class="servicios-compact">
                            @foreach($trabajo->trabajoServicios as $ts)
                                • {{ $ts->servicio->nombre }}
                                @if($ts->cantidad > 1) <span class="small">(x{{ $ts->cantidad }})</span> @endif
                                <span class="small">Bs {{ number_format($ts->importe_tecnico, 2) }}</span>
                                @if(!$loop->last) | @endif
                            @endforeach
                        </td>
                        <td class="text-right">{{ number_format($trabajo->total_tecnico, 2) }}</td>
                        
                        @if($esPrimeraFilaDelDia)
                            <td rowspan="{{ count($trabajosDia) }}" class="text-right" style="background-color: #f0f0f0; font-weight: bold; font-size: 8px;">
                                {{ number_format($trabajosDia->sum('total_tecnico'), 2) }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="total-final">
        TOTAL COMISIONES DEL PERÍODO: Bs {{ number_format($totalComision, 2) }}
    </div>

    <div style="margin-top: 8px; text-align: center; font-size: 6px; color: #666;">
        Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} - Chankas Car
    </div>
</body>
</html>