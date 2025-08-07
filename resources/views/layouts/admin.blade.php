<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('tituloPage', 'Panel de Administración')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Estilos personalizados --}}
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #4ade80;
            --warning-color: #fbbf24;
            --danger-color: #f87171;
            --info-color: #60a5fa;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .admin-header h2 {
            font-weight: 600;
            color: #1f2937;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border: none;
        }

        .alert {
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')  <!-- Aquí para estilos adicionales desde otras vistas -->
</head>
<body>
    {{-- Sidebar --}}
    @include('partials.sidebar')
    
    {{-- Contenido principal --}}
    <div class="main-content">
        <div class="p-4">
            <div class="admin-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">
                            @yield('tituloPage', 'Titulo de página')
                        </h2>
                    </div>
                </div>
            </div>
            
            @yield('contenido')
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('dist/js/adminJs/admin.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
