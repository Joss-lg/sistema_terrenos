<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema POS</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body{
            font-family: 'Figtree', sans-serif;
            margin: 0;
        }
        .font-playfair{ font-family:'Playfair Display', serif; }

        /* Fondo */
        .bg-login{
            background-image: url('{{ asset('images/imagen4.jpg') }}');
            background-size: cover;
            background-position: center;
        }

        /* Overlay */
        .overlay{
            background: linear-gradient(
                90deg,
                rgba(0,0,0,.88) 0%,
                rgba(0,0,0,.60) 45%,
                rgba(0,0,0,.18) 100%
            );
        }

        /* Glass panel */
        .glass-panel{
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,.12);
            box-shadow: 0 30px 70px rgba(0,0,0,.45);
        }

        /* Inputs */
        .input-dark{
            background: rgba(0,0,0,.28) !important;
            border: 2px solid rgba(59,130,246,.75) !important;
            color: #fff !important;
        }
        .input-dark:focus{
            border-color: rgba(59,130,246,1) !important;
            box-shadow: 0 0 0 4px rgba(59,130,246,.25);
        }

        .input-light{
            background: rgba(226,232,240,.95) !important;
            border: none !important;
            color: #0f172a !important;
        }
        .input-light:focus{
            box-shadow: 0 0 0 4px rgba(255,255,255,.20);
        }

        /* Botón */
        .btn-blue{
            background: linear-gradient(180deg, #3b82f6, #2563eb);
            transition: transform .15s ease, filter .15s ease;
        }
        .btn-blue:hover{
            filter: brightness(1.08);
            transform: translateY(-1px);
        }
        .btn-blue:active{
            transform: scale(.99);
        }

        .label{
            letter-spacing: .08em;
        }
    </style>
</head>

<body class="bg-black antialiased">
<div class="relative min-h-screen bg-login flex items-center">
    {{-- Overlay --}}
    <div class="absolute inset-0 overlay"></div>

    {{-- Volver --}}
    <div class="absolute top-0 left-0 p-8 z-20">
        <a href="{{ url('/') }}"
           class="flex items-center gap-2 text-white/70 hover:text-white transition-all no-underline">
            <span class="text-2xl leading-none">‹</span>
            <span class="text-xs font-medium">Volver al Inicio</span>
        </a>
    </div>

    {{-- Contenido --}}
    <div class="relative z-10 w-full px-8 md:px-20 lg:px-24">
        <div class="w-full flex flex-col md:flex-row items-center justify-between gap-12">

            {{-- Izquierda --}}
            <div class="w-full md:w-1/2 text-white pt-6 md:pt-20">
                <h1 class="font-playfair font-bold leading-none tracking-tight text-6xl md:text-7xl lg:text-8xl">
                    Bienvenido
                </h1>
                <p class="mt-3 text-lg md:text-xl text-white/80 font-light">
                    Inmoviliaria de Terrenos 
                </p>
            </div>

            
            <div class="w-full flex justify-center md:justify-end md:pt-16">
    <div style="width:440px; max-width:440px;">
        <div class="glass-panel rounded-2xl px-8 py-9 md:px-10 md:py-10">


                    <div class="text-center mb-8">
                        <h2 class="font-playfair text-[34px] font-bold text-white tracking-tight">
                            Iniciar Sesión
                        </h2>
                        <p class="text-white/60 mt-1 text-sm">
                            Acceso al Sistema
                        </p>
                    </div>

                    {{-- Errores --}}
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-500/20 border border-red-500/40 text-red-100 rounded-lg text-xs">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-[11px] font-semibold text-white/70 mb-2 uppercase label">
                                Correo Electrónico
                            </label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                class="input-dark w-full rounded-lg px-4 py-3 text-sm outline-none transition">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-white/70 mb-2 uppercase label">
                                Contraseña
                            </label>
                            <input
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="input-light w-full rounded-lg px-4 py-3 text-sm outline-none transition">
                        </div>

                        <button type="submit"
                                class="btn-blue w-full py-3.5 rounded-lg text-white font-semibold tracking-wide shadow-lg">
                            Iniciar Sesión
                        </button>

                        <div class="text-center pt-1">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-xs text-blue-300 hover:text-blue-200 underline decoration-blue-400/30">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
