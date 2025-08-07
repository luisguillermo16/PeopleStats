<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Sistema de Votación</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js"></script>
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

        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .main-content {
            padding: 20px;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
        }

        .stats-card.primary::before {
            --card-color: var(--primary-color);
            --card-color-light: #a78bfa;
        }

        .stats-card.success::before {
            --card-color: var(--success-color);
            --card-color-light: #86efac;
        }

        .stats-card.warning::before {
            --card-color: var(--warning-color);
            --card-color-light: #fde047;
        }

        .stats-card.info::before {
            --card-color: var(--info-color);
            --card-color-light: #93c5fd;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .progress-custom {
            height: 8px;
            border-radius: 10px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .progress-bar-custom {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, var(--progress-color), var(--progress-color-light));
            transition: width 1s ease-in-out;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            height: 400px;
        }

        .action-btn {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            padding: 20px;
            text-decoration: none;
            color: #374151;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 140px;
        }

        .action-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
        }

        .action-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .alert-modern {
            border: none;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .user-info {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            color: white;
        }

        @media (max-width: 768px) {
            .stats-number {
                font-size: 2rem;
            }
            
            .stats-card {
                padding: 20px;
            }
            
            .chart-container {
                height: 300px;
                padding: 20px;
            }
        }

        .animate-counter {
            animation: countUp 2s ease-out;
        }

        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="p-3">
                    <div class="user-info text-center">
                        <i class="bi bi-person-circle fs-1"></i>
                        <div class="mt-2">
                            <strong>alcalde de prueba</strong>
                            <div class="small">aspirante-alcaldía</div>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-success">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                6 permisos activos
                            </span>
                        </div>
                    </div>
                    
                    <nav class="nav flex-column mt-4">
                        <a class="nav-link active" href="#dashboard">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="#concejales">
                            <i class="bi bi-people me-2"></i>Crear Concejales
                        </a>
                        <a class="nav-link" href="#votantes">
                            <i class="bi bi-person-check me-2"></i>Ver Votantes
                        </a>
                        <a class="nav-link" href="#puntos">
                            <i class="bi bi-geo-alt me-2"></i>Crear Puntos de Votación
                        </a>
                        <a class="nav-link" href="#barrios">
                            <i class="bi bi-building me-2"></i>Crear Barrios
                        </a>
                        <a class="nav-link" href="#lideres">
                            <i class="bi bi-star me-2"></i>Crear Líderes
                        </a>
                    </nav>
                    
                    <div class="mt-auto pt-3">
                        <button class="btn btn-danger w-100" onclick="logout()">
                            <i class="bi bi-box-arrow-right me-2"></i>Salir
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">Panel de Control</h1>
                        <p class="text-muted mb-0">Bienvenido a tu dashboard de gestión electoral</p>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">Última actualización</div>
                        <div class="fw-bold">7/08/2025 2:14 p.m.</div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="stats-card primary">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-grow-1">
                                    <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Total de Votantes Únicos</h6>
                                    <div class="stats-number animate-counter">63</div>
                                    <div class="small text-muted mb-2">Registrados en el sistema</div>
                                    <div class="progress-custom">
                                        <div class="progress-bar-custom" style="width: 42%; --progress-color: var(--primary-color); --progress-color-light: #a78bfa;"></div>
                                    </div>
                                    <div class="small text-muted mt-1">0.42% del objetivo alcanzado</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="stats-card info">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-grow-1">
                                    <div class="stats-icon bg-info bg-opacity-10 text-info">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Total de Concejales</h6>
                                    <div class="stats-number animate-counter">8</div>
                                    <div class="small text-muted mb-2">Activos en el sistema</div>
                                    <div class="progress-custom">
                                        <div class="progress-bar-custom" style="width: 80%; --progress-color: var(--info-color); --progress-color-light: #93c5fd;"></div>
                                    </div>
                                    <div class="small text-success mt-1">
                                        <i class="bi bi-arrow-up me-1"></i>+2 este mes
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="stats-card warning">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-grow-1">
                                    <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                                        <i class="bi bi-star"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Total de Líderes</h6>
                                    <div class="stats-number animate-counter">12</div>
                                    <div class="small text-muted mb-2">Activos en el sistema</div>
                                    <div class="progress-custom">
                                        <div class="progress-bar-custom" style="width: 75%; --progress-color: var(--warning-color); --progress-color-light: #fde047;"></div>
                                    </div>
                                    <div class="small text-success mt-1">
                                        <i class="bi bi-arrow-up me-1"></i>+1 esta semana
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="chart-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="bi bi-pie-chart me-2"></i>
                                    Distribución por Barrio
                                </h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Exportar</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i>Imprimir</a></li>
                                    </ul>
                                </div>
                            </div>
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="chart-container">
                            <h5 class="mb-3">
                                <i class="bi bi-graph-up me-2"></i>
                                Tendencia Semanal
                            </h5>
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightning-charge me-2"></i>
                            Acciones Rápidas
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <a href="#" class="action-btn" onclick="quickAction('votantes')">
                                    <i class="bi bi-people text-success"></i>
                                    <span class="fw-semibold">Gestionar Votantes</span>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="#" class="action-btn" onclick="quickAction('concejales')">
                                    <i class="bi bi-person-badge text-info"></i>
                                    <span class="fw-semibold">Ver Concejales</span>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="#" class="action-btn" onclick="quickAction('lideres')">
                                    <i class="bi bi-star text-warning"></i>
                                    <span class="fw-semibold">Gestionar Líderes</span>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="#" class="action-btn" onclick="quickAction('reportes')">
                                    <i class="bi bi-bar-chart-line text-primary"></i>
                                    <span class="fw-semibold">Ver Reportes</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Datos para las gráficas
        const barrios = ['Guayabal', 'Punta Seca (Santa Fe)', 'Boca de la Ciénaga', 'Isla de gallinazo'];
        const votantesPorBarrio = [25, 18, 12, 8]; // Ejemplo basado en el gráfico
        const colores = ['#ef4444', '#3b82f6', '#f59e0b', '#10b981'];

        // Gráfico de distribución por barrio
        const ctx1 = document.getElementById('distributionChart').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: barrios,
                datasets: [{
                    data: votantesPorBarrio,
                    backgroundColor: colores,
                    borderWidth: 0,
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    duration: 2000
                }
            }
        });

        // Gráfico de tendencia
        const ctx2 = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Nuevos Registros',
                    data: [12, 8, 15, 10, 18, 5, 7],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e5e7eb',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // Funciones de interacción
        function quickAction(action) {
            const actions = {
                votantes: 'Redirigiendo a gestión de votantes...',
                concejales: 'Abriendo lista de concejales...',
                lideres: 'Cargando gestión de líderes...',
                reportes: 'Generando reportes...'
            };
            
            // Mostrar notificación
            showNotification(actions[action], 'info');
            
            // Simular navegación
            setTimeout(() => {
                console.log(`Navegando a: ${action}`);
            }, 1500);
        }

        function logout() {
            if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                showNotification('Cerrando sesión...', 'info');
                setTimeout(() => {
                    console.log('Logout');
                }, 1500);
            }
        }

        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-success',
                error: 'bg-danger',
                warning: 'bg-warning',
                info: 'bg-info'
            };

            const notification = document.createElement('div');
            notification.className = `alert ${colors[type]} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="bi bi-info-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Animación de números al cargar
        function animateCounters() {
            const counters = document.querySelectorAll('.stats-number');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                const increment = target / 50;
                let current = 0;
                
                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        counter.textContent = Math.floor(current);
                        setTimeout(updateCounter, 40);
                    } else {
                        counter.textContent = target;
                    }
                };
                
                updateCounter();
            });
        }

        // Inicializar animaciones al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(animateCounters, 500);
        });
    </script>
</body>
</html>