@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm mb-4" style="background: #0f172a; border-radius: 12px;">
    <div class="card-body p-4">
        <small class="text-uppercase fw-bold" style="color: #94a3b8; letter-spacing: 1px; font-size: 0.75rem;">
            INMOBILIARIA • COBRANZA
        </small>
        <h2 class="text-white fw-bold mb-0 mt-1">Módulo de Cobranza</h2>
    </div>
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

                    <div class="d-flex gap-2">
                        <button id="btn-procesar-cobro" class="btn btn-success btn-lg w-100 shadow-sm" disabled>
                            <i class="fas fa-check-circle me-1"></i> COBRAR
                        </button>
                        <button id="btn-liquidar-cuenta" class="btn btn-warning btn-lg w-100 shadow-sm text-dark fw-bold" disabled>
                            <i class="fas fa-star me-1"></i> LIQUIDAR
                        </button>
                    </div>
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

           
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let clienteId = null;
    let montoMensual = 0;
    let deudaTotal = 0;
    let pagosPendientesGlobal = [];

    const selectCliente = document.getElementById('cliente_id');
    const inputCant = document.getElementById('input-cantidad');
    const btnCobrar = document.getElementById('btn-procesar-cobro');
    const btnLiquidar = document.getElementById('btn-liquidar-cuenta');

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
                pagosPendientesGlobal = data.historial.filter(p => p.estado === 'pendiente');
                
                document.getElementById('info-deuda').style.display = 'block';
                document.getElementById('lblMontoCuota').textContent = `$${montoMensual.toLocaleString()}`;
                document.getElementById('lblDeudaTotal').textContent = `$${deudaTotal.toLocaleString()}`;
                
                actualizarTabla(data.historial);
                recalcularTotal();
                
                btnCobrar.disabled = false;
                btnLiquidar.disabled = (pagosPendientesGlobal.length === 0);
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
        let totalBase = 0;
        let totalMultas = 0;
        
        const hoy = new Date();
        hoy.setHours(0,0,0,0); 

        for(let i=0; i < cant; i++) {
            if(pagosPendientesGlobal[i]) {
                let cuota = pagosPendientesGlobal[i];
                let monto = parseFloat(cuota.monto);
                totalBase += monto;
                
                let fechaVencimiento = new Date(cuota.fecha_vencimiento + "T00:00:00");
                if(hoy > fechaVencimiento) {
                    totalMultas += (monto * 0.10);
                }
            }
        }
        
        let granTotal = totalBase + totalMultas;
        document.getElementById('lblTotalCobro').textContent = `$${granTotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        
        const lblMulta = document.getElementById('lblMultaDetalle');
        if(totalMultas > 0) {
            lblMulta.textContent = `Incluye $${totalMultas.toLocaleString(undefined, {minimumFractionDigits: 2})} de recargos (10%)`;
            lblMulta.className = "small text-warning mt-1 fw-bold";
        } else {
            lblMulta.textContent = 'Sin recargos aplicados';
            lblMulta.className = "small text-info mt-1";
        }
    }

    inputCant.addEventListener('input', recalcularTotal);

    function actualizarTabla(historial) {
        const tbody = document.getElementById('tabla-historial');
        tbody.innerHTML = '';
        const hoy = new Date();
        hoy.setHours(0,0,0,0);

        historial.forEach(p => {
            const statusClass = p.estado === 'pagado' ? 'bg-success' : 'bg-warning text-dark';
            let botonAccion = '';
            let advertenciaVencido = '';

            if (p.estado === 'pendiente') {
                let fechaVenc = new Date(p.fecha_vencimiento + "T00:00:00");
                if (hoy > fechaVenc) advertenciaVencido = '<br><small class="text-danger fw-bold">VENCIDO</small>';
                botonAccion = '<i class="fas fa-clock text-muted" title="Pendiente de pago"></i>';
            } else {
                botonAccion = `<a href="/cajas/ticket/${p.id}" target="_blank" class="btn btn-sm btn-primary" title="Imprimir Ticket">
                                  <i class="fas fa-print"></i> Ticket
                               </a>`;
            }

            tbody.innerHTML += `
                <tr>
                    <td>${p.numero_pago}</td>
                    <td>${p.fecha_vencimiento} ${advertenciaVencido}</td>
                    <td>$${p.monto}</td>
                    <td><span class="badge ${statusClass}">${p.estado.toUpperCase()}</span></td>
                    <td>${botonAccion}</td>
                </tr>
            `;
        });
    }

    function resetUI() {
        document.getElementById('info-deuda').style.display = 'none';
        document.getElementById('lblTotalCobro').textContent = '$0.00';
        document.getElementById('lblMultaDetalle').textContent = 'Sin recargos aplicados';
        document.getElementById('lblMultaDetalle').className = 'small text-info mt-1';
        document.getElementById('tabla-historial').innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Selecciona un cliente para ver su historial</td></tr>';
        btnCobrar.disabled = true;
        btnLiquidar.disabled = true;
    }

    async function ejecutarPago(cantidadAPagar) {
        const payload = {
            _token: "{{ csrf_token() }}",
            cliente_id: clienteId,
            mensualidades_a_pagar: cantidadAPagar,
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
                // 1. PRIMERO ABRIMOS EL TICKET
                if(data.pago_id) {
                    let ticket = window.open(`/cajas/ticket/${data.pago_id}?cuotas=${data.cuotas_pagadas}&recargos=${data.total_recargos}`, '_blank');
                    
                    if(!ticket || ticket.closed || typeof ticket.closed == 'undefined') {
                        alert('⚠️ El cobro fue exitoso, pero tu navegador bloqueó el ticket.\n\nPor favor, mira arriba a la derecha en la barra de direcciones y selecciona "Permitir siempre ventanas emergentes" para este sitio.');
                    }
                }

                // 2. LUEGO MOSTRAMOS EL MENSAJE Y RECARGAMOS
                alert('✅ ' + data.message);
                window.location.reload();

            } else {
                alert('❌ Error: ' + data.message);
            }
        } catch (e) {
            alert('⚠️ Error de conexión');
        }
    }

    btnCobrar.addEventListener('click', function() {
        if (!confirm('¿Confirmar registro de pago?')) return;
        ejecutarPago(inputCant.value);
    });

    btnLiquidar.addEventListener('click', function() {
        let textTotal = document.getElementById('lblTotalCobro').textContent;
        if (!confirm(`¿Estás seguro de liquidar la cuenta completa?`)) return;
        ejecutarPago(pagosPendientesGlobal.length);
    });
});
</script>
@endsection