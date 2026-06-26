@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Detail Penyesuaian Stock</h1>
                <p class="page-subtitle">Lihat detail dan riwayat penyesuaian</p>
            </div>
            <div>
                @can('delete', $stockAdjustment)
                    <form action="{{ route('stock-adjustments.destroy', $stockAdjustment) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus penyesuaian ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                @endcan
                <a href="{{ route('stock-adjustments.index') }}" class="btn btn-nexus-outline">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-nexus mb-3">
                <div class="card-header-nexus">
                    <h5 class="card-title">Informasi Penyesuaian</h5>
                </div>
                <div class="card-body-nexus">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Item</label>
                            <p class="mb-0">
                                <strong>{{ $stockAdjustment->item->name }}</strong>
                                <br>
                                <small class="text-muted">Kode: {{ $stockAdjustment->item->code }}</small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Kategori</label>
                            <p class="mb-0">
                                <span class="badge badge-info">{{ $stockAdjustment->item->category->name }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted">Stok Sebelum</label>
                            <p class="mb-0">
                                <strong class="h5">{{ $stockAdjustment->stock_before }}</strong>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Penyesuaian</label>
                            <p class="mb-0">
                                <strong class="h5 {{ $stockAdjustment->quantity_adjustment > 0 ? 'text-success' : ($stockAdjustment->quantity_adjustment < 0 ? 'text-danger' : '') }}">
                                    {{ $stockAdjustment->quantity_adjustment > 0 ? '+' : '' }}{{ $stockAdjustment->quantity_adjustment }}
                                </strong>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Stok Sesudah</label>
                            <p class="mb-0">
                                <strong class="h5">{{ $stockAdjustment->stock_after }}</strong>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Alasan</label>
                            <p class="mb-0">
                                <strong>{{ $stockAdjustment->reason }}</strong>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tanggal Penyesuaian</label>
                            <p class="mb-0">
                                <strong>{{ $stockAdjustment->created_at->format('d M Y H:i') }}</strong>
                            </p>
                        </div>
                    </div>

                    @if($stockAdjustment->notes)
                        <div class="mb-3">
                            <label class="form-label text-muted">Catatan</label>
                            <div class="alert alert-light">
                                {{ $stockAdjustment->notes }}
                            </div>
                        </div>
                    @endif

                    <div class="mb-0">
                        <label class="form-label text-muted">Diatur Oleh</label>
                        <p class="mb-0">
                            <strong>{{ $stockAdjustment->adjustedBy->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $stockAdjustment->adjustedBy->username }} ({{ $stockAdjustment->adjustedBy->role->label() }})</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-nexus">
                <div class="card-header-nexus">
                    <h5 class="card-title">Batch Stock yang Disesuaikan</h5>
                </div>
                <div class="card-body-nexus">
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted">ID Batch</td>
                            <td><strong>{{ $stockAdjustment->itemStock->id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Harga Beli</td>
                            <td><strong>Rp {{ number_format($stockAdjustment->itemStock->purchase_price, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor Batch</td>
                            <td><strong>{{ $stockAdjustment->itemStock->batch_number ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Kadaluarsa</td>
                            <td><strong>{{ $stockAdjustment->itemStock->expired_date ? $stockAdjustment->itemStock->expired_date->format('d M Y') : '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Catatan Batch</td>
                            <td><strong>{{ $stockAdjustment->itemStock->notes ?? '-' }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-nexus">
                <div class="card-header-nexus">
                    <h5 class="card-title">Status</h5>
                </div>
                <div class="card-body-nexus">
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>Penyesuaian Tersimpan</strong><br>
                        Perubahan stok sudah diterapkan ke sistem dan tercatat dalam riwayat pergerakan stok.
                    </div>
                </div>
            </div>

            <div class="card-nexus">
                <div class="card-header-nexus">
                    <h5 class="card-title">Ringkasan</h5>
                </div>
                <div class="card-body-nexus">
                    <div class="mb-3">
                        <small class="text-muted">Total Stok Item Sekarang</small>
                        <p class="mb-0 h5">
                            <strong>{{ $stockAdjustment->item->total_stock ?? 0 }}</strong>
                        </p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Total Batch</small>
                        <p class="mb-0 h5">
                            <strong>{{ $stockAdjustment->item->stocks->count() }}</strong>
                        </p>
                    </div>
                    <div>
                        <small class="text-muted">Pergerakan Terakhir</small>
                        <p class="mb-0 h5">
                            <strong>{{ $stockAdjustment->created_at->diffForHumans() }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
