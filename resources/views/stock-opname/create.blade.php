    @extends('layouts.app')

@section('title', 'Buat Stok Opname')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary)">Buat Stok Opname</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:12.5px">
                    <li class="breadcrumb-item"><a href="{{ route('stock-opname.index') }}" class="text-decoration-none" style="color:var(--text-muted)">Stok Opname</a></li>
                    <li class="breadcrumb-item active" style="color:var(--text-secondary)">Buat Baru</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1"
           style="border-radius:var(--radius-md);font-size:13px">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Peringatan --}}
    <div class="alert border-0 d-flex align-items-start gap-3 mb-4 animate-in"
         style="background:rgba(234,179,8,0.08);border-left:4px solid #eab308!important;border-radius:var(--radius-md)">
        <i class="bi bi-exclamation-triangle text-warning mt-1" style="font-size:1.1rem;flex-shrink:0"></i>
        <div style="font-size:13px">
            <strong style="color:var(--text-primary)">Perhatian sebelum membuat opname:</strong><br>
            <span class="text-muted">
                Saat opname dibuat, sistem akan <strong>men-snapshot seluruh stok saat ini</strong> sebagai stok sistem.
                Pastikan tidak ada transaksi penerimaan atau pengeluaran barang yang sedang berlangsung.
                Sebaiknya dilakukan saat akhir bulan atau saat operasional sedang tidak aktif.
            </span>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 animate-in" style="background:var(--bg-surface);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
                <div class="card-body p-4">

                    <h6 class="fw-bold mb-3" style="color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px">
                        <i class="bi bi-info-circle me-2"></i>Informasi Opname
                    </h6>

                    <form action="{{ route('stock-opname.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500">
                                Tanggal Opname <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="opname_date"
                                   class="form-control form-control-sm @error('opname_date') is-invalid @enderror"
                                   value="{{ old('opname_date', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}" style="font-size:13px" required>
                            @error('opname_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="font-size:13px;font-weight:500">Catatan</label>
                            <textarea name="notes" rows="3"
                                      class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                      placeholder="Contoh: Opname akhir bulan Juni 2024..."
                                      style="font-size:13px">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Info snapshot --}}
                        <div class="p-3 rounded-3 mb-4" style="background:var(--bg-muted);border:1px solid var(--border-subtle);font-size:13px">
                            <div class="d-flex align-items-center gap-2 text-muted mb-1">
                                <i class="bi bi-camera"></i>
                                <strong style="color:var(--text-secondary)">Snapshot Stok</strong>
                            </div>
                            <span class="text-muted" style="font-size:12.5px">
                                Sistem akan otomatis merekam jumlah stok saat ini dari
                                <strong id="totalBatch">...</strong> batch aktif di gudang.
                            </span>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-secondary"
                               style="border-radius:var(--radius-md);font-size:13px">Batal</a>
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                    style="border-radius:var(--radius-md);background:var(--accent-primary);border-color:var(--accent-primary);font-size:13px">
                                <i class="bi bi-clipboard-plus"></i> Buat Opname & Snapshot Stok
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Hitung total batch aktif untuk info snapshot
$.get('/api/items/stats', res => {
    if (res.success) $('#totalBatch').text('semua item');
});
</script>
@endpush
