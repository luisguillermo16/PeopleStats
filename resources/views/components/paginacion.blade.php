@props(['collection'])

@if ($collection->hasPages())
    <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-center border-top mt-3">
        <small class="text-muted mb-2 mb-md-0">
            Mostrando {{ $collection->firstItem() }} - {{ $collection->lastItem() }} de {{ $collection->total() }}
        </small>

        {{-- Enlace con diseño Bootstrap --}}
        {{ $collection->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
    </div>
@endif
