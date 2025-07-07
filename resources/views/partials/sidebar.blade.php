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
          

            <!-- Gestión de Usuarios -->
            @can('ver todo dashboard')
            <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin') ? 'active' : '' }}" 
               href="{{ route('admin') }}" title="Próximamente - Gestión de Usuarios">
                <i class="bi bi-people me-2"></i> Gestión de Usuarios
               
            </a>
            @endcan
              @can('ver todo dashboard')
            <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('') ? 'active' : '' }}" 
               href="#" title="Próximamente - Dashboard">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            @endcan
            <!-- Gestión de Perfiles -->
            @canany(['ver perfiles', 'crear perfiles', 'editar perfiles'])
            <div class="nav-item">
                <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.profiles.*') ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#profilesSubmenu" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.profiles.*') ? 'true' : 'false' }}">
                    <i class="bi bi-person-badge me-2"></i> 
                    Perfiles
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.profiles.*') ? 'show' : '' }}" id="profilesSubmenu">
                    <div class="nav flex-column ms-3">
                        @can('ver perfiles')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.profiles.index') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Ver Perfiles">
                            <i class="bi bi-list me-2"></i> Ver Perfiles
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                        
                        @can('crear perfiles')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.profiles.create') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Crear Perfil">
                            <i class="bi bi-plus-circle me-2"></i> Crear Perfil
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                        
                        @can('editar perfiles')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.profiles.edit') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Editar Perfiles">
                            <i class="bi bi-pencil-square me-2"></i> Editar Perfiles
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            @endcanany

            <!-- Gestión de Alcaldes -->
            @canany(['crear alcaldes', 'ver volantes del alcalde'])
            <div class="nav-item">
                <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.alcaldes.*') ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#alcaldesSubmenu" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.alcaldes.*') ? 'true' : 'false' }}">
                    <i class="bi bi-person-workspace me-2"></i> 
                    Alcaldes
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.alcaldes.*') ? 'show' : '' }}" id="alcaldesSubmenu">
                    <div class="nav flex-column ms-3">
                        @can('crear alcaldes')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.alcaldes.create') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Crear Alcalde">
                            <i class="bi bi-plus-circle me-2"></i> Crear Alcalde
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                        
                        @can('ver volantes del alcalde')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.alcaldes.volantes') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Volantes del Alcalde">
                            <i class="bi bi-file-earmark-text me-2"></i> Volantes del Alcalde
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            @endcanany

            <!-- Gestión de Concejales -->
            @canany(['crear concejales', 'ver volantes del concejal'])
            <div class="nav-item">
                <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.concejales.*') ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#concejalesSubmenu" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.concejales.*') ? 'true' : 'false' }}">
                    <i class="bi bi-people-fill me-2"></i> 
                    Concejales
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.concejales.*') ? 'show' : '' }}" id="concejalesSubmenu">
                    <div class="nav flex-column ms-3">
                        @can('crear concejales')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.concejales.create') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Crear Concejal">
                            <i class="bi bi-plus-circle me-2"></i> Crear Concejal
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                        
                        @can('ver volantes del concejal')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.concejales.volantes') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Volantes del Concejal">
                            <i class="bi bi-file-earmark-text me-2"></i> Volantes del Concejal
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                         
                        @can('ver votantes del concejal')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.concejales.votantes') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Votantes del Concejal">
                            <i class="bi bi-file-earmark-text me-2"></i> Votantes del Concejal
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            @endcanany

            <!-- Gestión de Líderes -->
            @can('crear lideres')
            <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.lideres.*') ? 'active' : '' }}" 
               href="#" title="Próximamente - Crear Líderes">
                <i class="bi bi-person-star me-2"></i> 
                Crear Líderes
                <small class="badge bg-warning text-dark ms-2">Próximamente</small>
            </a>
            @endcan

            <!-- Gestión de Volantes -->
            @canany(['ingresar volantes', 'ver volantes del alcalde', 'ver volantes del concejal'])
            <div class="nav-item">
                <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.volantes.*') ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#volantesSubmenu" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.volantes.*') ? 'true' : 'false' }}">
                    <i class="bi bi-clipboard-data me-2"></i> 
                    Volantes
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.volantes.*') ? 'show' : '' }}" id="volantesSubmenu">
                    <div class="nav flex-column ms-3">
                        @can('ingresar volantes')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.volantes.create') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Ingresar Volantes">
                            <i class="bi bi-plus-circle me-2"></i> Ingresar Volantes
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                        
                        @can('ver volantes del alcalde')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.volantes.alcalde') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Volantes Alcalde">
                            <i class="bi bi-person-workspace me-2"></i> Volantes Alcalde
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                        
                        @can('ver volantes del concejal')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.volantes.concejal') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Volantes Concejal">
                            <i class="bi bi-people-fill me-2"></i> Volantes Concejal
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            @endcanany

            <!-- Gestión del Sistema -->
            @canany(['ver logs sistema', 'administrar sistema'])
            <div class="nav-item">
                <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.sistema.*') ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#sistemaSubmenu" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.sistema.*') ? 'true' : 'false' }}">
                    <i class="bi bi-gear me-2"></i> 
                    Sistema
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.sistema.*') ? 'show' : '' }}" id="sistemaSubmenu">
                    <div class="nav flex-column ms-3">
                        @can('ver logs sistema')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.sistema.logs') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Ver Logs del Sistema">
                            <i class="bi bi-journal-text me-2"></i> Ver Logs del Sistema
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                        
                        @can('administrar sistema')
                        <a class="nav-link text-white sidebar-link p-2 {{ request()->routeIs('admin.sistema.settings') ? 'active' : '' }}" 
                           href="#" title="Próximamente - Administrar Sistema">
                            <i class="bi bi-sliders me-2"></i> Administrar Sistema
                            <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            @endcanany

            <!-- Separador -->
            <hr class="my-3 opacity-25">

            <!-- Accesos Rápidos -->
            <div class="menu-title">
                <small class="mb-0">ACCESOS RÁPIDOS</small>
            </div>

            <!-- Resumen de Volantes -->
            @canany(['ver volantes del alcalde', 'ver volantes del concejal'])
            <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.dashboard.volantes') ? 'active' : '' }}" 
               href="#" title="Próximamente - Resumen de Volantes">
                <i class="bi bi-pie-chart me-2"></i> 
                Resumen de Volantes
                <small class="badge bg-warning text-dark ms-2">Próximamente</small>
            </a>
            @endcanany

            <!-- Estadísticas -->
            @can('ver todo dashboard')
            <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('admin.estadisticas.*') ? 'active' : '' }}" 
               href="#" title="Próximamente - Estadísticas">
                <i class="bi bi-bar-chart me-2"></i> 
                Estadísticas
                <small class="badge bg-warning text-dark ms-2">Próximamente</small>
            </a>
            @endcan

            <!-- Perfil del Usuario -->
            <a class="nav-link text-white sidebar-link p-3 {{ request()->routeIs('profile.*') ? 'active' : '' }}" 
               href="#" title="Próximamente - Mi Perfil">
                <i class="bi bi-person-circle me-2"></i> Mi Perfil
                <small class="badge bg-warning text-dark ms-2">Próximamente</small>
            </a>
        </nav>
    </div>
