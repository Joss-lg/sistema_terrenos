<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Abono</title>
    <style>
        @page { margin: 0px; }
        body {
            font-family: 'Courier New', Courier, monospace; 
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 15px;
        }
        .ticket-container {
            width: 100%;
            max-width: 280px; 
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .text-danger { color: #d9534f; }
        
        .divisor {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }
        
        .header h2 { font-size: 16px; margin: 0 0 5px 0; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11px; }

        .tabla-info { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .tabla-info td { padding: 2px 0; vertical-align: top; }
        .col-izq { width: 45%; text-align: left; }
        .col-der { width: 55%; text-align: right; }

        .totales-box { margin-top: 10px; }
        .total-destacado { font-size: 14px; font-weight: bold; }
        .footer { margin-top: 15px; font-size: 10px; }
    </style>
</head>
<body>

<div class="ticket-container">
    <div class="header text-center">
        <h2>TERRENOS S.A.</h2>
        <p>Comprobante de Ingreso</p>
        <p>Fecha: {{ $pago->updated_at->timezone('America/Mexico_City')->format('d/m/Y H:i') }}</p>
        <div class="divisor"></div>
    </div>

    <table class="tabla-info">
        <tr>
            <td class="col-izq">Ticket #:</td>
            <td class="col-der bold">{{ str_pad($pago->id, 6, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td class="col-izq">Cliente:</td>
            <td class="col-der bold">{{ $cliente->cliente }}</td>
        </tr>
        <tr>
            <td class="col-izq">Lote/Terreno:</td>
            <td class="col-der">{{ $venta->terreno_id }}</td>
        </tr>
        <tr>
            <td class="col-izq">Cuota(s):</td>
            <td class="col-der">
                @if($cuotas_pagadas > 1)
                    {{ $cuotas_pagadas }} Mensualidades
                @else
                    {{ $pago->numero_pago }} de {{ $venta->mensualidades }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="col-izq">Método:</td>
            <td class="col-der" style="text-transform: uppercase;">{{ $venta->metodo_pago }}</td>
        </tr>
    </table>

    <div class="divisor"></div>

    <div class="totales-box">
        <table class="tabla-info">
            @if(isset($total_recargos) && $total_recargos > 0)
            <tr>
                <td class="col-izq text-danger">Recargos (10%):</td>
                <td class="col-der text-danger">${{ number_format($total_recargos, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="col-izq total-destacado">ABONO TOTAL:</td>
                <td class="col-der total-destacado">${{ number_format($abonoTotal, 2) }}</td>
            </tr>
            <tr><td colspan="2"><div style="height: 5px;"></div></td></tr>
            <tr>
                <td class="col-izq" style="font-size: 11px;">Costo Terreno:</td>
                <td class="col-der" style="font-size: 11px;">${{ number_format($venta->total, 2) }}</td>
            </tr>
            <tr>
                <td class="col-izq" style="font-size: 11px;">Saldo Restante:</td>
                <td class="col-der bold" style="font-size: 11px;">${{ number_format($saldoRestante, 2) }}</td>
            </tr>
        </table>
    </div>
    
    <div class="divisor"></div>
    
    <div class="footer text-center">
        <p>¡Gracias por su pago!</p>
        <p>Conserve este recibo para cualquier aclaración.</p>
        <p>*** COPIA CLIENTE ***</p>
    </div>
</div>

</body>
</html>