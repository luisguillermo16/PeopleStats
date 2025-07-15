<!-- resources/views/partials/sidebar.blade.php -->
<div class="col-md-3 col-lg-2 sidebar text-white p-0">
    <div class="p-4">
        <!-- Información del Usuario -->
        <div class="user-info mb-4">
            <div class="d-flex align-items-center">
                <div class="user-avatar me-3">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                             alt="Avatar" 
                             class="rounded-circle" 
                             width="40" 
                             height="40">
                    @else
                        <i class="bi bi-person"></i>
                    @endif
                </div>
                <div>
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <small class="mb-0">
                        @if(auth()->user()->roles->isNotEmpty())
                            {{ auth()->user()->roles->first()->name }}
                        @else
                            Sin rol asignado
                        @endif
                    </small>
                </div>
            </div>
            
            <!-- Mostrar permisos del usuario actual -->
            @if(auth()->user()->getAllPermissions()->isNotEmpty())
            <div class="mt-2">
                <small class="mb-0">
                    <i class="bi bi-shield-check me-1"></i>
                    {{ auth()->user()->getAllPermissions()->count() }} permisos activos
                </small>
            </div>
            @endif
            
            <hr class="my-3 opacity-25">
            
            <!-- Botones de acción -->
            <div class="d-flex gap-2">
                <a href="#" class="btn btn-outline-light btn-sm flex-fill" title="Próximamente - Perfil">
                    <i class="bi bi-person-gear me-1"></i> Perfil
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="flex-fill">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-box-arrow-right me-1"></i> Salir
                    </button>
                </form>
            </div>
        </div>

       <nav class="nav flex-column">

    <!-- Dashboard -->
    @can('ver todo dashboard')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('#') ? 'active' : '' }}" 
       href="{{ route('admin') }}" title="Dashboard">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>
    @endcan
       @can('ver todo dashboard')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin') ? 'active' : '' }}" 
       href="{{ route('admin') }}" title="Gestión de Usuarios">
         <i class="bi bi-person-badge me-2"></i> Gestión de Usuarios
    </a>
    @endcan

    <!-- Administración Alcaldes -->
    @can('crear alcaldes')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.alcaldes.*') ? 'active' : '' }}" 
       href="#" title="Administrar Alcaldes">
        <i class="bi bi-person-badge me-2"></i> Crear Alcaldes
        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
    </a>
    @endcan

    <!-- Votaciones Alcaldes -->
    @can('ver votaciones alcaldes')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.votaciones.alcaldes') ? 'active' : '' }}" 
       href="#" title="Ver Votaciones Alcaldes">
        <i class="bi bi-bar-chart-line me-2"></i> Votaciones Alcaldes
        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
    </a>
    @endcan

    <!-- Concejales vinculados al Alcalde -->
    @can('crear concejales vinculados al alcalde')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.concejales.vinculados') ? 'active' : '' }}" 
       href="home" title="Crear Concejales vinculados al Alcalde">
        <i class="bi bi-people-fill me-2"></i> Crear Concejales vinculados
    </a>
    @endcan
  
 <!-- Concejales vinculados al Alcalde -->
    @can('dashboardAlcalde')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.concejales.vinculados') ? 'active' : '' }}" 
        href="{{ route('dashboardAlcalde') }}" title="Crear Concejales vinculados al Alcalde">
        <i class="bi bi-people-fill me-2"></i> dashboardAlcalde
    </a>
    @endcan
    <!-- Gestión Concejales -->
    @can('crear concejales')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('home') ? 'active' : '' }}" 
       href="{{ route('crearConcejal') }}" title="Crear Concejales">
        <i class="bi bi-person-plus me-2"></i> Crear Concejales
      
    </a>
    @endcan

    <!-- Ver votantes del Alcalde -->
    @can('ver votantes del alcalde')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('alcaldia.votantes') ? 'active' : '' }}" 
       href="#" title="Ver Votantes del Alcalde">
        <i class="bi bi-people me-2"></i> Ver Votantes Alcalde
        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
    </a>
    @endcan

    <!-- Ver votaciones Concejales -->
    @can('ver votaciones concejales')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.votaciones.concejales') ? 'active' : '' }}" 
       href="#" title="Ver Votaciones Concejales">
        <i class="bi bi-bar-chart-line me-2"></i> Votaciones Concejales
        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
    </a>
    @endcan

    <!-- Ver votantes del Concejal -->
    @can('ver votantes del concejal')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('concejal.votantes') ? 'active' : '' }}" 
       href="#" title="Ver Votantes del Concejal">
        <i class="bi bi-people me-2"></i> Ver Votantes Concejal
        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
    </a>
    @endcan

    <!-- Crear Líderes -->
    @can('crear lideres')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.lideres.*') ? 'active' : '' }}" 
       href="{{ route('crearLider') }}" title="Crear Líderes">
        <i class="bi bi-person-star me-2"></i> Crear Líderes
        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
    </a>
    @endcan

    <!-- Ingresar Votantes -->
    @can('ingresar votantes')
    <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('votantes.ingresar') ? 'active' : '' }}" 
       href="#" title="Ingresar Votantes">
        <i class="bi bi-person-plus me-2"></i> Ingresar Votantes
        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
    </a>
    @endcan

</nav>
    </div>
</div>


<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Mantener abiertos los submenús activos y girar chevron
    document.querySelectorAll('.sidebar-link.active').forEach(link => {
        const collapseId = link.closest('.collapse')?.id;
        if (collapseId) {
            const collapseToggle = document.querySelector(`[href="#${collapseId}"]`);
            if (collapseToggle) {
                const chevron = collapseToggle.querySelector('.bi-chevron-down');
                if (chevron) {
                    chevron.style.transform = 'rotate(180deg)';
                }
            }
        }
    });

    // 2. Efecto de carga escalonado
    document.querySelectorAll('.sidebar-link').forEach((link, index) => {
        link.style.animationDelay = `${index * 0.05}s`;
        link.classList.add('animate-slide-in');
    });

    // 3. Control de submenús con rotación de chevron
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(link => {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            const chevron = this.querySelector('.bi-chevron-down');

            if (target && target.classList.contains('collapse')) {
                e.preventDefault(); // Prevenir navegación
                
                // Cerrar todos los demás submenús
                document.querySelectorAll('.collapse.show').forEach(opened => {
                    if (opened !== target) {
                        opened.classList.remove('show');
                        const toggle = document.querySelector(`[href="#${opened.id}"]`);
                        if (toggle) {
                            const openChevron = toggle.querySelector('.bi-chevron-down');
                            if (openChevron) openChevron.style.transform = 'rotate(0deg)';
                        }
                    }
                });

                // Alternar este submenú
                target.classList.toggle('show');
                if (chevron) {
                    chevron.style.transform = target.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
                }
            }
        });
    });

    // 4. Prevenir navegación en enlaces no funcionales
    document.querySelectorAll('a[href="#"]').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            console.log('Funcionalidad en desarrollo');
        });
    });
});
</script>