<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Cobros - {{ $cliente->cliente }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .info-cliente { margin-bottom: 20px; padding: 10px; background-color: #f8f9fa; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .total { text-align: right; margin-top: 20px; font-size: 18px; }
        .total-monto { font-weight: bold; color: #27ae60; }
    </style>
</head>
<body>

    <div class="header">
        <h2>ESTADO DE CUENTA / HISTORIAL DE COBROS</h2>
        <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-cliente">
        <strong>Cliente:</strong> {{ $cliente->cliente }} <br>
        <strong>ID del Cliente:</strong> {{ $cliente->id }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha de Pago</th>
                <th>Descripción / Concepto</th>
                <th>Monto Abonado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($historialCompleto as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}</td>
                <td>{{ $item->descripcion }}</td>
                <td>${{ number_format($item->monto, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Abonado Acumulado: <span class="total-monto">${{ number_format($totalAbonado, 2) }}</span>
    </div>

</body>
</html>