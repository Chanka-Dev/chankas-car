<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1a3a47;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #1a3a47;
        }
        .header p {
            margin: 5px 0;
            color: #6db3c8;
            font-weight: bold;
        }
        .info-trabajo {
            margin-bottom: 20px;
            background-color: #f5f5f5;
            padding: 15px;
            border-left: 5px solid #fbc02d;
        }
        .info-trabajo p {
            margin: 5px 0;
        }
        .section-title {
            background-color: #1a3a47;
            color: white;
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #6db3c8;
            color: #1a3a47;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .total-box {
            background-color: #1a3a47;
            color: white;
            padding: 20px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .firma-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 50px auto 10px auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CHANKAS CAR</h1>
        <p>GNV - HABILITACIONES VEHICULARES</p>
        <p>WhatsApp: 74106444</p>
    </div>

    <div style="text-align: right; margin-bottom: 20px;">
        <strong>TRABAJO #{{ $trabajo->id_trabajo }}</strong><br>
        <small>Fecha: {{ $trabajo->fecha_trabajo->format('d/m/Y') }}</small>
    </div>

    <div class="info-trabajo">
        <p><strong>Cliente:</strong> {{ $trabajo->cliente ? $trabajo->cliente->placas : 'CLIENTE SIN REGISTRO' }}</p>
        @if($trabajo->cliente && $trabajo->cliente->telefono)
            <p><strong>Teléfono:</strong> {{ $trabajo->cliente->telefono }}</p>
        @endif
        <p><strong>Técnico:</strong> {{ $trabajo->empleado->nombre }} {{ $trabajo->empleado->apellido }}</p>
        <p><strong>Fecha de Trabajo:</strong> {{ $trabajo->fecha_trabajo->format('d/m/Y') }}</p>
        <p><strong>Fecha de Recepción:</strong> {{ $trabajo->fecha_recepcion->format('d/m/Y') }}</p>
        @if($trabajo->fecha_recalificacion)
            <p><strong>Fecha de Recalificación:</strong> {{ $trabajo->fecha_recalificacion->format('d/m/Y') }}</p>
        @endif
    </div>

    <!-- Servicios Realizados -->
    <div class="section-title">
        <i class="fas fa-wrench"></i> SERVICIOS REALIZADOS
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trabajo->trabajoServicios as $ts)
                <tr>
                    <td>{{ $ts->servicio->nombre }}</td>
                    <td class="text-center">{{ $ts->cantidad }}</td>
                    <td class="text-right">Bs {{ number_format($ts->importe_cliente / $ts->cantidad, 2) }}</td>
                    <td class="text-right">Bs {{ number_format($ts->importe_cliente, 2) }}</td>
                </tr>
                @if($ts->observaciones)
                    <tr>
                        <td colspan="4" style="padding-left: 30px; font-size: 10px; color: #666;">
                            <em>Observación: {{ $ts->observaciones }}</em>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Piezas Utilizadas (si hay) -->
    @if($trabajo->trabajoInventarios->count() > 0)
        <div class="section-title">
            <i class="fas fa-cogs"></i> PIEZAS/REPUESTOS UTILIZADOS
        </div>

        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Precio Unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trabajo->trabajoInventarios as $ti)
                    <tr>
                        <td>{{ $ti->inventario->nombre }}</td>
                        <td class="text-center">{{ $ti->cantidad_usada }} {{ $ti->inventario->unidad_medida }}(s)</td>
                        <td class="text-right">Bs {{ number_format($ti->precio_unitario, 2) }}</td>
                        <td class="text-right">Bs {{ number_format($ti->cantidad_usada * $ti->precio_unitario, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Observaciones Generales -->
    @if($trabajo->observaciones)
        <div class="section-title">
            OBSERVACIONES
        </div>
        <p style="padding: 10px; background-color: #f9f9f9;">{{ $trabajo->observaciones }}</p>
    @endif

    <!-- Total -->
    @php
        $totalPiezas = $trabajo->trabajoInventarios->sum(function($ti) {
            return $ti->cantidad_usada * $ti->precio_unitario;
        });
        $totalGeneral = $trabajo->total_cliente + $totalPiezas;
    @endphp

    <table style="margin-top: 20px;">
        <tr>
            <td class="text-right" style="padding: 10px;"><strong>SUBTOTAL SERVICIOS:</strong></td>
            <td class="text-right" style="padding: 10px; width: 150px;"><strong>Bs {{ number_format($trabajo->total_cliente, 2) }}</strong></td>
        </tr>
        @if($totalPiezas > 0)
            <tr>
                <td class="text-right" style="padding: 10px;"><strong>SUBTOTAL PIEZAS:</strong></td>
                <td class="text-right" style="padding: 10px;"><strong>Bs {{ number_format($totalPiezas, 2) }}</strong></td>
            </tr>
        @endif
        <tr>
            <td colspan="2">
                <div class="total-box">
                    TOTAL A PAGAR: Bs {{ number_format($totalGeneral, 2) }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Firma del Cliente -->
    <div style="margin-top: 80px;">
        <div class="firma-line"></div>
        <p class="text-center"><strong>Firma del Cliente</strong></p>
        <p class="text-center" style="font-size: 10px; color: #666;">
            Nombre: _________________________________
        </p>
        <p class="text-center" style="font-size: 10px; color: #666;">
            CI: _________________________________
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>CHANKAS CAR - GNV</strong></p>
        <p>Conversiones a gas natural vehicular y habilitaciones</p>
        <p>WhatsApp: 74106444 | Sucre - Bolivia</p>
        <p style="margin-top: 10px; font-size: 9px;">
            Documento generado el {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>
</body>
</html>