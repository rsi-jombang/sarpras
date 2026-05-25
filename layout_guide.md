# Panduan Layouting & Desain Nexus Admin

Dokumen ini mendokumentasikan sistem layouting, variabel CSS (Design Tokens), komponen premium, animasi, serta fitur Javascript interaktif yang disediakan oleh tema **Nexus Admin** untuk digunakan dalam membangun halaman-halaman aplikasi Sarpras RS.

---

## 1. Integrasi Dasar Halaman (Blade Template)

Setiap halaman baru (misalnya `resources/views/unit/index.blade.php`) harus memperluas (*extends*) layout utama `layouts.app` dan mendefinisikan blok data berikut:

```blade
@extends('layouts.app')

@section('title', 'Nama Halaman')

@push('styles')
    <!-- CSS Khusus Halaman (jika ada) -->
@endpush

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Konten Halaman Premium Anda di Sini -->
    </div>
@endsection

@push('scripts')
    <!-- JS Khusus Halaman (jika ada) -->
@endpush
```

---

## 2. Token Desain (CSS Variables)

Gunakan variabel CSS ini di dalam atribut `style=""` atau file SCSS kustom untuk memastikan kepatuhan penuh terhadap palet warna **Light/Dark Mode** dan konsistensi UI:

### Warna Latar & Batas (Background & Borders)
* **`var(--bg-canvas)`**: Warna latar belakang halaman utama (Light: `#f0f2f7`, Dark: `#0b0d14`)
* **`var(--bg-surface)`**: Latar kartu/konten utama (Light: `#ffffff`, Dark: `#111318`)
* **`var(--bg-surface-2)`**: Warna latar alternatif/form input (Light: `#f8f9fc`, Dark: `#16191f`)
* **`var(--border-color)`**: Batas garis tipis (Light: `#e2e8f0`, Dark: `#1e2130`)

### Aksen & Warna Utama (Brand Colors)
* **`var(--accent-primary)`**: Warna indigo utama (`#4f46e5`)
* **`var(--accent-hover)`**: Warna indigo saat hover (`#4338ca`)
* **`var(--accent-soft)`**: Indigo transparan untuk badge/hover halus (`rgba(79, 70, 229, 0.1)`)
* **`var(--accent-glow)`**: Indigo glow untuk fokus form (`rgba(79, 70, 229, 0.25)`)

### Tipografi (Typography Colors)
* **`var(--text-primary)`**: Teks utama (Light: `#0f172a`, Dark: `#f1f5f9`)
* **`var(--text-secondary)`**: Teks sekunder/keterangan (Light: `#64748b`, Dark: `#94a3b8`)
* **`var(--text-muted)`**: Teks abu-abu redup (Light: `#94a3b8`, Dark: `#4b5563`)

### Efek Visual (Shadows & Radius)
* **`var(--shadow-sm)`**: Bayangan halus (e.g., untuk baris tabel)
* **`var(--shadow-md)`**: Bayangan sedang (e.g., untuk kartu/card biasa)
* **`var(--shadow-lg)`**: Bayangan kuat (e.g., untuk dropdown/modal/auth card)
* **`var(--radius-sm)`**: `6px` | **`var(--radius-md)`**: `10px` | **`var(--radius-lg)`**: `14px` | **`var(--radius-xl)`**: `20px`

---

## 3. Komponen Premium & Struktur HTML

Gunakan struktur kelas berikut untuk merancang antarmuka premium:

### A. Judul Halaman & Breadcrumb (Page Header)
Mendukung susunan judul halaman yang modern dan bersih:

```html
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 animate-in">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary)">Daftar Unit</h1>
        <p class="text-muted mb-0" style="font-size: 13.5px">Kelola unit kerja dan instalasi rumah sakit penerima sarpras.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius: var(--radius-md)">
            <i class="bi bi-download"></i> Export
        </button>
        <a href="#" class="btn btn-primary d-flex align-items-center gap-2" style="border-radius: var(--radius-md); background: var(--accent-primary); border-color: var(--accent-primary)">
            <i class="bi bi-plus-lg"></i> Tambah Unit
        </a>
    </div>
</div>
```

### B. Kartu Konten Premium (Premium Cards)
Kartu dengan tepian melengkung modern, bayangan halus, dan pergantian tema otomatis:

