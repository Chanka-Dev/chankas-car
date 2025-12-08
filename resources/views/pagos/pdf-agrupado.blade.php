<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Agrupado</title>
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
        .badge {
            background-color: #007bff;
            color: white;
            padding: 1px 5px;
            border-radius: 2px;
            font-size: 6.5px;
            font-weight: bold;
            display: inline-block;
        }
        .small {
            font-size: 6px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CHANKAS CAR</h1>
        <p>DETALLE DE PAGO AGRUPADO POR SERVICIO</p>
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
                <th style="width: 15%;">Fecha</th>
                <th style="width: 55%;">Servicio</th>
                <th style="width: 15%;" class="text-center">Cantidad</th>
                <th style="width: 15%;" class="text-right">Total (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalGeneral = 0;
            @endphp
            
            @foreach($serviciosPorFecha as $fecha => $servicios)
                @php
                    $serviciosArray = [];
                    foreach($servicios as $nombre => $datos) {
                        $serviciosArray[] = ['nombre' => $nombre, 'datos' => $datos];
                    }
                    $totalDia = array_sum(array_column($servicios, 'total_tecnico'));
                    $totalGeneral += $totalDia;
                @endphp
                
                @foreach($serviciosArray as $index => $servicio)
                    @php
                        $esPrimeraFila = ($index === 0);
                        $esUltimaFila = ($index === count($serviciosArray) - 1);
                    @endphp
                    
                    <tr>
                        @if($esPrimeraFila)
                            <td rowspan="{{ count($serviciosArray) }}" class="text-center" style="background-color: #f9f9f9; font-weight: bold;">
                                {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                            </td>
                        @endif
                        
                        <td><strong>{{ $servicio['nombre'] }}</strong></td>
                        <td class="text-center">
                            <span class="badge">{{ $servicio['datos']['cantidad'] }}</span>
                        </td>
                        <td class="text-right">{{ number_format($servicio['datos']['total_tecnico'], 2) }}</td>
                    </tr>
                @endforeach
                
                {{-- Fila de subtotal del día --}}
                <tr style="background-color: #e8e8e8; font-weight: bold;">
                    <td colspan="3" class="text-right">Subtotal del día:</td>
                    <td class="text-right">{{ number_format($totalDia, 2) }}</td>
                </tr>
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
