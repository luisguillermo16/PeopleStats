@if ($rol === 'aspirante-alcaldia' || $rol === 'aspirante-concejo')
<div class="col-12 col-md-6 col-xl-4">
       <a href="{{ route('verVotantes') }}" class="text-decoration-none">
    <div class="card border-0 shadow-sm h-100 hover-shadow dashboard-card votantes-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 fw-semibold">
                                <span class="d-none d-lg-inline">Total de Votantes Únicos</span>
                                <span class="d-lg-none">Votantes Únicos</span>
                            </h6>
                            <small class="text-muted">Registrados en el sistema</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-end">
                        <h2 class="mb-0 fw-bold text-success counter" 
                            data-target="{{ $totalVotantes }}">0</h2>     
                    </div>
                </div>
            </div>
         <!-- 🔹 Texto "Ver más" al estilo View All -->
        <div class="text-end text-success counter">
            <span class="ver-mas">Ver más →</span>
        </div>
        </div>
    </div>
    </a>
</div>
@endif
@if ($rol === 'lider')
  <div class="col-12 col-md-6 col-xl-4">
    <a href="{{ route('ingresarVotantes') }}" class="text-decoration-none">
    <div class="card border-0 shadow-sm h-100 hover-shadow dashboard-card votantes-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 fw-semibold">
                                <span class="d-none d-lg-inline">Total de Votantes Únicos</span>
                                <span class="d-lg-none">Votantes Únicos</span>
                            </h6>
                            <small class="text-muted">Registrados en el sistema por ti</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-end">
                        <h2 class="mb-0 fw-bold text-success counter" 
                            data-target="{{ $totalVotantes }}">0</h2>
                            
                    </div>
                </div>
            </div>
            <div class="text-end text-success counter">
            <span class="ver-mas">Ver más →</span>
        </div>
        </div>
    </div>
    </a>
</div>
@endif
<style>
/* ESTILOS PARA CARD LÍDERES (WARNING/DORADO) */
.lideres-card.hover-shadow {
    border: 1px solid #e2e8f0 !important;
    border-radius: 18px !important;
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.98) !important;
    position: relative;
    overflow: hidden;
    animation: slideInFromRight 0.7s ease-out;
    animation-delay: 0.2s;
}

.lideres-card.hover-shadow::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #f59e0b, #f97316, #d97706);
    background-size: 200% 100%;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: gradientShift 3s ease-in-out infinite;
}

.lideres-card.hover-shadow:hover::before {
    opacity: 1;
}

.lideres-card.hover-shadow:hover {
    transform: translateY(-6px) scale(1.015);
    box-shadow: 0 20px 40px rgba(245, 158, 11, 0.15), 
                0 10px 20px rgba(217, 119, 6, 0.1) !important;
    border-color: #f59e0b !important;
}

.lideres-card .bg-warning.bg-opacity-10 {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.12) 0%, rgba(217, 119, 6, 0.08) 100%) !important;
    border: 2px solid rgba(245, 158, 11, 0.25);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lideres-card.hover-shadow:hover .bg-warning.bg-opacity-10 {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.18) 0%, rgba(217, 119, 6, 0.14) 100%) !important;
    transform: scale(1.12) rotate(-3deg);
    border-color: rgba(245, 158, 11, 0.4);
    box-shadow: 0 6px 16px rgba(245, 158, 11, 0.3);
}

/* ESTILOS PARA CARD CONCEJALES (PRIMARY/AZUL) */
.concejales-card.hover-shadow {
    border: 1px solid #e2e8f0 !important;
    border-radius: 18px !important;
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.98) !important;
    position: relative;
    overflow: hidden;
    animation: slideInFromRight 0.7s ease-out;
    animation-delay: 0.4s;
}

.concejales-card.hover-shadow::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0d6efd, #0b5ed7, #0a58ca);
    background-size: 200% 100%;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: gradientShift 3s ease-in-out infinite;
}

.concejales-card.hover-shadow:hover::before {
    opacity: 1;
}

.concejales-card.hover-shadow:hover {
    transform: translateY(-6px) scale(1.015);
    box-shadow: 0 20px 40px rgba(13, 110, 253, 0.15), 
                0 10px 20px rgba(10, 88, 202, 0.1) !important;
    border-color: #0d6efd !important;
}

.concejales-card .bg-primary.bg-opacity-10 {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.12) 0%, rgba(10, 88, 202, 0.08) 100%) !important;
    border: 2px solid rgba(13, 110, 253, 0.25);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.concejales-card.hover-shadow:hover .bg-primary.bg-opacity-10 {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.18) 0%, rgba(10, 88, 202, 0.14) 100%) !important;
    transform: scale(1.12) rotate(-3deg);
    border-color: rgba(13, 110, 253, 0.4);
    box-shadow: 0 6px 16px rgba(13, 110, 253, 0.3);
}

/* ESTILOS PARA CARD VOTANTES (SUCCESS/VERDE) */
.votantes-card.hover-shadow {
    border: 1px solid #e2e8f0 !important;
    border-radius: 18px !important;
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.98) !important;
    position: relative;
    overflow: hidden;
    animation: slideInFromRight 0.7s ease-out;
    animation-delay: 0.6s;
}

.votantes-card.hover-shadow::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #198754, #157347, #146c43);
    background-size: 200% 100%;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: gradientShift 3s ease-in-out infinite;
}

.votantes-card.hover-shadow:hover::before {
    opacity: 1;
}

.votantes-card.hover-shadow:hover {
    transform: translateY(-6px) scale(1.015);
    box-shadow: 0 20px 40px rgba(25, 135, 84, 0.15), 
                0 10px 20px rgba(20, 108, 67, 0.1) !important;
    border-color: #198754 !important;
}

