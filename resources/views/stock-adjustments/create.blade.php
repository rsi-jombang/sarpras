@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="page-header">
        <h1 class="page-title">Penyesuaian Stock Baru</h1>
        <p class="page-subtitle">Buat penyesuaian stok manual</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> Terjadi kesalahan:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($message = session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card-nexus">
                <div class="card-header-nexus">
                    <h5 class="card-title">Detail Penyesuaian</h5>
                </div>
                <div class="card-body-nexus">
                    <form action="{{ route('stock-adjustments.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="item_id" class="form-label">Item <span class="text-danger">*</span></label>
                            <select id="item_id" name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Item --</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                                        {{ $item->name }} (Stok: <span class="item-stock" data-item-id="{{ $item->id }}">{{ $item->total_stock ?? 0 }}</span>)
                                    </option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_stock" class="form-label">Stok Saat Ini</label>
                                    <input type="number" id="current_stock" class="form-control" readonly value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity_adjustment" class="form-label">Penyesuaian <span class="text-danger">*</span></label>
                                    <input type="number" id="quantity_adjustment" name="quantity_adjustment" class="form-control @error('quantity_adjustment') is-invalid @enderror" 
                                        value="{{ old('quantity_adjustment') }}" placeholder="Contoh: 5 atau -3" required>
                                    @error('quantity_adjustment')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_stock" class="form-label">Stok Setelah Penyesuaian</label>
                            <input type="number" id="new_stock" class="form-control" readonly value="0">
                            <small class="text-muted">Akan otomatis dihitung</small>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Alasan Penyesuaian <span class="text-danger">*</span></label>
                            <select id="reason" name="reason" class="form-select @error('reason') is-invalid @enderror" required>
                                <option value="">-- Pilih Alasan --</option>
                                <option value="Selisih Opname" @selected(old('reason') == 'Selisih Opname')>Selisih Opname</option>
                                <option value="Rusak" @selected(old('reason') == 'Rusak')>Rusak</option>
                                <option value="Hilang" @selected(old('reason') == 'Hilang')>Hilang</option>
                                <option value="Koreksi Data" @selected(old('reason') == 'Koreksi Data')>Koreksi Data</option>
                                <option value="Lainnya" @selected(old('reason') == 'Lainnya')>Lainnya</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                rows="3" placeholder="Tambahkan catatan jika diperlukan">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Simpan Penyesuaian
                            </button>
                            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-nexus-outline">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-nexus">
                <div class="card-header-nexus">
                    <h5 class="card-title">Informasi</h5>
                </div>
                <div class="card-body-nexus">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Penyesuaian Manual</strong><br>
                        Gunakan fitur ini untuk menyesuaikan stok yang tidak sesuai dengan sistem. Setiap penyesuaian akan dicatat dalam riwayat pergerakan stok.
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Catatan Penting:</strong><br>
                        • Penyesuaian akan langsung diterapkan<br>
                        • Tidak bisa di-undo, hanya bisa dihapus oleh Admin<br>
                        • Setiap penyesuaian dicatat secara detail
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const itemStocks = {
        @foreach($items as $item)
            {{ $item->id }}: {{ $item->total_stock ?? 0 }},
        @endforeach
    };

    $('#item_id').on('change', function() {
        const itemId = $(this).val();
        const currentStock = itemStocks[itemId] || 0;
        $('#current_stock').val(currentStock);
        updateNewStock();
    });

    $('#quantity_adjustment').on('input', function() {
        updateNewStock();
    });

    function updateNewStock() {
        const currentStock = parseInt($('#current_stock').val()) || 0;
        const adjustment = parseInt($('#quantity_adjustment').val()) || 0;
        const newStock = currentStock + adjustment;
        
        $('#new_stock').val(newStock);
        
        if (newStock < 0) {
            $('#new_stock').addClass('is-invalid');
        } else {
            $('#new_stock').removeClass('is-invalid');
        }
    }

    // Initialize on page load
    if ($('#item_id').val()) {
        $('#item_id').trigger('change');
    }
</script>
@endpush
@endsection
