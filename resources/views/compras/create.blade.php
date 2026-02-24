@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Card centrado --}}
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 600px;">
        
        {{-- Cabecera oscura y h4 para el título --}}
        <div class="card-header bg-dark text-white border-0">
            <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Registrar Nuevo Pago</h4>
        </div>

        {{-- card-body con p-4 y sin alerta de sesión --}}
        <div class="card-body p-4">

            <form action="{{ route('pagos.store') }}" method="POST">
                @csrf

                <h6 class="text-muted">Detalles del Pago</h6>
                <hr class="mt-1 mb-3 border-secondary">

                {{-- Cliente --}}
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user fa-fw"></i></span>
                        <select class="form-select @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required>
                            <option value="">Seleccione un Cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} {{-- Muestra el nombre del cliente --}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('cliente_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                
                {{-- Total del Pago --}}
                <div class="mb-3">
                    <label for="total" class="form-label">Monto del Pago</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0.01" class="form-control @error('total') is-invalid @enderror" id="total" name="total" value="{{ old('total') }}" required>
                    </div>
                    @error('total') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- Método de Pago --}}
                <div class="mb-3">
                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-credit-card fa-fw"></i></span>
                        <select class="form-select @error('metodo_pago') is-invalid @enderror" id="metodo_pago" name="metodo_pago" required>
                            <option value="">Seleccione...</option>
                            @foreach (['efectivo', 'tarjeta', 'transferencia'] as $metodo)
                                <option value="{{ $metodo }}" {{ old('metodo_pago') == $metodo ? 'selected' : '' }}>
                                    {{ ucfirst($metodo) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('metodo_pago') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- Descripción --}}
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción o Lote (Opcional)</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    
                    @if (Auth::user()->hasPermissionTo('pagos', 'alta'))
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Guardar Pago
                        </button>
                    @else
                        <span class="text-danger">No tienes permiso para registrar pagos.</span>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection