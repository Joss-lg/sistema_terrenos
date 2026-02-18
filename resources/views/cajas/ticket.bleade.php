<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Courier', sans-serif; font-size: 12px; width: 80mm; margin: 0; }
        .text-center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .total { font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="text-center">
        <h3>INMOBILIARIA - ERP</h3>
        <p>Recibo de Pago Mensual</p>
    </div>

    <div class="divider"></div>

    <p><strong>Cliente:</strong> {{ $cliente->Nombre }}</p>
    <p><strong>Tel:</strong> {{ $cliente->telefono }}</p>
    <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>

    <div class="divider"></div>

    <p>Concepto: Mensualidad ({{ $meses }} meses)</p>
    <p>Monto Base: ${{ number_format($subtotal, 2) }}</p>
    <p>Recargo (10%): ${{ number_format($multa, 2) }}</p>

    <div class="divider"></div>

    <div class="text-center">
        <span class="total">TOTAL: ${{ number_format($total, 2) }}</span>
        <p>Método: {{ ucfirst($metodo) }}</p>
    </div>

    <div class="divider"></div>

    <p class="text-center">
        <strong>SALDO RESTANTE: ${{ number_format($saldo_restante, 2) }}</strong>
    </p>

    <p class="text-center" style="margin-top: 20px;">¡Gracias por su pago!</p>
</body>
</html>