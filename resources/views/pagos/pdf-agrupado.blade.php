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
            margin-bottom: 6px;
            border-bottom: 1.5px solid #000;
            padding-bottom: 3px;
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
            margin-bottom: 5px;
            background-color: #f0f0f0;
            padding: 4px 6px;
            display: flex;
            justify-content: space-between;
        }
        .info-empleado div {
            margin: 0;
            line-height: 1.3;
        }
        .resumen-box {
            margin-bottom: 5px;
            border: 1px solid #333;
            padding: 3px 6px;
            background-color: #f9f9f9;
            font-size: 7px;
        }
        .resumen-row {
            display: flex;
            justify-content: space-between;
            padding: 1px 0;
            line-height: 1.3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
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
        .fecha-card {
            margin-bottom: 3px;
        }
        .fecha-header {
            background-color: #555;
            color: white;
            padding: 2px 4px;
            font-weight: bold;
            font-size: 7px;
            border: 1px solid #000;
            display: flex;
            justify-content: space-between;
        }
        .subtotal-row {
            background-color: #e8e8e8;
            font-weight: bold;
            font-size: 7px;
        }
        .total-final {
            margin-top: 5px;
            background-color: #000;
            color: white;
            padding: 6px;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
        }
        .saldo-pendiente {
            background-color: #ffcc00;
            color: #000;
            padding: 4px 6px;
            text-align: right;
            font-size: 9px;
            font-weight: bold;
            margin-top: 3px;
        }
        .saldo-ok {
            background-color: #28a745;
            color: white;
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
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 6.5px;
            font-weight: bold;
        }
        .small {
            font-size: 6px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CHANKAS CAR</h1>
        <p>DETALLE DE PAGO AGRUPADO</p>
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

    <div class="resumen-box">
        <div class="resumen-row">
            <span>Comisiones:</span>
            <span><strong>Bs {{ number_format($totalComision, 2) }}</strong></span>
        </div>
        <div class="resumen-row">
            <span>Pagado:</span>
            <span><strong>Bs {{ number_format($totalPagado, 2) }}</strong></span>
        </div>
        <div class="resumen-row" style="color: {{ $saldoPendiente > 0 ? '#cc0000' : '#28a745' }}; font-weight: bold;">
            <span>Saldo:</span>
            <span><strong>Bs {{ number_format($saldoPendiente, 2) }}</strong></span>
        </div>
    </div>

    @php
        $totalGeneral = 0;
        $totalServiciosGeneral = 0;
    @endphp

    @foreach($serviciosPorFecha as $fecha => $servicios)
        @php
            $totalDia = array_sum(array_column($servicios, 'total_tecnico'));
            $totalServiciosDia = array_sum(array_column($servicios, 'cantidad'));
            $totalGeneral += $totalDia;
            $totalServiciosGeneral += $totalServiciosDia;
        @endphp

        <table>
            <thead>
                <tr>
                    <th colspan="3" class="fecha-header" style="background-color: #555;">
                        <span>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                        <span class="small">{{ $totalServiciosDia }} serv. - Bs {{ number_format($totalDia, 2) }}</span>
                    </th>
                </tr>
                <tr>
                    <th style="width: 60%;">Servicio</th>
                    <th style="width: 20%;" class="text-center">Cant.</th>
                    <th style="width: 20%;" class="text-right">Com. (Bs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicios as $nombreServicio => $datos)
                    <tr>
                        <td><strong>{{ $nombreServicio }}</strong></td>
                        <td class="text-center">
                            <span class="badge">{{ $datos['cantidad'] }}</span>
                        </td>
                        <td class="text-right">{{ number_format($datos['total_tecnico'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="subtotal-row">
                    <td colspan="2" class="text-right">Subtotal:</td>
                    <td class="text-right">{{ number_format($totalDia, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endforeach

    <div class="total-final">
        TOTAL A PAGAR: Bs {{ number_format($totalComision, 2) }}
    </div>

    @if($saldoPendiente > 0)
        <div class="saldo-pendiente">
            SALDO PENDIENTE: Bs {{ number_format($saldoPendiente, 2) }}
        </div>
    @endif

    <div style="margin-top: 6px; text-align: center; font-size: 6px; color: #666;">
        Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} - Chankas Car
    </div>
</body>
</html>
