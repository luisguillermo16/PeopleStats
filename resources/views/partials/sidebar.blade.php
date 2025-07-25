<!-- resources/views/partials/sidebar.blade.php -->

<!-- Botón Hamburguesa (solo visible en mobile) -->
<div class="hamburger-container">
    <button class="hamburger-btn" id="hamburgerBtn" aria-label="Abrir menú">
        <div class="hamburger-line"></div>
        <div class="hamburger-line"></div>
        <div class="hamburger-line"></div>
    </button>
</div>

<!-- Overlay para mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="sidebar text-white p-0" id="sidebar">
    <div class="sidebar-content">
       
        <!-- Información del Usuario -->
        <div class="user-info p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="user-avatar me-3">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                             alt="Avatar" 
                             class="rounded-circle" 
                             width="40" 
                             height="40">
                    @else
                        <div class="avatar-placeholder">
                            <i class="bi bi-person"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <small class="text-white-50">
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
            <div class="permissions-info mb-3">
                <small class="text-white-50">
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

        <!-- Navegación -->
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <!-- Dashboard -->
                @can('ver todo dashboard')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('admin') ? 'active' : '' }}" 
                       href="{{ route('admin') }}" title="Dashboard">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard
                    </a>
                </li>
                @endcan
                
                @can('ver todo dashboard')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('admin') ? 'active' : '' }}" 
                       href="{{ route('admin') }}" title="Gestión de Usuarios">
                        <i class="bi bi-person-badge me-2"></i>
                        Gestión de Usuarios
                    </a>
                </li>
                @endcan

                <!-- Administración Alcaldes -->
                @can('crear alcaldes')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('admin.alcaldes.*') ? 'active' : '' }}" 
                       href="#" title="Administrar Alcaldes">
                        <i class="bi bi-person-badge me-2"></i>
                        Crear Alcaldes
                        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                    </a>
                </li>
                @endcan

                <!-- Votaciones Alcaldes -->
                @can('ver votaciones alcaldes')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('admin.votaciones.alcaldes') ? 'active' : '' }}" 
                       href="#" title="Ver Votaciones Alcaldes">
                        <i class="bi bi-bar-chart-line me-2"></i>
                        Votaciones Alcaldes
                        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                    </a>
                </li>
                @endcan

                <!-- Concejales vinculados al Alcalde -->
                @can('crear concejales vinculados al alcalde')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('admin.concejales.vinculados') ? 'active' : '' }}" 
                       href="home" title="Crear Concejales vinculados al Alcalde">
                        <i class="bi bi-people-fill me-2"></i>
                        Crear Concejales vinculados
                    </a>
                </li>
                @endcan
              
                <!-- Dashboard Alcalde -->
                @can('dashboardAlcalde')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('dashboardAlcalde') ? 'active' : '' }}" 
                        href="{{ route('dashboardAlcalde') }}" title="Dashboard Alcalde">
                        <i class="bi bi-people-fill me-2"></i>
                        Dashboard Alcalde
                    </a>
                </li>
                @endcan
                
                <!-- Gestión Concejales -->
                @can('crear concejales')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('crearConcejal') ? 'active' : '' }}" 
                       href="{{ route('crearConcejal') }}" title="Crear Concejales">
                        <i class="bi bi-person-plus me-2"></i>
                        Crear Concejales
                    </a>
                </li>
                @endcan

                <!-- Ver votantes del Alcalde -->
                @can('ver votantes del alcalde')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('alcaldia.votantes') ? 'active' : '' }}" 
                         href="{{ route('votantesAlcalde') }}" title="Ver Votantes del Alcalde">
                        <i class="bi bi-people me-2"></i>
                        Ver Votantes Alcalde
                        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                    </a>
                </li>
                @endcan

                <!-- Ver votaciones Concejales -->
                @can('ver votaciones concejales')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('admin.votaciones.concejales') ? 'active' : '' }}" 
                       href="#" title="Ver Votaciones Concejales">
                        <i class="bi bi-bar-chart-line me-2"></i>
                        Votaciones Concejales
                        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                    </a>
                </li>
                @endcan

                <!-- Ver votantes del Concejal -->
                @can('ver votantes del concejal')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('concejal.votantes') ? 'active' : '' }}" 
                       href="#" title="Ver Votantes del Concejal">
                        <i class="bi bi-people me-2"></i>
                        Ver Votantes Concejal
                        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                    </a>
                </li>
                @endcan

                <!-- Crear Líderes -->
                @can('crear lideres')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('admin.lideres.*') ? 'active' : '' }}" 
                       href="{{ route('crearLider') }}" title="Crear Líderes">
                        <i class="bi bi-person-star me-2"></i>
                        Crear Líderes
                        <small class="badge bg-warning text-dark ms-2">Próximamente</small>
                    </a>
                </li>
                @endcan

                <!-- Ingresar Votantes -->
                @can('ingresar votantes')
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link {{ request()->routeIs('votantes.ingresar') ? 'active' : '' }}" 
                        href="{{ route('ingresarVotantes') }}" title="Ingresar Votantes">
                        <i class="bi bi-person-plus me-2"></i>
                        Ingresar Votantes
                      
                    </a>
                </li>
                @endcan
                
            </ul>
        </nav>
    </div>
</div>

<style>
/* =================
   ESTILOS GENERALES
================= */
.sidebar {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    min-height: 100vh;
    box-shadow: 2px 0 15px rgba(0,0,0,0.1);
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    z-index: 1000;
    overflow-y: auto;
}

/* =================
   HEADER DEL SIDEBAR
================= */
.sidebar-header {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    background: rgba(0,0,0,0.1);
}

