@props(['collection'])

@if ($collection->hasPages())
    <div class="mobile-pagination-wrapper rounded border">
        
        {{-- Información de resultados --}}
        <div class="pagination-info text-center mb-3">
            <small class="text-muted">
                Mostrando {{ $collection->firstItem() }} - {{ $collection->lastItem() }} de {{ $collection->total() }}
            </small>
        </div>

        {{-- Paginación horizontal estilo móvil --}}
        <div class="mobile-pagination  d-flex justify-content-center align-items-center">
            
            {{-- Botón anterior --}}
            @if (!$collection->onFirstPage())
                <a href="{{ $collection->previousPageUrl() }}" class="pagination-arrow">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @else
                <span class="pagination-arrow disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @endif

            {{-- Números de página en scroll horizontal --}}
            <div class="pagination-numbers-container">
                <div class="pagination-numbers">
                    @php
                        $start = max(1, $collection->currentPage() - 2);
                        $end = min($collection->lastPage(), $collection->currentPage() + 2);
                        
                        // Asegurar que siempre mostremos al menos 5 páginas si es posible
                        if ($end - $start < 4) {
                            if ($start == 1) {
                                $end = min($collection->lastPage(), $start + 4);
                            } else {
                                $start = max(1, $end - 4);
                            }
                        }
                    @endphp

                    {{-- Primera página si no está en el rango --}}
                    @if ($start > 1)
                        <a href="{{ $collection->url(1) }}" class="page-number">1</a>
                        @if ($start > 2)
                            <span class="page-dots">...</span>
                        @endif
                    @endif

                    {{-- Páginas en el rango --}}
                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $collection->currentPage())
                            <span class="page-number active">{{ $i }}</span>
                        @else
                            <a href="{{ $collection->url($i) }}" class="page-number">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Última página si no está en el rango --}}
                    @if ($end < $collection->lastPage())
                        @if ($end < $collection->lastPage() - 1)
                            <span class="page-dots">...</span>
                        @endif
                        <a href="{{ $collection->url($collection->lastPage()) }}" class="page-number">{{ $collection->lastPage() }}</a>
                    @endif
                </div>
            </div>

            {{-- Botón siguiente --}}
            @if ($collection->hasMorePages())
                <a href="{{ $collection->nextPageUrl() }}" class="pagination-arrow">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-arrow disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>

    <style>
        .mobile-pagination-wrapper {
            padding: 16px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            margin-top: 16px;
        }

        .mobile-pagination {
            gap: 8px;
        }

        .pagination-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            color: #495057;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination-arrow:hover:not(.disabled) {
            background: #e9ecef;
            color: #495057;
            text-decoration: none;
            transform: scale(1.05);
        }

        .pagination-arrow.disabled {
            background: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }

        .pagination-numbers-container {
            flex: 1;
            max-width: calc(100vw - 120px);
            overflow: hidden;
        }

        .pagination-numbers {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 0 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .pagination-numbers::-webkit-scrollbar {
            display: none;
        }

        .page-number {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 8px;
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            color: #495057;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .page-number:hover:not(.active) {
            background: #e9ecef;
            color: #495057;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .page-number.active {
            background: #007bff;
            border-color: #007bff;
            color: white;
            font-weight: 600;
        }

        .page-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            color: #6c757d;
            font-weight: bold;
        }

        .pagination-info {
            color: #6c757d;
            font-size: 14px;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .mobile-pagination-wrapper {
                padding: 12px;
            }

            .pagination-arrow {
                width: 36px;
                height: 36px;
            }

            .page-number {
                min-width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .pagination-info {
                font-size: 12px;
            }

            .pagination-numbers-container {
                max-width: calc(100vw - 100px);
            }
        }

        /* Smooth scroll behavior */
        .pagination-numbers {
            scroll-behavior: smooth;
        }

        /* Focus states for accessibility */
        .page-number:focus,
        .pagination-arrow:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }
    </style>

    {{-- Script para centrar la página activa en móvil --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const activePageNumber = document.querySelector('.page-number.active');
            const numbersContainer = document.querySelector('.pagination-numbers');
            
            if (activePageNumber && numbersContainer) {
                const containerWidth = numbersContainer.offsetWidth;
                const activeLeft = activePageNumber.offsetLeft;
                const activeWidth = activePageNumber.offsetWidth;
                
                const scrollLeft = activeLeft - (containerWidth / 2) + (activeWidth / 2);
                numbersContainer.scrollLeft = scrollLeft;
            }
        });
    </script>
@endif