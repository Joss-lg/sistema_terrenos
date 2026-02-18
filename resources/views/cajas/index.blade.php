@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 1200px; padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark"><i class="fas fa-hand-holding-usd me-2"></i>Módulo de Cobranza</h2>
        <span class="badge bg-success px-3 py-2">Caja Sistema Activa</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow border-0">
                <div class="card-header text-white" style="background:#158499;">
                    <div class="fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Registrar Pago</div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Seleccionar Cliente</label>
                        {{-- ID del select es cliente_id --}}
                       <select id="cliente_id" class="form-select select2">
    <option value="">-- Buscar Cliente --</option>
    @foreach($clientes as $c)
        {{-- CORRECCIÓN: Usamos 'id' porque así se llama en tu base de datos --}}
        <option value="{{ $c->id }}" 
                data-tel="{{ $c->telefono }}" 
                data-dir="{{ $c->direccion }}">
            {{ $c->cliente }}
        </option>
    @endforeach
</select>
                    </div>

                    <div id="info-contacto" class="p-2 mb-3 border rounded bg-light" style="display:none; border-left: 4px solid #158499 !important;">
                        <div class="small text-dark">
                            <i class="fas fa-map-marker-alt me-2 text-muted"></i><strong>Dir:</strong> <span id="txt-dir"></span><br>
                            <i class="fas fa-phone me-2 text-muted"></i><strong>Tel:</strong> <span id="txt-tel"></span>
                        </div>
                    </div>

                    <div id="info-deuda" style="display:none;">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="small fw-bold">Mensualidad Pactada</label>
                                <select id="selectMontoCuota" class="form-select">
                                    <option value="4000">$4,000</option>
                                    <option value="5000">$5,000</option>
                                </select>
                            </div>
                            <div class="col-6 text-center">
                                <label class="small fw-bold text-danger">Deuda Pendiente</label>
                                <h4 id="lblDeudaTotal" class="fw-bold text-danger">$0.00</h4>
                            </div>
                        </div>

                        <div class="border rounded p-3 mb-3 bg-dark text-white text-center">
                            <small class="opacity-75">TOTAL A COBRAR HOY (Con 10% si aplica)</small>
                            <h2 class="fw-bold m-0" id="lblTotalCobro">$0.00</h2>
                            <div class="small mt-1" id="lblMultaDetalle">Calculando recargos...</div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-7">
                                <label class="form-label small fw-bold">¿Cuántas mensualidades paga?</label>
                                <div class="input-group">
                                    <input type="number" id="input-cantidad" class="form-control" value="1" min="1">
                                    <button class="btn btn-outline-danger btn-sm" type="button" id="btn-liquidar">LIQUIDAR</button>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">Método</label>
                                <select id="metodo_pago" class="form-select">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transf.</option>
                                </select>
                            </div>
                        </div>

                        <button id="btn-procesar-cobro" class="btn btn-success btn-lg w-100 shadow-sm">
                            <i class="fas fa-check-circle me-1"></i> CONFIRMAR COBRO
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow border-0">
                <div id="titulo-derecho" class="card-header bg-dark text-white fw-bold">Últimos Movimientos de Caja</div>
                <div class="card-body p-0">
                    <div id="contenedor-tabla" class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-3">Descripción / Fecha</th>
                                    <th class="text-end pe-3">Monto</th>
                                </tr>
                            </thead>
                            <tbody id="lista-movimientos">
                                {{-- Carga inicial de movimientos generales --}}
                                @foreach($movimientos as $m)
                                    <tr>
                                        <td class="ps-3">
                                            <small class="text-muted d-block">{{ $m->created_at->format('d/m/Y H:i') }}</small>
                                            {{ $m->descripcion }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <strong class="{{ $m->tipo == 'ingreso' ? 'text-success' : 'text-danger' }}">
                                                ${{ number_format($m->monto, 2) }}
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Referencias
    const $selectCli = $('#cliente_id');
    const selectMonto = document.getElementById('selectMontoCuota');
    const inputCant = document.getElementById('input-cantidad');
    const lblTotal = document.getElementById('lblTotalCobro');
    const lblMulta = document.getElementById('lblMultaDetalle');
    const btnProcesar = document.getElementById('btn-procesar-cobro');
    const tituloDer = document.getElementById('titulo-derecho');
    const listaMovimientos = document.getElementById('lista-movimientos');
    let deudaActual = 50000; // Simulación inicial

    console.log("Sistema de Cobranza Iniciado.");

    // 2. DETECTAR CAMBIO (Funciona con Select2 y Normal)
    $selectCli.on('change select2:select', function() {
        const clienteId = $(this).val();
        
        // Datos adicionales (data-tel, data-dir)
        const $option = $(this).find(':selected');
        const dir = $option.data('dir');
        const tel = $option.data('tel');

        console.log("Cliente seleccionado ID:", clienteId);

        // Si no hay cliente seleccionado, ocultamos paneles
        if(!clienteId) {
            $('#info-contacto, #info-deuda').hide();
            tituloDer.textContent = "Últimos Movimientos de Caja";
            return;
        }

        // Mostrar Info de Contacto
        $('#txt-dir').text(dir || 'Sin dirección registrada');
        $('#txt-tel').text(tel || 'Sin teléfono registrado');
        $('#info-contacto, #info-deuda').fadeIn(); // Efecto suave

        // 3. CARGAR HISTORIAL VÍA AJAX
        tituloDer.textContent = "Cargando Estado de Cuenta...";
        listaMovimientos.innerHTML = '<tr><td colspan="2" class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><br>Buscando historial...</td></tr>';

        // Definimos la URL usando el origen actual del navegador
        const urlHistorial = `${window.location.origin}/cajas/historial/${clienteId}`;
        console.log("Consultando:", urlHistorial);

        $.get(urlHistorial)
            .done(function(data) {
                console.log("Respuesta servidor:", data);
                tituloDer.textContent = "Estado de Cuenta del Terreno";
                listaMovimientos.innerHTML = '';

                if(Array.isArray(data) && data.length > 0) {
                    data.forEach(pago => {
                        // Limpieza visual del concepto
                        let concepto = pago.concepto || 'Pago registrado';
                        
                        listaMovimientos.innerHTML += `
                            <tr>
                                <td class="ps-3">
                                    <small class="text-muted d-block">${pago.fecha_pago}</small>
                                    <span class="fw-bold text-dark">${concepto}</span>
                                </td>
                                <td class="text-end pe-3 align-middle">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3">
                                        $${parseFloat(pago.monto).toLocaleString('en-US', {minimumFractionDigits: 2})}
                                    </span>
                                </td>
                            </tr>`;
                    });
                } else {
                    listaMovimientos.innerHTML = '<tr><td colspan="2" class="text-center text-muted p-4"><i class="fas fa-folder-open fa-2x mb-2"></i><br>No hay pagos registrados para este cliente.</td></tr>';
                }
                
                // Aquí podrías actualizar la deudaActual si el servidor la devuelve
                // deudaActual = data.deuda_pendiente ?? 50000;
                recalcular();
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX:", textStatus, errorThrown);
                tituloDer.textContent = "Error de conexión";
                listaMovimientos.innerHTML = '<tr><td colspan="2" class="text-danger text-center p-3"><i class="fas fa-exclamation-triangle"></i> Error al cargar el historial.</td></tr>';
            });
    });

    // 4. FUNCIONES DE CÁLCULO
    function recalcular() {
        const cuota = parseFloat(selectMonto.value) || 0;
        const cantidad = parseInt(inputCant.value) || 1;
        const subtotal = cuota * cantidad;
        
        // Regla: Multa del 10% después del día 5
        const dia = new Date().getDate();
        const multa = (dia > 5) ? (subtotal * 0.10) : 0;
        const total = subtotal + multa;

        if(multa > 0) {
            lblMulta.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-exclamation-circle"></i> Recargo 10% aplicado ($${multa.toLocaleString()})</span>`;
        } else {
            lblMulta.innerHTML = `<span class="text-success"><i class="fas fa-check"></i> Pago a tiempo</span>`;
        }
        
        lblTotal.textContent = '$' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('lblDeudaTotal').textContent = '$' + deudaActual.toLocaleString();
    }

    // Eventos para disparar el recálculo
    $('#input-cantidad').on('input', recalcular);
    $('#selectMontoCuota').on('change', recalcular);

    // Botón Liquidar (Calcula cuántos pagos faltan)
    $('#btn-liquidar').click(function() {
        const cuota = parseFloat(selectMonto.value);
        if(cuota > 0) {
            inputCant.value = Math.ceil(deudaActual / cuota);
            recalcular();
        }
    });

    // 5. PROCESAR COBRO
    btnProcesar.addEventListener('click', async function() {
        const clienteId = $selectCli.val();
        
        if(!clienteId) return alert("Por favor, seleccione un cliente.");
        if(!confirm("¿Está seguro de registrar este cobro?")) return;

        // Limpieza de moneda para enviar solo números puros
        const totalLimpio = parseFloat(lblTotal.textContent.replace(/[$,]/g, ''));

        const payload = {
            cliente_id: clienteId,
            mensualidad: selectMonto.value,
            monto_recibido: totalLimpio,
            metodo_pago: document.getElementById('metodo_pago').value,
            _token: '{{ csrf_token() }}'
        };

        btnProcesar.disabled = true;
        btnProcesar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';

        try {
            const response = await fetch("{{ route('cajas.registrarCobro') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const res = await response.json();

            if(res.res || res.abrir_ticket) {
                // Recarga para mostrar cambios y abrir ticket
                window.location.reload();
            } else {
                alert("Error: " + (res.error || "No se pudo procesar la solicitud."));
                btnProcesar.disabled = false;
                btnProcesar.innerHTML = '<i class="fas fa-check-circle me-1"></i> CONFIRMAR COBRO';
            }
        } catch (error) {
            console.error(error);
            alert("Error de red o servidor al procesar el cobro.");
            btnProcesar.disabled = false;
            btnProcesar.innerHTML = '<i class="fas fa-check-circle me-1"></i> CONFIRMAR COBRO';
        }
    });
});
</script>

{{-- SCRIPT PARA ABRIR TICKET AUTOMÁTICAMENTE --}}
@if(session('abrir_ticket'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const datos = @json(session('datos_ticket'));
            if(datos) {
                const params = new URLSearchParams({
                    cliente_id: datos.cliente_id,
                    total: datos.total,
                    multa: datos.multa,
                    saldo_restante: datos.saldo_restante,
                    metodo: '{{ session("metodo_pago") ?? "efectivo" }}'
                });
                // Abrir en nueva pestaña
                window.open("{{ route('cajas.descargarTicket') }}?" + params.toString(), '_blank');
            }
        });
    </script>
@endif
@endsection