.votantes-card .bg-success.bg-opacity-10 {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.12) 0%, rgba(20, 108, 67, 0.08) 100%) !important;
    border: 2px solid rgba(25, 135, 84, 0.25);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.votantes-card.hover-shadow:hover .bg-success.bg-opacity-10 {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.18) 0%, rgba(20, 108, 67, 0.14) 100%) !important;
    transform: scale(1.12) rotate(-3deg);
    border-color: rgba(25, 135, 84, 0.4);
    box-shadow: 0 6px 16px rgba(25, 135, 84, 0.3);
}

/* ESTILOS COMUNES PARA TODAS LAS CARDS */
.dashboard-card .card-title {
    color: #1e293b !important;
    font-weight: 600 !important;
    font-size: 15px !important;
    letter-spacing: -0.025em;
    transition: color 0.3s ease;
}

.dashboard-card .text-muted {
    color: #64748b !important;
    font-size: 12px !important;
    font-weight: 400 !important;
    transition: color 0.3s ease;
}

.dashboard-card .fw-bold {
    font-size: 2.5rem !important;
    font-weight: 800 !important;
    line-height: 1;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.4s ease;
}

.dashboard-card.hover-shadow:hover .fw-bold {
    transform: scale(1.08);
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.dashboard-card .bi {
    transition: all 0.4s ease;
    position: relative;
    z-index: 2;
}

.dashboard-card.hover-shadow:hover .bi {
    transform: scale(1.15) rotate(5deg);
    filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.2));
}

/* ANIMACIONES */
@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes slideInFromRight {
    from {
        opacity: 0;
        transform: translateX(30px) rotateY(10deg);
    }
    to {
        opacity: 1;
        transform: translateX(0) rotateY(0deg);
    }
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .dashboard-card .fw-bold {
        font-size: 2.1rem !important;
    }
    
    .dashboard-card .bg-warning.bg-opacity-10,
    .dashboard-card .bg-primary.bg-opacity-10,
    .dashboard-card .bg-success.bg-opacity-10 {
        width: 46px;
        height: 46px;
    }
    
    .dashboard-card.hover-shadow:hover {
        transform: translateY(-4px) scale(1.01);
    }
}

@media (max-width: 576px) {
    .dashboard-card .fw-bold {
        font-size: 1.85rem !important;
    }
}
</style>
   <script>
        // Función mejorada para animar contadores
        function animateCounter(element, target, duration = 2000) {
            const start = 0;
            const startTime = performance.now();
            
            // Función de easing para suavizar la animación
            function easeOutQuart(t) {
                return 1 - (--t) * t * t * t;
            }

            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Aplicar easing
                const easedProgress = easeOutQuart(progress);
                const current = Math.floor(start + (target - start) * easedProgress);
                
                // Formatear número con comas
                element.textContent = current.toLocaleString();
                
                // Agregar clase de pulso ocasionalmente durante la animación
                if (progress > 0.3 && progress < 0.7 && Math.random() < 0.1) {
                    element.classList.add('number-pulse');
                    setTimeout(() => element.classList.remove('number-pulse'), 600);
                }
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    // Pulso final cuando termina la animación
                    element.classList.add('number-pulse');
                    setTimeout(() => element.classList.remove('number-pulse'), 600);
                }
            }
            
            requestAnimationFrame(updateCounter);
        }

        // Función para animar barras de progreso
        function animateProgressBars() {
            const progressBars = document.querySelectorAll('.dashboard-card .progress-bar');
            
            progressBars.forEach((bar, index) => {
                const targetWidth = bar.style.width;
                bar.style.width = '0%';
                bar.style.transition = 'none';
                
                setTimeout(() => {
                    bar.style.transition = 'width 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    bar.style.width = targetWidth;
                }, 1000 + (index * 300));
            });
        }

        // Función para inicializar todas las animaciones
        function initializeAnimations() {
            // Animar números
            const numberElements = document.querySelectorAll('.dashboard-card .fw-bold[data-target]');
            
            numberElements.forEach((element, index) => {
                const targetNumber = parseInt(element.getAttribute('data-target'));
                element.textContent = '0';
                
                setTimeout(() => {
                    animateCounter(element, targetNumber, 1800 + (index * 200));
                }, 800 + (index * 200));
            });
            
            // Animar barras de progreso
            animateProgressBars();
        }

        // Función para reiniciar animaciones
        function restartAnimations() {
            // Reiniciar números
            const numberElements = document.querySelectorAll('.dashboard-card .fw-bold[data-target]');
            numberElements.forEach(element => {
                element.textContent = '0';
                element.classList.remove('number-pulse');
            });
            
            // Reiniciar barras
            const progressBars = document.querySelectorAll('.dashboard-card .progress-bar');
            progressBars.forEach(bar => {
                bar.style.width = '0%';
                bar.style.transition = 'none';
            });
            
            // Reiniciar animaciones después de un breve delay
            setTimeout(initializeAnimations, 200);
        }

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeAnimations, 1200);
        });

        // Opcional: Animar cuando las cards entren en el viewport
        function observeCards() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const numberEl = entry.target.querySelector('.fw-bold[data-target]');
                        if (numberEl && numberEl.textContent === '0') {
                            const target = parseInt(numberEl.getAttribute('data-target'));
                            setTimeout(() => animateCounter(numberEl, target), 300);
                        }
                    }
                });
            }, { threshold: 0.5 });

            document.querySelectorAll('.dashboard-card').forEach(card => {
                observer.observe(card);
            });
        }

        // Activar observador (opcional)
        // observeCards();
    </script>
