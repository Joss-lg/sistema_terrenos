@extends('layouts.app') 

@section('content')
<div class="container-fluid p-4">
    
    <div class="mb-4 p-4 shadow-sm" style="background-color: #0f172a; border-radius: 10px;">
        <div class="fw-bold text-uppercase" style="font-size: 0.75rem; color: #94a3b8; letter-spacing: 1px; margin-bottom: 4px;">
            INMOBILIARIA • COBRANZA
        </div>
        <h2 class="fw-bold text-white mb-0">
            Historial de Pagos
        </h2>
    </div>
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Cliente</th>
                            <th>Descripción</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $pago->cliente->cliente ?? 'N/A' }}</td>
                            <td class="text-muted">{{ $pago->descripcion }}</td>
                            <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</td>
                            <td class="text-success fw-bold">${{ number_format($pago->monto, 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('historial.pdf', $pago->id) }}" target="_blank" class="btn btn-danger btn-sm" title="Descargar Historial Completo">
                                    <i class="fas fa-file-pdf me-1"></i> Estado de Cuenta
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No se encontraron pagos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection