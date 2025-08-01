<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('tituloPage', 'Panel de Administración')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    
    @stack('styles')  <!-- Aquí para estilos adicionales -->
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
                            @yield('tituloPage', 'Titulo de pagina')
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
  @stack('scripts')
</body>
</html>