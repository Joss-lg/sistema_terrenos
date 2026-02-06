@extends('layouts.app')

@section('content')
{{-- Estilo para forzar la visibilidad del texto en el select --}}
<style>
    #cliente_id option {
        color: #000000 !important;
        background-color: #ffffff !important;
    }
</style>

<div class="container-fluid" style="max-width: 1200px; padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark"><i class="fas fa-hand-holding-usd me-2"></i>Módulo de Cobranza</h2>
        <span class="badge bg-success px-3 py-2">Caja Activa: ${{ number_format($cajaAbierta->saldo_actual ?? 0, 2) }}</span>
    </div>

    <div class="row g-4">
        {{-- PANEL DE COBRO DINÁMICO --}}
        <div class="col-lg-5">
            <div class="card shadow border-0">
                <div class="card-header text-white" style="background:#158499;">
                    <div class="fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Registrar Pago de Mensualidad</div>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Seleccionar Cliente</label>
                        {{-- Se corrigió el loop para mostrar el nombre correctamente --}}
                        <select id="cliente_id" class="form-select select2" required style="color: #000 !important;">
                            <option value="">-- Buscar Cliente --</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}" data-tel="{{ $c->telefono }}" data-dir="{{ $c->direccion }}">
                                    {{ $c->cliente }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="info-deuda" class="p-3 mb-3 rounded" style="background:#f1f5f9; display:none;">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">Monto Mensual</small>
                                <span class="fw-bold text-primary fs-5" id="lblMontoCuota">$0.00</span>
                            </div>
                            <div class="col-6 border-start">
                                <small class="text-muted d-block">Deuda Pendiente</small>
                                <span class="fw-bold text-danger fs-5" id="lblDeudaTotal">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-3 bg-dark text-white text-center">
                        <small class="opacity-75">TOTAL A COBRAR HOY</small>
                        <h2 class="fw-bold m-0" id="lblTotalCobro">$0.00</h2>
                        <div class="small text-info mt-1" id="lblMultaDetalle">Sin recargos aplicados</div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Mensualidades a pagar</label>
                            <input type="number" id="input-cantidad" class="form-control form-control-lg text-center" value="1" min="1">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Método</label>
                            <select id="metodo_pago" class="form-select form-select-lg">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transf.</option>
                            </select>
                        </div>
                    </div>

                    <button id="btn-procesar-cobro" class="btn btn-success btn-lg w-100 shadow-sm" disabled>
                        <i class="fas fa-check-circle me-1"></i> PROCESAR COBRO
                    </button>
                </div>
            </div>
        </div>

        {{-- HISTORIAL Y ESTADO DE CUENTA --}}
        <div class="col-lg-7">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fas fa-history me-2"></i>Estado de Cuenta / Pagos Recientes
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-hover align-middle mb-0 text-dark">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Vencimiento</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-historial">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Selecciona un cliente para ver su historial</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- MOVIMIENTOS RECIENTES DE CAJA --}}
            <div class="card shadow border-0">
                <div class="card-header bg-secondary text-white small fw-bold">ÚLTIMOS MOVIMIENTOS DE CAJA</div>
                <div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                    @foreach($movimientos as $m)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <small class="d-block fw-bold text-dark">{{ $m->descripcion }}</small>
                                <small class="text-muted">{{ $m->created_at->format('d/m/Y H:i') }} · {{ $m->metodo_pago }}</small>
                            </div>
                            <span class="fw-bold {{ $m->tipo == 'ingreso' ? 'text-success' : 'text-danger' }}">
                                {{ $m->tipo == 'ingreso' ? '+' : '-' }}${{ number_format($m->monto, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let clienteId = null;
    let montoMensual = 0;
    let deudaTotal = 0;

    const selectCliente = document.getElementById('cliente_id');
    const inputCant = document.getElementById('input-cantidad');
    const btnCobrar = document.getElementById('btn-procesar-cobro');

    // 1. Cargar Estado de Cuenta al seleccionar cliente
    selectCliente.addEventListener('change', async function() {
        clienteId = this.value;
        if (!clienteId) {
            resetUI();
            return;
        }

        try {
            const res = await fetch(`/api/ventas/estado-cuenta/${clienteId}`);
            const data = await res.json();

            if (data.success) {
                montoMensual = data.monto_cuota;
                deudaTotal = data.pendiente;
                
                document.getElementById('info-deuda').style.display = 'block';
                document.getElementById('lblMontoCuota').textContent = `$${montoMensual.toLocaleString()}`;
                document.getElementById('lblDeudaTotal').textContent = `$${deudaTotal.toLocaleString()}`;
                
                actualizarTabla(data.historial);
                recalcularTotal();
                btnCobrar.disabled = false;
            } else {
                alert('Este cliente no tiene deudas pendientes.');
                resetUI();
            }
        } catch (e) {
            console.error("Error cargando cuenta");
        }
    });

    function recalcularTotal() {
        const cant = parseInt(inputCant.value) || 0;
        const total = cant * montoMensual;
        document.getElementById('lblTotalCobro').textContent = `$${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
    }

    inputCant.addEventListener('input', recalcularTotal);

    function actualizarTabla(historial) {
        const tbody = document.getElementById('tabla-historial');
        tbody.innerHTML = '';
        historial.forEach(p => {
            const statusClass = p.estado === 'pagado' ? 'bg-success' : 'bg-warning text-dark';
            tbody.innerHTML += `
                <tr>
                    <td>${p.numero_pago}</td>
                    <td>${p.fecha_vencimiento}</td>
                    <td>$${p.monto}</td>
                    <td><span class="badge ${statusClass}">${p.estado.toUpperCase()}</span></td>
                    <td>${p.estado === 'pendiente' ? '<i class="fas fa-clock text-muted"></i>' : '<i class="fas fa-check-double text-success"></i>'}</td>
                </tr>
            `;
        });
    }

    function resetUI() {
        document.getElementById('info-deuda').style.display = 'none';
        document.getElementById('lblTotalCobro').textContent = '$0.00';
        document.getElementById('tabla-historial').innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Selecciona un cliente para ver su historial</td></tr>';
        btnCobrar.disabled = true;
    }

    // 2. Procesar Cobro (AJAX)
    btnCobrar.addEventListener('click', async function() {
        if (!confirm('¿Confirmar registro de pago?')) return;

        const payload = {
            _token: "{{ csrf_token() }}",
            cliente_id: clienteId,
            mensualidades_a_pagar: inputCant.value,
            metodo_pago: document.getElementById('metodo_pago').value
        };

        try {
            const res = await fetch("{{ route('ventas.guardar_cobro') }}", {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                alert('✅ ' + data.message);
                window.location.reload();
            } else {
                alert('❌ Error: ' + data.message);
            }
        } catch (e) {
            alert('⚠️ Error de conexión');
        }
    });
});
</script>
@endsection