@extends('layouts.app')

@section('content')
<style>
    .tpv-container { background-color: #f4f7f9; color: #334155 !important; min-height: calc(100vh - 76px); padding: 20px; overflow-y: auto; }
    .ventas-header-new { background-color: #0d2137; color: white; padding: 20px; border-radius: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .card-dark { background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .card-total-destacada { background-color: #0f172a; color: white !important; border-radius: 15px; padding: 25px; text-align: center; border: 5px solid #ffffff; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2); }
    .label-accent, label { color: #1e293b !important; font-weight: bold !important; }
    .text-highlight { color: #0ea5e9 !important; font-weight: bold !important; } 
    .form-control-dark { background-color: #ffffff !important; color: #1e293b !important; border: 1px solid #cbd5e1 !important; }
    .btn-procesar-tpv { background-color: #158499; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; transition: background 0.2s; }
    .btn-procesar-tpv:hover { background-color: #0e6677; color: white; }
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
        </div>

        <div class="row">
            {{-- BLOQUE 1: CLIENTE Y CATEGORÍA --}}
            <div class="col-lg-4">
                <div class="card card-dark p-3 shadow">
                    <label class="label-accent mb-2">1. SELECCIONAR CLIENTE</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary"><i class="fas fa-user text-primary"></i></span>
                        <input type="text" id="temporal-client-name" class="form-control form-control-dark" placeholder="Buscar cliente..." readonly>
                        <button class="btn btn-info dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"></button>
                        <ul class="dropdown-menu w-100 shadow-lg" style="max-height: 250px; overflow-y: auto;">
                            @foreach($clientes as $c)
                                <li>
                                    <a class="dropdown-item select-client" href="#" 
                                       data-id="{{ $c->id }}" 
                                       data-nombre="{{ $c->cliente }}" 
                                       data-tel="{{ $c->telefono }}" 
                                       data-identificacion="{{ $c->identificacion }}" 
                                       data-direccion="{{ $c->direccion }}">
                                       {{ $c->cliente }}
                                    </a>
                                </li>
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
                    <label class="label-accent mb-2">2. FILTRAR POR TIPO</label>
                    <select id="filtro-categoria" class="form-select mb-3 form-control-dark">
                        <option value="todos">Mostrar Todos</option>
                        <option value="BASICO">Básico</option>
                        <option value="MEDIO">Medio</option>
                        <option value="PREMIUM">Premium</option>
                    </select>
                    <div class="row g-2 overflow-auto" id="product-list" style="max-height: 280px;">
                        @foreach ($terrenos as $t)
                            <div class="col-6 mb-2 terrain-item" data-cat="{{ strtoupper($t->categoria ?? 'BASICO') }}">
                                <div class="card product-card" data-id="{{ $t->id }}" data-price="{{ $t->precio_total }}" data-name="{{ $t->nombre }}">
                                    <div class="card-body p-2 text-center">
                                        <small class="text-highlight d-block fw-bold" style="font-size: 0.65rem;">{{ strtoupper($t->categoria ?? 'BASICO') }}</small>
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
                    <label class="label-accent mb-2">3. PLAN DE PAGO Y FINANCIAMIENTO</label>
                    <div class="mb-3 bg-light p-2 rounded">
                        <label class="small">Lote Seleccionado:</label>
                        <h6 id="lote-seleccionado-nombre" class="fw-bold text-primary">Ninguno</h6>
                        <input type="hidden" id="terreno_id_input">
                    </div>
                    
                    <div class="mb-3">
                        <label class="small">Pago Inicial (Enganche):</label>
                        <input type="number" id="pago_inicial" class="form-control form-control-dark text-highlight fw-bold" value="5000">
                    </div>

                    <label class="small mb-2">Elegir Plazo (Meses):</label>
                    <div class="rounded border border-light overflow-hidden mb-3 bg-light">
                        @foreach([12, 24, 36, 48, 60] as $m)
                            <label class="d-flex justify-content-between p-2 mensualidad-row mb-0">
                                <span><input type="radio" name="mensualidades" value="{{ $m }}"> {{ $m }} Meses</span>
                                <span class="text-highlight small label-monto-cuota" id="label-monto-{{ $m }}">$0.00</span>
                            </label>
                        @endforeach
                    </div>

                    <label class="small">Detalles de la operación:</label>
                    <textarea id="detalles" class="form-control form-control-dark" rows="3" placeholder="Ej. Promoción especial, cliente referido..."></textarea>
                </div>
            </div>

            {{-- BLOQUE 3: TOTALES --}}
            <div class="col-lg-4">
                <div class="card-total-destacada shadow mb-3">
                    <label class="label-accent mb-2" style="color: white !important;">PRECIO TOTAL</label>
                    <h1 id="total-display" class="fw-bold mb-4" style="font-size: 3rem; color: white !important;">$0.00</h1>
                    
                    <div class="d-flex justify-content-between mb-3 px-3">
                        <small>Financiado:</small>
                        <small id="monto-a-financiar" class="fw-bold text-info">$0.00</small>
                    </div>

                    <button id="process-payment" class="btn btn-procesar-tpv btn-lg w-100 shadow-lg" disabled>
                        REGISTRAR VENTA
                    </button>
                    <button id="btn-toggle-pagos" class="btn btn-outline-light btn-sm w-100 mt-3">Ver Tabla de Amortización</button>
                </div>

                <div id="panel-pagos" class="card shadow mt-2" style="display:none; max-height: 350px; overflow-y: auto;">
                    <div class="p-3">
                        <h6 class="fw-bold text-dark border-bottom pb-2">Calendario Proyectado</h6>
                        <div id="tabla-pagos-preview" class="small text-dark"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let clienteId = null; 
    let precioTerreno = 0;
    let terrenoSeleccionadoId = null;

    // 1. Filtro de Categorías
    document.getElementById('filtro-categoria').addEventListener('change', function() {
        const cat = this.value;
        document.querySelectorAll('.terrain-item').forEach(item => {
            if (cat === 'todos' || item.dataset.cat === cat) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // 2. Selección de Cliente
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

    // 3. Selección de Terreno
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', () => {
            precioTerreno = parseFloat(card.dataset.price);
            terrenoSeleccionadoId = card.dataset.id;
            
            document.getElementById('lote-seleccionado-nombre').textContent = card.dataset.name;
            document.getElementById('total-display').textContent = `$${precioTerreno.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            document.getElementById('terreno_id_input').value = terrenoSeleccionadoId;
            
            recalcularYProyectar();
        });
    });

    // 4. Cálculos Automáticos y Proyección
    function recalcularYProyectar() {
        const enganche = parseFloat(document.getElementById('pago_inicial').value) || 0;
        const saldo = precioTerreno - enganche;
        const radioMeses = document.querySelector('input[name="mensualidades"]:checked');
        
        document.getElementById('monto-a-financiar').textContent = `$${saldo.toLocaleString('en-US', {minimumFractionDigits:2})}`;

        [12, 24, 36, 48, 60].forEach(m => {
            const cuota = saldo > 0 ? saldo / m : 0;
            const label = document.getElementById(`label-monto-${m}`);
            if(label) label.textContent = `$${cuota.toLocaleString('en-US', {minimumFractionDigits:2})}`;
        });

        const tablaPreview = document.getElementById('tabla-pagos-preview');
        if (radioMeses && precioTerreno > 0) {
            const meses = parseInt(radioMeses.value);
            const cuotaMensual = saldo / meses;
            let saldoRestante = saldo;

            let html = `<table class="table table-sm mt-2">
                            <thead><tr><th>Pago</th><th>Cuota</th><th>Saldo Restante</th></tr></thead>
                            <tbody>`;
            for (let i = 1; i <= meses; i++) {
                saldoRestante -= cuotaMensual;
                html += `<tr>
                            <td>#${i}</td>
                            <td class="fw-bold text-primary">$${cuotaMensual.toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                            <td class="text-danger">$${Math.abs(saldoRestante).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                         </tr>`;
            }
            html += `</tbody></table>`;
            tablaPreview.innerHTML = html;
        } else {
            tablaPreview.innerHTML = '<div class="alert alert-warning p-2 small">Seleccione un terreno y un plazo para ver la proyección.</div>';
        }
        
        validar();
    }

    // 5. Validación de Botón
    function validar() {
        const btn = document.getElementById('process-payment');
        const radio = document.querySelector('input[name="mensualidades"]:checked');
        btn.disabled = !(clienteId && terrenoSeleccionadoId && radio && precioTerreno > 0);
    }

    document.getElementById('pago_inicial').addEventListener('input', recalcularYProyectar);
    document.getElementsByName('mensualidades').forEach(r => r.addEventListener('change', recalcularYProyectar));

    document.getElementById('btn-toggle-pagos').addEventListener('click', () => {
        const p = document.getElementById('panel-pagos');
        p.style.display = p.style.display === 'none' ? 'block' : 'none';
        if(p.style.display === 'block') recalcularYProyectar();
    });

    // 6. Registro de Venta con Apertura de Contrato
    document.getElementById('process-payment').addEventListener('click', async function() {
        const radio = document.querySelector('input[name="mensualidades"]:checked');
        const payload = {
            _token: "{{ csrf_token() }}",
            cliente_id: clienteId,
            terreno_id: terrenoSeleccionadoId,
            total: precioTerreno,
            pago_inicial: document.getElementById('pago_inicial').value,
            mensualidades: radio.value,
            fecha_compra: new Date().toISOString().split('T')[0],
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
                alert('✅ Venta registrada con éxito');
                
                // Abrir contrato en pestaña nueva
                window.open("{{ url('ventas/contrato') }}/" + data.venta_id, '_blank');
                
                // Recargar para limpiar formulario
                window.location.reload();
            } else {
                alert('❌ Error: ' + (data.message || 'No se pudo procesar'));
            }
        } catch (e) {
            alert('⚠️ Error de conexión con el servidor');
        }
    });
});
</script>
@endsection