```html
<div class="card border-0 mb-4" style="background: var(--bg-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm)">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="color: var(--text-primary)">Informasi Statistik</h5>
    </div>
    <div class="card-body px-4 pb-4">
        <!-- Konten Kartu -->
    </div>
</div>
```

### C. Tabel Data Interaktif (Data Tables)
Tema mendukung seleksi baris interaktif (`#masterCheck` dan `.row-check`) serta gaya yang minimalis namun premium:

```html
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr style="border-bottom: 2px solid var(--border-color); font-size: 12.5px; color: var(--text-secondary)">
                <th width="40px">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="masterCheck" style="border-radius: 4px">
                    </div>
                </th>
                <th>Kode Unit</th>
                <th>Nama Unit</th>
                <th>Status</th>
                <th width="100px" class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px solid var(--border-color)">
                <td>
                    <div class="form-check">
                        <input class="form-check-input row-check" type="checkbox" style="border-radius: 4px">
                    </div>
                </td>
                <td class="fw-bold" style="color: var(--text-primary)">IGD</td>
                <td>Instalasi Gawat Darurat</td>
                <td>
                    <span class="badge rounded-pill bg-success-subtle text-success px-2.5 py-1" style="font-size: 11px">Aktif</span>
                </td>
                <td class="text-end">
                    <div class="dropdown">
                        <button class="btn btn-link text-secondary p-1" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="border-radius: var(--radius-md)">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i> Edit</a></li>
                            <li><a class="dropdown-item text-danger" href="#" data-confirm="Apakah Anda yakin ingin menghapus unit ini?"><i class="bi bi-trash me-2"></i> Hapus</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

---

## 4. Efek Interaktif & Animasi JavaScript Terintegrasi

Aplikasi telah memuat fungsi-fungsi interaktif secara global di `dashboard.js`. Anda cukup menulis markup HTML yang sesuai untuk mengaktifkannya secara otomatis:

### A. Animasi Masuk Saat Scroll (Fade In Scroll)
Tambahkan class `animate-in` pada elemen container mana saja. Saat pengguna melakukan scroll ke elemen tersebut, elemen akan memudar ke atas (*fade-up*) secara elegan.
```html
<div class="card animate-in"> ... </div>
```

### B. Penghitung Angka Dinamis (Animated Counter)
Sangat cocok untuk widget stat/dashboard angka besar. Cukup sediakan atribut `data-count`:
* **`data-count`**: Angka target (e.g., `150`, `3245`, atau desimal `4.8`)
* **`data-prefix`**: Karakter awalan opsional (e.g., `Rp. `, `$`)
* **`data-suffix`**: Karakter akhiran opsional (e.g., ` pcs`, ` Unit`, `%`)

```html
<div class="h2 fw-extrabold mb-1" data-count="124" data-suffix=" Unit">0</div>
<div class="h2 fw-extrabold mb-1" data-count="15000000" data-prefix="Rp. ">0</div>
```

### C. Pintasan Keyboard Pencarian (Search Focus Shortcut)
Menekan tombol `Ctrl + K` (Windows/Linux) atau `Cmd + K` (Mac) pada halaman mana saja akan memindahkan fokus kursor ke kotak pencarian utama di navbar secara instan.

### D. Konfirmasi Hapus Instan (`data-confirm`)
Untuk tombol form hapus atau tautan krusial, tambahkan atribut `data-confirm="Pesan konfirmasi Anda"` untuk mengaktifkan dialog konfirmasi bawaan secara otomatis sebelum aksi dijalankan:
```html
<button type="submit" class="btn btn-danger" data-confirm="Apakah Anda yakin ingin menghapus data ini secara permanen?">
    Hapus Data
</button>
```

### E. Integrasi Chart Mini Canvas
Jika Anda ingin menggambar bagan interaktif yang merespons perubahan Dark/Light mode secara instan, cukup sediakan tag canvas dengan ID berikut:
* `#revenueChart` (Bar Chart)
* `#usersChart` (Line Area Chart)
* `#trafficChart` (Donut Chart)
* `#perfChart` (Double Area Chart)

```html
<canvas id="revenueChart" width="400" height="220"></canvas>
```
*Catatan: Inisialisasi visual canvas ini akan otomatis diulang oleh JS sesaat setelah tombol toggle Dark Mode diklik.*
