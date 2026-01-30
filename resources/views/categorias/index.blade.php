@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f2f2f2;
    }
    /* Estructura del encabezado profesional */
    .title-bar {
        background: #0d2c4b; /* Azul oscuro solicitado */
        color: #fff;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 30px;
    }
    .title-bar span {
        font-size: 0.85rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        opacity: 0.8;
        display: block;
        margin-bottom: 5px;
    }
    .title-bar h2 {
        font-weight: 700;
        font-size: 2.2rem;
        margin: 0;
    }
    
    /* Estilo de las tarjetas */
    .property-card {
        background: #ffffff;
        border: 1px solid #dcdcdc;
        border-radius: 10px;
        overflow: hidden;
        transition: 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .property-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.18);
    }
    .property-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .card-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .property-title {
        color: #000000; 
        font-weight: bold;
        font-size: 1.4rem;
    }

    /* Botones con el azul oscuro de la imagen */
    .btn-elegant {
        background: #0d2c4b;
        color: #fff;
        font-weight: 600;
        border: none;
        padding: 10px;
        transition: 0.3s;
    }
    .btn-elegant:hover {
        background: #153e66;
        color: #fff;
    }
</style>

<div class="container">
    <div class="title-bar shadow-sm">
        <span>INMOBILIARIA • CASAS</span>
        <h2>Clasificación de Casas</h2>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="property-card shadow-sm">
                <img src="images/basica1.jpg" alt="Casa Basica">
                <div class="card-content">
                    <div>
                        <h4 class="property-title">BÁSICA</h4>
                        <p>Vivienda accesible para familias que buscan economía, funcionalidad y servicios esenciales para vivir cómodamente.</p>
                    </div>
                    <button class="btn btn-elegant w-100" data-bs-toggle="modal" data-bs-target="#modalBasica">Ver detalles</button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="property-card shadow-sm">
                <img src="images/casamedium.jpg" alt="Casa Media">
                <div class="card-content">
                    <div>
                        <h4 class="property-title">MEDIA</h4>
                        <p>Vivienda cómoda con espacios funcionales, acabados de calidad, servicios completos y buena conectividad urbana.</p>
                    </div>
                    <button class="btn btn-elegant w-100" data-bs-toggle="modal" data-bs-target="#modalMedia">Ver detalles</button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="property-card shadow-sm">
                <img src="images/casapremium.jpg" alt="Casa Premium">
                <div class="card-content">
                    <div>
                        <h4 class="property-title">PREMIUM</h4>
                        <p>Vivienda de alta calidad con acabados de lujo, áreas amplias, seguridad, jardines, amenidades y ubicación privilegiada.</p>
                    </div>
                    <button class="btn btn-elegant w-100" data-bs-toggle="modal" data-bs-target="#modalPremium">Ver detalles</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBasica" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white"> 
                <h5 class="modal-title font-weight-bold">Detalles: Vivienda BÁSICA</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <img src="images/plano 1.jpg" class="img-fluid rounded border shadow-sm" alt="Plano Básica">
                    </div>
                    <div class="col-md-5">
                        <h5 class="text-warning">Especificaciones</h5>
                        <hr>
                        <ul class="list-unstyled">
                            <li><strong>Terreno:</strong> 90m²</li>
                            <li><strong>Construcción:</strong> 65m²</li>
                            <li><strong>Dormitorios:</strong> 1</li>
                            <li><strong>Baños:</strong> 1</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMedia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold">Detalles: Vivienda MEDIA</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <img src="images/plano 2.jpg" class="img-fluid rounded border shadow-sm" alt="Plano Media">
                    </div>
                    <div class="col-md-5">
                        <h5 class="text-warning">Especificaciones</h5>
                        <hr>
                        <ul class="list-unstyled">
                            <li><strong>Terreno:</strong> 200m²</li>
                            <li><strong>Construcción:</strong> 140m²</li>
                            <li><strong>Habitaciones:</strong> 3</li>
                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPremium" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold">Detalles: Vivienda PREMIUM</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <img src="images/plano 3.jpg" class="img-fluid rounded border shadow-sm" alt="Plano Premium">
                    </div>
                    <div class="col-md-5">
                        <h5 class="text-warning">Especificaciones</h5>
                        <hr>
                        <ul class="list-unstyled">
                            <li><strong>Terreno:</strong> 1500m²</li>
                            <li><strong>Construcción:</strong> 800m²</li>
                            <li><strong>Habitaciones:</strong> 3</li>
                            

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection