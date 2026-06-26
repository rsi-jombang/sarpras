@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Penyesuaian Stock</h1>
                <p class="page-subtitle">Kelola penyesuaian stok manual dan opname</p>
            </div>
            @can('create', App\Models\StockAdjustment::class)
                <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Penyesuaian Baru
                </a>
            @endcan
        </div>
    </div>

    @if($message = session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($message = session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card-nexus">
        <div class="card-body-nexus">
            <table id="adjustmentsTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Stok Sebelum</th>
                        <th>Penyesuaian</th>
                        <th>Stok Sesudah</th>
                        <th>Alasan</th>
                        <th>Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#adjustmentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("api.stock-adjustments.index") }}',
            columns: [
                { data: 'created_at', render: function(data) {
                    return new Date(data).toLocaleDateString('id-ID', { 
                        year: 'numeric', month: 'short', day: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });
                }},
                { data: 'item_name' },
                { data: 'stock_before' },
                { data: 'quantity_adjustment', render: function(data) {
                    let cls = data > 0 ? 'text-success' : (data < 0 ? 'text-danger' : '');
                    return `<span class="${cls}">${data > 0 ? '+' : ''}${data}</span>`;
                }},
                { data: 'stock_after' },
                { data: 'reason' },
                { data: 'adjusted_by_name' },
                { data: 'action', orderable: false, searchable: false }
            ],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
            pageLength: 15
        });
    });
</script>
@endpush
@endsection
