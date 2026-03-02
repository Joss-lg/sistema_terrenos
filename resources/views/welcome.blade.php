<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inmobiliaria | Compra y Venta de Terrenos</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; }
        .font-playfair { font-family: 'Playfair Display', serif; }

        .hero-bg {
            position: relative;
            min-height: 100vh;
            background-image:
                linear-gradient(rgba(0,0,0,0.60), rgba(0,0,0,0.60)),
                url("{{ asset('images/descarga.jpg') }}");
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
        }

        .glass-box {
            background: rgba(0, 0, 0, 0.42);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 26px;
            padding: 4rem 2rem;              /* antes 5rem */
            width: 92%;
            max-width: 1200px;               /* antes 1400px (se veía enorme) */
            box-shadow: 0 30px 70px rgba(0,0,0,.45);
        }

        .fade-in { animation: fadeIn 1.1s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Botón azul pro */
        .btn-blue {
            background: linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%);
            border: 1px solid rgba(255,255,255,.18);
            box-shadow: 0 14px 30px rgba(37,99,235,.25);
            transition: transform .15s ease, filter .15s ease, box-shadow .15s ease;
        }
        .btn-blue:hover{
            filter: brightness(1.08);
            transform: translateY(-1px);
            box-shadow: 0 18px 34px rgba(37,99,235,.32);
        }
        .btn-blue:active{
            transform: scale(.99);
        }
    </style>
</head>

<body class="antialiased">
    <div class="hero-bg">

        <header class="p-6 lg:px-12 flex justify-between items-center z-10">
            <div></div>

            @if (Route::has('login'))
                <nav>
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="text-white border border-white/50 px-5 py-2 rounded-md hover:bg-white hover:text-black transition no-underline">
                            Panel
                        </a>
                    @else
                        {{-- ✅ Botón azul --}}
                        <a href="{{ route('login') }}"
                           class="btn-blue text-white px-6 py-2.5 rounded-md font-semibold transition no-underline">
                            Iniciar Sesión
                        </a>
                    @endauth
                </nav>
            @endif
        </header>

        <main class="flex-grow flex items-center justify-center text-center px-6 z-10 fade-in">
            <div class="glass-box">

                {{-- ✅ TÍTULO MÁS PEQUEÑO / PRO --}}
                <h1 class="font-playfair text-white font-bold leading-[0.9] tracking-tight
                           text-4xl sm:text-5xl md:text-7xl lg:text-8xl">
                    Compra y venta
                    <br>
                    <span class="italic font-light block mt-4 opacity-90
                                 text-3xl sm:text-4xl md:text-6xl lg:text-7xl">
                        de terrenos
                    </span>
                </h1>

                <p class="mt-10 text-base sm:text-lg md:text-xl text-white/90 font-light max-w-4xl mx-auto leading-relaxed border-t border-white/20 pt-7">
                    Vive la experiencia de adquirir una residencia excepcional. Propiedades exclusivas, atención personalizada y un proceso diseñado para quienes buscan invertir con distinción y confianza.
                </p>
            </div>
        </main>

        <footer class="p-8 text-center text-white/40 text-xs tracking-[0.3em] z-10 uppercase">
            &copy; {{ date('Y') }} Inmobiliaria
        </footer>
    </div>
</body>
</html>