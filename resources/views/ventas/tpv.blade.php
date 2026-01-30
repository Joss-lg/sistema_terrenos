@extends('layouts.app')

@section('content')
<style>
    /* ... Tus estilos se mantienen igual ... */
    .tpv-container { background-color: #f4f7f9; color: #334155 !important; min-height: calc(100vh - 76px); padding: 20px; overflow-y: auto; }
    .ventas-header-new { background-color: #0d2137; color: white; padding: 20px; border-radius: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .card-dark { background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .card-total-destacada { background-color: #0f172a; color: white !important; border-radius: 15px; padding: 25px; text-align: center; border: 5px solid #ffffff; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2); }
    .label-accent, label { color: #1e293b !important; font-weight: bold !important; }
    .text-highlight { color: #0ea5e9 !important; font-weight: bold !important; } 
    .form-control-dark { background-color: #ffffff !important; color: #1e293b !important; border: 1px solid #cbd5e1 !important; }
    .btn-procesar-tpv { background-color: #158499; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; transition: background 0.2s; }
    .btn-procesar-tpv:hover { background-color: #0e6677; color: white; }
    .btn-procesar-tpv:disabled { opacity: 0.5; cursor: not-allowed; background-color: #64748b; }
    .product-card { transition: all 0.3s; background-color: #f8fafc; border: 1px solid #e2e8f0; height: 100%; cursor: pointer; }
    .product-card:hover { transform: translateY(-5px); background-color: #0ea5e9 !important; border-color: #0ea5e9; }
    .product-card:hover * { color: #ffffff !important; }
    .cliente-resumen-card { background: #f1f5f9; border-left: 5px solid #0ea5e9; padding: 15px; border-radius: 8px; margin-top: 10px; }
    .mensualidad-row { border-bottom: 1px solid #e2e8f0 !important; transition: background 0.2s; cursor: pointer; }
    .mensualidad-row:has(input:checked) { background-color: rgba(14, 165, 233, 0.1); border-left: 4px solid #0ea5e9 !important; }
</style>

<div class="tpv-container">
    @if ($cajaAbierta) 
        <div class="ventas-header-new">
            <div>
                <p style="margin: 0; font-size: 0.8rem; opacity: 0.8; text-transform: uppercase; color: white !important;">Inmobiliaria • Ventas</p>
                <h2 class="m-0" style="color: white !important; font-weight: bold;">Módulo de Ventas</h2>
            </div>
            <span class="badge bg-primary px-3 py-2" style="border-radius: 20px;">+ Nuevo</span>
        </div>

        <div class="row">
            {{-- BLOQUE 1: CLIENTE Y LOTE --}}
            <div class="col-lg-4">
                <div class="card card-dark p-3 shadow">
                    <label class="label-accent mb-2">1. CLIENTE</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary"><i class="fas fa-user text-primary"></i></span>
                        <input type="text" id="temporal-client-name" class="form-control form-control-dark" placeholder="Buscar cliente..." readonly>
                        <button class="btn btn-info dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"></button>
                        <ul class="dropdown-menu w-100 shadow-lg" style="max-height: 250px; overflow-y: auto;">
                            @foreach($clientes as $c)
                                <li><a class="dropdown-item select-client" href="#" data-id="{{ $c->idCli }}" data-nombre="{{ $c->cliente }}" data-tel="{{ $c->telefono }}" data-correo="{{ $c->correo }}" data-identificacion="{{ $c->identificacion }}" data-direccion="{{ $c->direccion }}">{{ $c->cliente }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div id="resumen-cliente" class="cliente-resumen-card" style="display:none;">
                        <h5 id="res-nombre" class="text-dark fw-bold mb-2">---</h5>
                        <p class="mb-1 small">Identificación: <span id="res-identificacion" class="text-highlight">---</span></p>
                        <p class="mb-1 small">Dirección: <span id="res-direccion">---</span></p>
                        <p class="mb-1 small">Tel: <span id="res-tel" class="text-highlight">---</span></p>
                    </div>
                </div>

                <div class="card card-dark p-3 shadow">
                    <label class="label-accent mb-2">2. SELECCIÓN DE LOTE</label>
                    <div class="row g-2 overflow-auto" id="product-list" style="max-height: 250px;">
                        @foreach ($terrenos as $t)
                            <div class="col-6 mb-2">
                                <div class="card product-card" data-id="{{ $t->id }}" data-price="{{ $t->precio_total }}">
                                    <div class="card-body p-2 text-center">
                                        <small class="text-highlight d-block fw-bold" style="font-size: 0.75rem; text-transform: uppercase;">
                                            {{ $t->categoria ?? 'BASICO' }}
                                        </small>
                                        <i class="fas fa-mountain text-info mb-1"></i>
                                        <p class="mb-0 fw-bold small text-dark">{{ $t->nombre }}</p>
                                        <span class="text-highlight small">${{ number_format($t->precio_total, 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- BLOQUE 2: FINANCIAMIENTO --}}
            <div class="col-lg-4">
                <div class="card card-dark p-3 h-100 shadow">
                    <label class="label-accent mb-2">3. PLAN DE PAGO</label>
                    <div class="mb-3">
                        <label class="small">Terreno</label>
                        <select class="form-select form-control-dark" id="terreno_id">
                            <option value="">-- Elija un lote --</option>
                            @foreach($terrenos as $t)
                                <option value="{{ $t->id }}" data-precio="{{ $t->precio_total }}">
                                    {{ strtoupper($t->categoria ?? 'BASICO') }} - {{ $t->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small">Enganche</label>
                        <input type="number" id="pago_inicial" class="form-control form-control-dark text-highlight fw-bold" value="5000" min="0" step="1">
                    </div>
                    <label class="small mb-2">Plazo:</label>
                    <div class="rounded border border-light overflow-hidden mb-3 bg-light">
                        @foreach([12, 24, 36, 48, 60] as $m)
                            <label class="d-flex justify-content-between p-2 mensualidad-row mb-0">
                                <span><input type="radio" name="mensualidades" value="{{ $m }}"> {{ $m }} Meses</span>
                                <span class="text-highlight small" id="label-monto-{{ $m }}">$0.00</span>
                            </label>
                        @endforeach
                    </div>
                    <textarea id="detalles" class="form-control form-control-dark" rows="3" placeholder="Notas y observaciones..."></textarea>
                </div>
            </div>

            {{-- BLOQUE 3: TOTAL DESTACADO --}}
            <div class="col-lg-4">
                <div class="card-total-destacada shadow mb-3">
                    <label class="label-accent mb-2" style="color: white !important;">TOTAL VENTA</label>
                    <h1 id="total" class="fw-bold mb-4" style="font-size: 3.5rem; color: white !important;">$0.00</h1>
                    <button id="process-payment" class="btn btn-procesar-tpv btn-lg w-100 shadow-lg" disabled>PROCESAR VENTA</button>
                    <button id="btn-toggle-pagos" class="btn btn-outline-light btn-sm w-100 mt-3" style="border-color: #0ea5e9; color: #0ea5e9;">Ver Calendario</button>
                </div>

                <div id="panel-pagos" class="card shadow mt-2" style="display:none; max-height: 400px; overflow-y: auto; background-color: #ffffff !important; border: 2px solid #0ea5e9; border-radius: 12px;">
                    <div class="p-3">
                        <h6 class="fw-bold text-dark border-bottom pb-2">Calendario de Pagos Proyectado</h6>
                        <div id="tabla-pagos-preview" class="text-dark"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- MODAL --}}
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #0f172a; border: 1px solid #38bdf8;">
            <div class="modal-header border-secondary"><h5 class="modal-title text-highlight text-white">Confirmar Venta</h5></div>
            <div class="modal-body text-center">
                <p class="text-white">Enganche a recibir:</p>
                <h1 class="text-highlight" id="modal-pago-display">$0.00</h1>
                <select class="form-select form-control-dark mt-3" id="modal-metodo" style="background-color: #1d2d50 !important; color: white !important;">
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                </select>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-info fw-bold text-white" id="confirm-final">Finalizar Venta</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let clienteId = null; 
    let precioT = 0;
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));

    // Selección Cliente
    document.querySelectorAll('.select-client').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            clienteId = item.dataset.id;
            document.getElementById('temporal-client-name').value = item.dataset.nombre;
            document.getElementById('res-nombre').textContent = item.dataset.nombre;
            document.getElementById('res-identificacion').textContent = item.dataset.identificacion || 'N/A';
            document.getElementById('res-direccion').textContent = item.dataset.direccion || 'N/A';
            document.getElementById('res-tel').textContent = item.dataset.tel || 'N/A';
            document.getElementById('resumen-cliente').style.display = 'block';
            validar();
        });
    });

    // Selección Terreno Card CORREGIDO
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', () => {
            const terrenoId = card.dataset.id;
            const selectTerreno = document.getElementById('terreno_id');
            selectTerreno.value = terrenoId;
            
            // Disparamos el evento change para que calc() y validar() se ejecuten
            selectTerreno.dispatchEvent(new Event('change'));
        });
    });

    function calc() {
        const sel = document.getElementById('terreno_id');
        const selectedOption = sel.selectedOptions[0];
        
        precioT = parseFloat(selectedOption?.dataset.precio || 0);
        let eng = parseFloat(document.getElementById('pago_inicial').value) || 0;

        if(eng < 0) { eng = 0; document.getElementById('pago_inicial').value = 0; }

        [12, 24, 36, 48, 60].forEach(m => {
            const res = precioT > 0 ? (precioT - eng) / m : 0;
            document.getElementById(`label-monto-${m}`).textContent = `$${res.toLocaleString('en-US',{minimumFractionDigits:2})}`;
        });
        
        document.getElementById('total').textContent = `$${precioT.toLocaleString()}`;
        validar();
    }

    // Función validar CORREGIDA
    function validar() {
        const btn = document.getElementById('process-payment');
        const radio = document.querySelector('input[name="mensualidades"]:checked');
        
        // Habilitar solo si hay Cliente, Terreno y se seleccionó un Plazo
        if (clienteId && precioT > 0 && radio) {
            btn.classList.remove('disabled');
            btn.disabled = false;
        } else {
            btn.classList.add('disabled');
            btn.disabled = true;
        }
    }

    // Event Listeners
    document.getElementById('terreno_id').addEventListener('change', calc);
    document.getElementById('pago_inicial').addEventListener('input', calc);
    
    // Al cambiar radio button, validar de nuevo
    document.getElementsByName('mensualidades').forEach(r => {
        r.addEventListener('change', () => {
            validar();
        });
    });

    document.getElementById('process-payment').addEventListener('click', function() {
        const radio = document.querySelector('input[name="mensualidades"]:checked');
        if(!radio) {
            alert('Por favor, selecciona un plazo de meses.');
            return;
        }
        document.getElementById('modal-pago-display').textContent = `$${parseFloat(document.getElementById('pago_inicial').value).toLocaleString()}`;
        modal.show();
    });

    document.getElementById('confirm-final').addEventListener('click', async () => {
        const radio = document.querySelector('input[name="mensualidades"]:checked');
        const payload = {
            _token: "{{ csrf_token() }}",
            cliente_id: clienteId,
            terreno_id: document.getElementById('terreno_id').value,
            pago_inicial: document.getElementById('pago_inicial').value,
            mensualidades: radio ? radio.value : 0,
            total: precioT,
            fecha_compra: new Date().toISOString().split('T')[0],
            metodo_pago: document.getElementById('modal-metodo').value,
            detalles: document.getElementById('detalles').value
        };

        try {
            const res = await fetch("{{ route('ventas.store') }}", {
                method: 'POST', 
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if(data.success) {
                if(confirm('¡Venta registrada! ¿Deseas abrir el contrato en PDF?')) {
                    window.open("{{ url('ventas/contrato') }}/" + data.id, '_blank');
                }
                window.location.reload(); 
            } else { alert('Error: ' + data.message); }
        } catch (error) { alert('Error de comunicación'); }
    });

    // Calendario proyectado
    document.getElementById('btn-toggle-pagos').addEventListener('click', () => {
        const p = document.getElementById('panel-pagos');
        const tablaPreview = document.getElementById('tabla-pagos-preview');
        
        if(precioT > 0) {
            const radio = document.querySelector('input[name="mensualidades"]:checked');
            const meses = radio ? parseInt(radio.value) : 0;
            const enganche = parseFloat(document.getElementById('pago_inicial').value) || 0;
            
            if (meses > 0) {
                let deudaFinanciada = precioT - enganche; 
                let cuota = deudaFinanciada / meses;
                let saldoRestante = deudaFinanciada;
                
                let tablaHTML = `
                    <table class="table table-sm mt-2" style="color: #1e293b !important;">
                        <thead style="border-bottom: 2px solid #0ea5e9;">
                            <tr>
                                <th>N° Pago</th>
                                <th>Cuota</th>
                                <th>Saldo Pendiente</th>
                            </tr>
                        </thead>
                        <tbody>`;
                
                for (let i = 1; i <= meses; i++) {
                    saldoRestante -= cuota;
                    if (i === meses) saldoRestante = 0;

                    tablaHTML += `
                        <tr>
                            <td>Pago ${i}</td>
                            <td class="fw-bold text-primary">$${cuota.toLocaleString('en-US',{minimumFractionDigits:2})}</td>
                            <td class="text-danger fw-bold">$${Math.abs(saldoRestante).toLocaleString('en-US',{minimumFractionDigits:2})}</td>
                        </tr>`;
                }
                
                tablaHTML += `</tbody></table>`;
                tablaPreview.innerHTML = tablaHTML;
            } else {
                tablaPreview.innerHTML = '<div class="alert alert-warning p-2 small">⚠️ Selecciona un plazo (Meses) primero.</div>';
            }
            p.style.display = p.style.display === 'none' ? 'block' : 'none';
        } else {
            alert("Primero selecciona un terreno para ver su calendario.");
        }
    });
});
</script>
@endsection