</div>

<!-- Estilos CSS -->
<style>
.sidebar {
    background: linear-gradient(145deg, #2c3e50 0%, #34495e 100%);
    box-shadow: 2px 0 15px rgba(0,0,0,0.1);
    border-radius: 0 15px 15px 0;
    min-height: 100vh;
}

.sidebar-link {
    border-radius: 8px;
    margin: 2px 0;
    transition: all 0.3s ease;
    position: relative;
    border: none;
    background: none;
    text-decoration: none;
}

.sidebar-link:hover {
    background: rgba(255,255,255,0.1);
    transform: translateX(5px);
    color: #ffffff !important;
}

.sidebar-link.active {
    background: rgba(255,255,255,0.2);
    border-left: 4px solid #3498db;
    color: #ffffff !important;
}

/* Estilos para links deshabilitados */
.sidebar-link[href="#"] {
    opacity: 0.7;
    cursor: not-allowed;
}

.sidebar-link[href="#"]:hover {
    background: rgba(255,255,255,0.05);
    transform: none;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.user-avatar img {
    object-fit: cover;
}

.badge {
    font-size: 9px;
    padding: 2px 6px;
    border-radius: 12px;
    font-weight: 500;
}

.collapse .nav-link {
    font-size: 14px;
    padding: 8px 16px;
}

.menu-title {
    padding: 10px 0 5px 0;
    margin-top: 20px;
}

.menu-title small {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1px;
}

/* Animaciones para submenús */
.collapse {
    transition: all 0.3s ease;
}

.collapse.show {
    background: rgba(0,0,0,0.1);
    border-radius: 8px;
    margin: 5px 0;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        position: fixed;
        z-index: 1000;
        width: 100%;
        border-radius: 0;
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
    
    .badge {
        display: none;
    }
}

/* Efectos de hover mejorados */
.sidebar-link:hover i {
    color: #3498db;
    transform: scale(1.1);
    transition: all 0.3s ease;
}

/* Indicadores de estado */
.sidebar-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 70%;
    background: #3498db;
    border-radius: 0 4px 4px 0;
}

/* Notificación de desarrollo */
.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

/* Transiciones suaves para chevrons */
.bi-chevron-down {
    transition: transform 0.3s ease;
}

/* Animaciones de entrada */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-slide-in {
    animation: slideIn 0.3s ease-out forwards;
}
</style>

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