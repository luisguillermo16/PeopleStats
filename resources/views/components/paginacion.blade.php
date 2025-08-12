@props(['collection', 'showInfo' => true, 'theme' => 'default'])

@if ($collection->hasPages())
    <div class="mobile-pagination-wrapper mobile-pagination-wrapper--{{ $theme }} rounded border" 
         role="navigation" 
         aria-label="Navegación de páginas">
        
        {{-- Información de resultados --}}
        @if($showInfo)
            <div class="pagination-info text-center mb-3" aria-live="polite">
                <small class="text-muted">
                    <span class="d-none d-sm-inline">Mostrando</span>
                    <strong>{{ number_format($collection->firstItem()) }}</strong>
                    -
                    <strong>{{ number_format($collection->lastItem()) }}</strong>
                    de
                    <strong>{{ number_format($collection->total()) }}</strong>
                    <span class="d-none d-sm-inline">resultados</span>
                </small>
            </div>
        @endif

        {{-- Paginación principal --}}
        <div class="mobile-pagination d-flex justify-content-center align-items-center">
            
            {{-- Botón primera página --}}
            @if($collection->currentPage() > 3)
                <a href="{{ $collection->url(1) }}" 
                   class="pagination-arrow pagination-arrow--first" 
                   title="Primera página"
                   aria-label="Ir a la primera página">
                    <i class="fas fa-angle-double-left" aria-hidden="true"></i>
                </a>
            @endif
            
            {{-- Botón anterior --}}
            @if (!$collection->onFirstPage())
                <a href="{{ $collection->previousPageUrl() }}" 
                   class="pagination-arrow pagination-arrow--prev"
                   title="Página anterior"
                   aria-label="Ir a la página anterior">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </a>
            @else
                <span class="pagination-arrow pagination-arrow--prev disabled"
                      aria-hidden="true">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </span>
            @endif

            {{-- Números de página con scroll horizontal mejorado --}}
            <div class="pagination-numbers-container">
                <div class="pagination-numbers" id="pagination-numbers">
                    @php
                        $currentPage = $collection->currentPage();
                        $lastPage = $collection->lastPage();
                        
                        // Lógica mejorada para mostrar páginas
                        if ($lastPage <= 7) {
                            // Mostrar todas las páginas si son pocas
                            $start = 1;
                            $end = $lastPage;
                        } else {
                            // Mostrar un rango dinámico centrado en la página actual
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                            
                            // Ajustar para mantener 5 páginas visibles cuando sea posible
                            if ($end - $start < 4) {
                                if ($start == 1) {
                                    $end = min($lastPage, $start + 4);
                                } elseif ($end == $lastPage) {
                                    $start = max(1, $end - 4);
                                }
                            }
                        }
                    @endphp

                    {{-- Primera página con elipsis --}}
                    @if ($start > 1)
                        <a href="{{ $collection->url(1) }}" 
                           class="page-number"
                           aria-label="Ir a la página 1">1</a>
                        @if ($start > 2)
                            <span class="page-dots" aria-hidden="true">
                                <i class="fas fa-ellipsis-h"></i>
                            </span>
                        @endif
                    @endif

                    {{-- Páginas en el rango visible --}}
                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $currentPage)
                            <span class="page-number active" 
                                  aria-current="page"
                                  aria-label="Página actual, página {{ $i }}">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $collection->url($i) }}" 
                               class="page-number"
                               aria-label="Ir a la página {{ $i }}">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    {{-- Última página con elipsis --}}
                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="page-dots" aria-hidden="true">
                                <i class="fas fa-ellipsis-h"></i>
                            </span>
                        @endif
                        <a href="{{ $collection->url($lastPage) }}" 
                           class="page-number"
                           aria-label="Ir a la página {{ $lastPage }}">
                            {{ $lastPage }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Botón siguiente --}}
            @if ($collection->hasMorePages())
                <a href="{{ $collection->nextPageUrl() }}" 
                   class="pagination-arrow pagination-arrow--next"
                   title="Página siguiente"
                   aria-label="Ir a la página siguiente">
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </a>
            @else
                <span class="pagination-arrow pagination-arrow--next disabled"
                      aria-hidden="true">
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </span>
            @endif

            {{-- Botón última página --}}
            @if($collection->currentPage() < $collection->lastPage() - 2)
                <a href="{{ $collection->url($collection->lastPage()) }}" 
                   class="pagination-arrow pagination-arrow--last"
                   title="Última página"
                   aria-label="Ir a la última página">
                    <i class="fas fa-angle-double-right" aria-hidden="true"></i>
                </a>
            @endif
        </div>


    </div>

    <style>
        /* Variables CSS para temas - Adaptado al dashboard */
        :root {
            --pagination-bg: white;
            --pagination-border: #e9ecef;
            --pagination-text: #495057;
            --pagination-text-muted: #6c757d;
            --pagination-active-bg: #2196f3;
            --pagination-active-border: #2196f3;
            --pagination-active-text: white;
            --pagination-hover-bg: #f8f9fa;
            --pagination-disabled-bg: #f8f9fa;
            --pagination-shadow: 0 2px 8px rgba(33, 150, 243, 0.1);
        }

        /* Tema oscuro */
        .mobile-pagination-wrapper--dark {
            --pagination-bg: #2d3748;
            --pagination-border: #4a5568;
            --pagination-text: #e2e8f0;
            --pagination-text-muted: #a0aec0;
            --pagination-active-bg: #4299e1;
            --pagination-active-border: #4299e1;
            --pagination-active-text: white;
            --pagination-hover-bg: #4a5568;
            --pagination-disabled-bg: #1a202c;
        }

        .mobile-pagination-wrapper {
            padding: 20px;
            background: var(--pagination-bg);
            border: 1px solid var(--pagination-border);
            border-radius: 12px;
            margin-top: 20px;
            box-shadow: var(--pagination-shadow);
            transition: all 0.3s ease;
        }

        .mobile-pagination {
            gap: 6px;
            position: relative;
        }

        .pagination-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: white;
            border: 1px solid var(--pagination-border);
            border-radius: 8px;
            color: var(--pagination-text);
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .pagination-arrow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.3s ease;
        }

        .pagination-arrow:hover:not(.disabled) {
            background: #e3f2fd;
            color: #2196f3;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.2);
        }

        .pagination-arrow:hover:not(.disabled)::before {
            left: 100%;
        }

        .pagination-arrow:active:not(.disabled) {
            transform: translateY(0);
        }

        .pagination-arrow.disabled {
            background: var(--pagination-disabled-bg);
            color: var(--pagination-text-muted);
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pagination-arrow--first,
        .pagination-arrow--last {
            display: none;
        }

        @media (min-width: 576px) {
            .pagination-arrow--first,
            .pagination-arrow--last {
                display: flex;
            }
        }

        .pagination-numbers-container {
            flex: 1;
            max-width: calc(100vw - 120px);
            overflow: hidden;
            position: relative;
        }

        @media (min-width: 576px) {
            .pagination-numbers-container {
                max-width: calc(100vw - 200px);
            }
        }

        .pagination-numbers {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 4px 8px;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: var(--pagination-border) transparent;
            scroll-behavior: smooth;
        }

        .pagination-numbers::-webkit-scrollbar {
            height: 4px;
        }

        .pagination-numbers::-webkit-scrollbar-track {
            background: transparent;
        }

        .pagination-numbers::-webkit-scrollbar-thumb {
            background: var(--pagination-border);
            border-radius: 2px;
        }

        .page-number {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 8px;
            background: white;
            border: 1px solid var(--pagination-border);
            border-radius: 6px;
            color: var(--pagination-text);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .page-number::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0,123,255,0.1), transparent);
            transition: left 0.3s ease;
        }

        .page-number:hover:not(.active) {
            background: #e3f2fd;
            color: #2196f3;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(33, 150, 243, 0.15);
        }

        .page-number:hover:not(.active)::before {
            left: 100%;
        }

        .page-number.active {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            border-color: var(--pagination-active-border);
            color: var(--pagination-active-text);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4);
            transform: scale(1.05);
        }

        .page-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            color: var(--pagination-text-muted);
            font-weight: bold;
            opacity: 0.7;
        }

        .pagination-info {
            color: var(--pagination-text-muted);
            font-size: 14px;
        }



        /* Estados de loading */
        .pagination-loading .page-number,
        .pagination-loading .pagination-arrow {
            pointer-events: none;
            opacity: 0.6;
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mobile-pagination-wrapper {
            animation: fadeInUp 0.3s ease;
        }

        /* Responsive adjustments mejoradas */
        @media (max-width: 576px) {
            .mobile-pagination-wrapper {
                padding: 12px;
                margin-top: 12px;
            }

            .mobile-pagination {
                gap: 4px;
            }

            .pagination-arrow {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .page-number {
                min-width: 32px;
                height: 32px;
                font-size: 14px;
                padding: 0 6px;
            }

            .pagination-info {
                font-size: 12px;
                margin-bottom: 8px !important;
            }

            .pagination-numbers-container {
                max-width: calc(100vw - 96px);
            }
        }

        /* Estados de focus mejorados para accesibilidad */
        .page-number:focus,
        .pagination-arrow:focus {
            outline: 2px solid #2196f3;
            outline-offset: 2px;
            z-index: 1;
        }

        /* Indicador de carga */
        .pagination-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            border-top-color: #2196f3;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Mejoras para modo reducido de movimiento */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>

    {{-- Script mejorado con más funcionalidades --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializePagination();
        });

        function initializePagination() {
            const activePageNumber = document.querySelector('.page-number.active');
            const numbersContainer = document.querySelector('.pagination-numbers');
            
            if (activePageNumber && numbersContainer) {
                centerActivePage();
            }

            // Agregar indicadores de scroll si es necesario
            addScrollIndicators();
            
            // Manejar navegación con teclado
            handleKeyboardNavigation();
        }

        function centerActivePage() {
            const activePageNumber = document.querySelector('.page-number.active');
            const numbersContainer = document.querySelector('.pagination-numbers');
            
            if (!activePageNumber || !numbersContainer) return;
            
            setTimeout(() => {
                const containerWidth = numbersContainer.offsetWidth;
                const activeLeft = activePageNumber.offsetLeft;
                const activeWidth = activePageNumber.offsetWidth;
                
                const scrollLeft = activeLeft - (containerWidth / 2) + (activeWidth / 2);
                
                numbersContainer.scrollTo({
                    left: Math.max(0, scrollLeft),
                    behavior: 'smooth'
                });
            }, 100);
        }

        function addScrollIndicators() {
            const container = document.querySelector('.pagination-numbers');
            if (!container) return;

            const wrapper = container.parentElement;
            
            // Crear indicadores
            const leftIndicator = document.createElement('div');
            const rightIndicator = document.createElement('div');
            
            leftIndicator.className = 'scroll-indicator scroll-indicator--left';
            rightIndicator.className = 'scroll-indicator scroll-indicator--right';
            
            leftIndicator.innerHTML = '<i class="fas fa-chevron-left"></i>';
            rightIndicator.innerHTML = '<i class="fas fa-chevron-right"></i>';
            
            wrapper.appendChild(leftIndicator);
            wrapper.appendChild(rightIndicator);

            // Función para actualizar visibilidad de indicadores
            function updateIndicators() {
                const canScrollLeft = container.scrollLeft > 0;
                const canScrollRight = container.scrollLeft < (container.scrollWidth - container.clientWidth);
                
                leftIndicator.style.opacity = canScrollLeft ? '1' : '0';
                rightIndicator.style.opacity = canScrollRight ? '1' : '0';
            }

            container.addEventListener('scroll', updateIndicators);
            updateIndicators();

            // Manejar clicks en indicadores
            leftIndicator.addEventListener('click', () => {
                container.scrollBy({ left: -100, behavior: 'smooth' });
            });

            rightIndicator.addEventListener('click', () => {
                container.scrollBy({ left: 100, behavior: 'smooth' });
            });
        }

        function handleKeyboardNavigation() {
            document.addEventListener('keydown', function(e) {
                if (e.target.matches('input')) return;

                const currentPage = parseInt(document.querySelector('.page-number.active')?.textContent);
                const prevLink = document.querySelector('.pagination-arrow--prev:not(.disabled)');
                const nextLink = document.querySelector('.pagination-arrow--next:not(.disabled)');

                switch(e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        if (prevLink) prevLink.click();
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        if (nextLink) nextLink.click();
                        break;
                    case 'Home':
                        e.preventDefault();
                        const firstLink = document.querySelector('a[href*="page=1"]');
                        if (firstLink) firstLink.click();
                        break;
                    case 'End':
                        e.preventDefault();
                        const lastLink = document.querySelector('.pagination-arrow--last');
                        if (lastLink) lastLink.click();
                        break;
                }
            });
        }

        // Función para mostrar estado de carga
        function showLoadingState() {
            const wrapper = document.querySelector('.mobile-pagination-wrapper');
            if (wrapper) {
                wrapper.classList.add('pagination-loading');
            }
        }

        // Auto-ocultar loading state cuando la página carga
        window.addEventListener('pageshow', function() {
            const wrapper = document.querySelector('.mobile-pagination-wrapper');
            if (wrapper) {
                wrapper.classList.remove('pagination-loading');
            }
        });
    </script>

    {{-- Estilos adicionales para indicadores de scroll --}}
    <style>
        .pagination-numbers-container {
            position: relative;
        }

        .scroll-indicator {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--pagination-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 2;
            backdrop-filter: blur(4px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .scroll-indicator--left {
            left: 4px;
        }

        .scroll-indicator--right {
            right: 4px;
        }

        .scroll-indicator:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
        }

        @media (max-width: 576px) {
            .scroll-indicator {
                width: 20px;
                height: 20px;
                font-size: 12px;
            }
        }
    </style>
@endif