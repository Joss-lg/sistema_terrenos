<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato de Compraventa - {{ $venta->terreno->nombre }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #0d2137; padding-bottom: 10px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #0d2137; text-transform: uppercase; font-size: 22px; }
        .header p { margin: 5px 0; font-size: 12px; color: #666; }
        
        .section-title { background: #f4f7f9; padding: 5px 10px; font-weight: bold; border-left: 4px solid #0d2137; margin-top: 20px; text-transform: uppercase; font-size: 14px; }
        .content { font-size: 13px; text-align: justify; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; font-size: 12px; }
        table th { background-color: #f8fafc; font-weight: bold; }

        .totals { margin-top: 20px; float: right; width: 40%; }
        .totals table td { border: none; padding: 4px; }
        .totals .grand-total { font-size: 16px; font-weight: bold; color: #0d2137; border-top: 1px solid #0d2137; }

        .signatures { margin-top: 80px; width: 100%; }
        .signature-box { width: 45%; display: inline-block; text-align: center; }
        .signature-line { border-top: 1px solid #333; margin-top: 50px; padding-top: 5px; font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Contrato de Compraventa Inmobiliaria</h1>
        <p>Folio de Venta: #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }} | Fecha: {{ \Carbon\Carbon::parse($venta->fecha_compra)->format('d/m/Y') }}</p>
    </div>

    <div class="content">
        <p>En Valle de Chalco, Estado de México, se celebra el presente contrato entre la Inmobiliaria y el Cliente descrito a continuación:</p>

        <div class="section-title">Datos del Adquirente (Cliente)</div>
        <table>
            <tr>
                <th>Nombre Completo:</th>
                <td>{{ $venta->cliente->cliente }}</td>
            </tr>
            <tr>
                <th>Identificación:</th>
                <td>{{ $venta->cliente->identificacion ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Dirección:</th>
                <td>{{ $venta->cliente->direccion ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Teléfono:</th>
                <td>{{ $venta->cliente->telefono }}</td>
            </tr>
        </table>

        <div class="section-title">Detalles del Inmueble (Terreno)</div>
        <table>
            <tr>
                <th>Nombre del Lote:</th>
                <td>{{ $venta->terreno->nombre }}</td>
                <th>Categoría:</th>
                <td>{{ strtoupper($venta->terreno->categoria ?? 'BASICO') }}</td>
            </tr>
            <tr>
                <th>Ubicación:</th>
                <td colspan="3">{{ $venta->terreno->ubicacion ?? 'Valle de Chalco' }}</td>
            </tr>
        </table>

        <div class="section-title">Plan de Financiamiento</div>
        <table>
            <tr>
                <th>Precio Total:</th>
                <td>${{ number_format($venta->total, 2) }}</td>
            </tr>
            <tr>
                <th>Enganche Recibido:</th>
                <td>${{ number_format($venta->pago_inicial, 2) }}</td>
            </tr>
            <tr>
                <th>Plazo Elegido:</th>
                <td>{{ $venta->mensualidades }} Meses</td>
            </tr>
            <tr>
                <th>Monto Mensual:</th>
                <td><strong>${{ number_format($venta->monto_mensual, 2) }}</strong></td>
            </tr>
            <tr>
                <th>Método de Pago:</th>
                <td>{{ ucfirst($venta->metodo_pago) }}</td>
            </tr>
        </table>

        @if($venta->detalles)
            <div class="section-title">Observaciones Adicionales</div>
            <p style="font-size: 12px;">{{ $venta->detalles }}</p>
        @endif

        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">POR LA INMOBILIARIA</div>
            </div>
            <div class="signature-box" style="float: right;">
                <div class="signature-line">EL CLIENTE</div>
                <small>{{ $venta->cliente->cliente }}</small>
            </div>
        </div>
    </div>

    <div class="footer">
        Este documento es un comprobante de venta. Los términos legales se rigen bajo las leyes vigentes del Estado de México.
    </div>

</body>
</html>