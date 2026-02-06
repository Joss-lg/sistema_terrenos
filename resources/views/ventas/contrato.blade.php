<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrato de Venta - {{ $venta->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0d2137; }
        .section-title { background: #f4f7f9; padding: 5px; font-weight: bold; text-transform: uppercase; margin-top: 20px; }
        .data-row { margin-bottom: 5px; }
        .label { font-weight: bold; width: 150px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #0d2137; color: white; }
        .footer { margin-top: 50px; text-align: center; }
        .signature { margin-top: 80px; display: inline-block; width: 200px; border-top: 1px solid #000; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONTRATO DE COMPRAVENTA</h1>
        <p>Inmobiliaria Terrenos3 - Folio de Venta: #{{ $venta->id }}</p>
    </div>

    <div class="section-title">Datos del Comprador</div>
    <div class="data-row"><span class="label">Nombre:</span> {{ $venta->cliente->cliente }}</div>
    <div class="data-row"><span class="label">Identificación:</span> {{ $venta->cliente->identificacion }}</div>
    <div class="data-row"><span class="label">Teléfono:</span> {{ $venta->cliente->telefono }}</div>
    <div class="data-row"><span class="label">Dirección:</span> {{ $venta->cliente->direccion }}</div>

    <div class="section-title">Descripción del Terreno</div>
    <div class="data-row"><span class="label">Lote:</span> {{ $venta->terreno->nombre }}</div>
    <div class="data-row"><span class="label">Categoría:</span> {{ $venta->terreno->categoria }}</div>
    <div class="data-row"><span class="label">Ubicación:</span> {{ $venta->terreno->ubicacion }}</div>

    <div class="section-title">Detalles Financieros</div>
    <div class="data-row"><span class="label">Precio Total:</span> ${{ number_format($venta->total, 2) }}</div>
    <div class="data-row"><span class="label">Pago Inicial:</span> ${{ number_format($venta->pago_inicial, 2) }}</div>
    <div class="data-row"><span class="label">Plazo:</span> {{ $venta->mensualidades }} meses</div>
    <div class="data-row"><span class="label">Cuota Mensual:</span> ${{ number_format($venta->monto_mensual, 2) }}</div>
    <div class="data-row"><span class="label">Fecha de Venta:</span> {{ $venta->fecha_compra }}</div>

    <div class="section-title">Calendario de Pagos</div>
    <table>
        <thead>
            <tr>
                <th>N° Pago</th>
                <th>Fecha Vencimiento</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->pagos as $pago)
            <tr>
                <td>Pago {{ $pago->numero_pago }}</td>
                <td>{{ $pago->fecha_vencimiento }}</td>
                <td>${{ number_format($pago->monto, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Este documento sirve como comprobante legal de la operación realizada el día {{ date('d/m/Y') }}.</p>
        <div style="margin-top: 50px;">
            <div class="signature" style="margin-right: 100px;">Firma del Vendedor</div>
            <div class="signature">Firma del Comprador</div>
        </div>
    </div>
</body>
</html>