/* =================
   INFORMACIÓN DEL USUARIO
================= */
.user-info {
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.user-avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-placeholder {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.permissions-info {
    font-size: 0.85rem;
}

/* =================
   NAVEGACIÓN
================= */
.sidebar-nav {
    padding: 1rem 0;
}

.sidebar-nav .nav-link {
    color: rgba(255,255,255,0.8);
    padding: 0.75rem 1.5rem;
    border-radius: 0;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    display: flex;
    align-items: center;
    position: relative;
}

.sidebar-nav .nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    border-left-color: #3498db;
    text-decoration: none;
}

.sidebar-nav .nav-link.active {
    background: rgba(52, 152, 219, 0.2);
    color: white;
    border-left-color: #3498db;
}

.sidebar-nav .nav-link i {
    width: 20px;
    flex-shrink: 0;
}

/* =================
   BOTÓN HAMBURGUESA
================= */
.hamburger-container {
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1100;
    display: none;
}

.hamburger-btn {
    width: 45px;
    height: 45px;
    background: #2c3e50;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 4px;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3);
}

.hamburger-btn:hover {
    transform: scale(1.05);
    background: #34495e;
    box-shadow: 0 6px 20px rgba(44, 62, 80, 0.4);
}

.hamburger-btn:active {
    transform: scale(0.95);
}

.hamburger-line {
    width: 22px;
    height: 2px;
    background: white;
    border-radius: 2px;
    transition: all 0.3s ease;
    transform-origin: center;
}

/* Animación cuando está activo */
.hamburger-btn.active .hamburger-line:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.hamburger-btn.active .hamburger-line:nth-child(2) {
    opacity: 0;
    transform: scaleX(0);
}

.hamburger-btn.active .hamburger-line:nth-child(3) {
    transform: rotate(-45deg) translate(5px, -5px);
}

/* =================
   OVERLAY
================= */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* =================
   ANIMACIONES
================= */
.animate-slide-in {
    animation: slideIn 0.5s ease forwards;
    opacity: 0;
    transform: translateX(-20px);
}

.animate-slide-in:nth-child(1) { animation-delay: 0.1s; }
.animate-slide-in:nth-child(2) { animation-delay: 0.2s; }
.animate-slide-in:nth-child(3) { animation-delay: 0.3s; }
.animate-slide-in:nth-child(4) { animation-delay: 0.4s; }
.animate-slide-in:nth-child(5) { animation-delay: 0.5s; }

@keyframes slideIn {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* =================
   RESPONSIVE DESIGN
================= */

/* Pantallas grandes (Desktop) */
@media (min-width: 768px) {
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        transform: translateX(0);
        height: 100vh;
        overflow-y: auto;
        width: 280px;
    }
    
    .hamburger-container {
        display: none !important;
    }
}

/* Pantallas pequeñas (Mobile) */
@media (max-width: 767.98px) {
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        width: 280px;
        max-width: 85vw;
        overflow-y: auto;
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
    
    .hamburger-container {
        display: block !important;
    }
}

/* Prevenir scroll cuando sidebar está abierto en mobile */
body.sidebar-open {
    overflow: hidden;
}

/* =================
   BADGES Y ELEMENTOS
================= */
.badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Scrollbar personalizada */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.5);
}

/* =================
   CORRECCIÓN PARA EL LAYOUT
================= */
body {
    margin: 0;
    padding: 0;
}

/* Ajustar el contenido principal */
.main-content {
    margin-left: 280px;
    width: calc(100% - 280px);
    min-height: 100vh;
    padding: 0;
}

/* En mobile, el contenido principal ocupa todo el ancho */
@media (max-width: 767.98px) {
    .main-content {
        margin-left: 0;
        width: 100%;
        padding-top: 70px; /* Espacio para el botón hamburguesa */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const body = document.body;
    
    // Estado del sidebar
    let sidebarOpen = false;
    
    // Función para abrir el sidebar
    function openSidebar() {
        sidebar.classList.add('show');
        sidebarOverlay.classList.add('show');
        hamburgerBtn.classList.add('active');
        body.classList.add('sidebar-open');
        hamburgerBtn.setAttribute('aria-label', 'Cerrar menú');
        sidebarOpen = true;
    }
    
    // Función para cerrar el sidebar
    function closeSidebar() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        hamburgerBtn.classList.remove('active');
        body.classList.remove('sidebar-open');
        hamburgerBtn.setAttribute('aria-label', 'Abrir menú');
        sidebarOpen = false;
    }
    
    // Función para alternar el sidebar
    function toggleSidebar() {
        if (sidebarOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }
    
    // Event Listeners
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', toggleSidebar);
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
    
    // Cerrar sidebar al hacer clic en enlaces (solo en mobile)
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Solo cerrar en mobile
            if (window.innerWidth < 768 && sidebarOpen) {
                closeSidebar();
            }
            
            // Manejar navegación para enlaces con #
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
                console.log('Funcionalidad en desarrollo:', this.textContent.trim());
            }
        });
    });
    
    // Cerrar sidebar con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebarOpen) {
            closeSidebar();
        }
    });
    
    // Manejar cambios de tamaño de pantalla
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            // En desktop, resetear estado
            closeSidebar();
        }
    });
    
    // Inicializar estado correcto al cargar la página
    function initializeSidebar() {
        if (window.innerWidth < 768) {
            closeSidebar();
        }
    }
    
    // Animaciones de entrada
    function animateNavItems() {
        const navItems = document.querySelectorAll('.sidebar-nav .nav-item');
        navItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
            item.classList.add('animate-slide-in');
        });
    }
    
    // Inicializar
    initializeSidebar();
    animateNavItems();
});
</script>