@extends('layouts.admin')

@section('tituloPage', 'Gestión de Votantes')

@section('contenido')

{{-- Barra superior de búsqueda y acciones --}}
<div class="mb-3">
    <div class="p-3 p-md-4 border bg-light rounded w-100">
        <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 justify-content-between">
            
            {{-- Bloque de búsqueda --}}
            <form method="GET" action="{{ url()->current() }}" class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 flex-grow-1">
                
                <!-- Campo búsqueda -->
                <div class="input-group" style="max-width: min(100%, 320px);">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           class="form-control border-start-0"
                           placeholder="Buscar por nombre o cédula..."
                           name="search"
                           value="{{ request('search') }}">
                </div>

                <!-- Botones búsqueda -->
                <div class="d-flex gap-2" style="min-width: fit-content;">
                    <button class="btn btn-outline-primary btn-sm" type="submit" style="min-width: 80px;">
                        <i class="bi bi-search me-1"></i> 
                        <span class="d-none d-sm-inline">Buscar</span>
                    </button>

                    <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-sm" style="min-width: 80px;">
                        <i class="bi bi-x-circle me-1"></i> 
                        <span class="d-none d-sm-inline">Limpiar</span>
                    </a>
                </div>
            </form>

            {{-- Separador visual en móvil --}}
            <div class="d-lg-none border-top pt-3 mt-0"></div>

            {{-- Botones de acciones --}}
            <div class="d-flex flex-column flex-sm-row align-items-stretch gap-2" style="min-width: fit-content;">
                
                {{-- Importar Excel --}}
                <form id="importForm"
                      action="{{ route('votantes.import') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="d-flex flex-column flex-sm-row gap-2 align-items-stretch">
                    @csrf
                    <input type="file"
                           id="excel_file"
                           name="excel_file"
                           class="d-none"
                           accept=".xlsx,.xls"
                           required
                           onchange="updateImportButton(this)">

                    <button type="button"
                            class="btn btn-outline-success"
                            onclick="document.getElementById('excel_file').click()"
                            style="white-space: nowrap;">
                        <i class="bi bi-file-earmark-excel me-1"></i> 
                        <span class="d-inline d-md-none d-xl-inline">Seleccionar Excel</span>
                        <span class="d-none d-md-inline d-xl-none">Seleccionar</span>
                    </button>

                    <button type="submit"
                            class="btn btn-success"
                            id="importBtn"
                            disabled
                            style="white-space: nowrap;">
                        <i class="bi bi-upload me-1"></i> Importar
                    </button>
                </form>

                {{-- Nuevo Votante --}}
                <button class="btn btn-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#createModal"
                        style="white-space: nowrap;">
                    <i class="bi bi-plus-circle me-1"></i> 
                    <span class="d-inline d-md-none d-xl-inline">Nuevo Votante</span>
                    <span class="d-none d-md-inline d-xl-none">Nuevo</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="table-responsive">
    <table class="table rounded border shadow-sm overflow-hidden">
        <thead>
            <tr>
                <th></th>
                <th>Nombre</th>
                <th class="d-none d-md-table-cell">Cédula</th>
                <th class="d-none d-lg-table-cell">Teléfono</th>
                <th class="d-none d-lg-table-cell">Barrio</th>
                <th class="d-none d-lg-table-cell">Lugar de Votación</th>
                <th class="d-none d-lg-table-cell">Mesa</th>
                <th class="d-none d-lg-table-cell">
                    @if(is_null($lider->concejal_id)) ¿Vota Concejal? @else ¿Vota Alcalde? @endif
                </th>
                <th width="150">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($votantes as $votante)
                <tr>
                    <td></td>
                    <td>
                        <div class="fw-semibold">{{ $votante->nombre }}</div>
                        <div class="d-md-none"><small class="text-muted">{{ $votante->telefono }}</small></div>
                    </td>
                    <td class="d-none d-md-table-cell">{{ $votante->cedula }}</td>
                    <td class="d-none d-lg-table-cell">{{ $votante->telefono ?? 'Sin asignar' }}</td>
                    <td class="d-none d-lg-table-cell">{{ $votante->barrio->nombre ?? 'Sin asignar' }}</td>
                    <td class="d-none d-lg-table-cell">{{ $votante->lugarVotacion->nombre ?? 'Sin asignar' }}</td>
                    <td class="d-none d-lg-table-cell">
                        {{ $votante->mesa ? 'Mesa ' . $votante->mesa->numero : 'Sin asignar' }}
                    </td>
                    <td class="d-none d-lg-table-cell">
                        @if(is_null($lider->concejal_id))
                            {{ $votante->concejal?->name ?? $votante->concejal?->user?->name ?? 'No' }}
                        @else
                            {{ $votante->alcalde_id ? 'Sí' : 'No' }}
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $votante->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $votante->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center py-4">No hay votantes registrados</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<x-paginacion :collection="$votantes" />

{{-- Loading Overlay Simplificado --}}
<x-loading-overlay />

{{-- Modal Crear --}}
<x-modal-crear-votante
    :barrios="$barrios"
    :lugares="$lugares"
    :concejal-opciones="$concejalOpciones"
    :lider="$lider"
/>

@foreach ($votantes as $votante)
    <x-modal-editar-votante 
        :votante="$votante"
        :barrios="$barrios"
        :lugares="$lugares"
        :concejal-opciones="$concejalOpciones"
        :lider="$lider"
    />
@endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------
    // Overlay de carga para importación
    // ----------------------------
    let progressInterval;
    const loadingOverlay = document.getElementById('loadingOverlay');
    const progressBar = document.getElementById('progressBar');

    const showLoading = () => {
        loadingOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        progressBar.style.width = '0%';
        startProgressAnimation();
    };

    const startProgressAnimation = () => {
        let progress = 0;
        progressInterval = setInterval(() => {
            progress += Math.random() * 8 + 2;
            if (progress > 95) progress = 95;
            progressBar.style.width = progress + "%";
        }, 600);
    };

    const hideLoading = () => {
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 1000);
    };

    // ----------------------------
    // Botón de Importar
    // ----------------------------
    const fileInput = document.getElementById('excel_file');
    const importBtn = document.getElementById('importBtn');

    const updateImportButton = (input) => {
        if (input.files.length > 0) {
            const fileName = input.files[0].name;
            importBtn.disabled = false;
            importBtn.innerHTML = `<i class="bi bi-upload me-1"></i> Importar ${fileName.length > 15 ? fileName.substring(0, 15) + '...' : fileName}`;
        } else {
            importBtn.disabled = true;
            importBtn.innerHTML = '<i class="bi bi-upload me-1"></i> Importar';
        }
    };

    if (fileInput && importBtn) {
        fileInput.addEventListener('change', function() { updateImportButton(this); });
    }

    document.getElementById('importForm')?.addEventListener('submit', function(e) {
        if (!fileInput.files.length) {
            e.preventDefault();
            return;
        }
        showLoading();
    });

    window.updateImportButton = updateImportButton; // global por si lo necesitas

    // ----------------------------
    // Checkbox seleccionar todos
    // ----------------------------
    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // ----------------------------
    // Confirmación antes de eliminar
    // ----------------------------
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // ----------------------------
    // SweetAlert global (éxito, error, validación)
    // ----------------------------
    @if(session('success'))
        Swal.fire({ icon: 'success', title: '¡Éxito!', text: '{{ session('success') }}', confirmButtonColor: '#3085d6' });
    @endif

    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Error', text: '{{ session('error') }}', confirmButtonColor: '#d33' });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#d33'
        });
    @endif

    // ----------------------------
    // Mostrar resultados de importación
    // ----------------------------
    try {
        const data = @json(session('import_result'));
        if (data && (data.importados?.length || data.errores?.length)) {
            if (loadingOverlay.style.display === 'flex') hideLoading();
            setTimeout(() => mostrarResultadosImportacion(data), 1200);
        }
    } catch (error) {
        console.error('Error al procesar resultados:', error);
        if (loadingOverlay.style.display === 'flex') hideLoading();
        Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un problema al mostrar los resultados de la importación.', confirmButtonColor: '#d33' });
    }

    function mostrarResultadosImportacion(data) {
        const { importados = [], errores = [] } = data;
        const total = importados.length + errores.length;
        const successRate = total > 0 ? Math.round((importados.length / total) * 100) : 0;

        let htmlContent = '';
        if (total > 0) htmlContent += `<div style="background:#e9ecef;padding:15px;border-radius:8px;margin-bottom:20px;text-align:center;border:1px solid #dee2e6;">
            <div><strong>Total procesado:</strong> <span style="color:#007bff;font-weight:bold;">${total}</span></div>
            <div><strong style="color:#28a745;">Tasa de éxito:</strong> <span style="color:#28a745;font-weight:bold;">${successRate}%</span></div>
        </div>`;

        if (importados.length) {
            htmlContent += `<h5 style="color:#28a745;margin:20px 0 12px 0;display:flex;align-items:center;font-weight:600;">
                <span style="margin-right:8px;">✅</span> Importados Exitosamente (${importados.length})</h5>
                <div style="max-height:180px;overflow-y:auto;background:#d4edda;padding:12px;border-radius:6px;margin-bottom:20px;border:1px solid #c3e6cb;">
                <ul style="margin:0;padding-left:18px;">${importados.map(i=>`<li>${i}</li>`).join('')}</ul></div>`;
        }

        if (errores.length) {
            const duplicados = errores.filter(e => e.includes('Ya fue registrada en esta campaña'));
            const otrosErrores = errores.filter(e => !e.includes('Ya fue registrada en esta campaña'));

            if (duplicados.length) htmlContent += `<h5 style="color:#ffc107;margin:20px 0 12px 0;"><span style="margin-right:8px;">⚠️</span> Votantes Duplicados (${duplicados.length})</h5>
                <div style="max-height:150px;overflow-y:auto;background:#fff3cd;padding:12px;border-radius:6px;margin-bottom:15px;border:1px solid #ffeaa7;">
                <ul style="margin:0;padding-left:18px;">${duplicados.map(e=>`<li>${e}</li>`).join('')}</ul></div>`;

            if (otrosErrores.length) htmlContent += `<h5 style="color:#dc3545;margin:20px 0 12px 0;"><span style="margin-right:8px;">❌</span> Otros Errores (${otrosErrores.length})</h5>
                <div style="max-height:150px;overflow-y:auto;background:#f8d7da;padding:12px;border-radius:6px;border:1px solid #f1b2b7;">
                <ul style="margin:0;padding-left:18px;">${otrosErrores.map(e=>`<li>${e}</li>`).join('')}</ul></div>`;
        }

        const icon = errores.length && importados.length ? 'warning' : errores.length ? 'error' : 'success';
        const title = errores.length && importados.length ? 'Importación Completada con Advertencias' : errores.length ? 'Importación Fallida' : '¡Importación Exitosa!';

        Swal.fire({
            icon, title, html: htmlContent, width: 800,
            confirmButtonText: 'Cerrar',
            confirmButtonColor: icon === 'success' ? '#28a745' : (icon === 'warning' ? '#ffc107' : '#dc3545')
        });
    }

    // ----------------------------
    // Limpiar backdrop al cerrar modales
    // ----------------------------
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', () => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    });
});
</script>
@endpush
