<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem SARPRAS - RSI Jombang</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: var(--font-sans, "Plus Jakarta Sans", sans-serif);
            overflow: hidden; /* Mengunci halaman agar benar-benar Single Page */
            background-color: #ffffff;
        }

        /* TIPOGRAF & WARNA */
        .text-slate-900 { color: #0f172a; }
        .text-slate-600 { color: #475569; }
        .text-indigo-600 { color: #4f46e5; }
        .bg-indigo-50 { background-color: #eef2ff; }

        /* BAGIAN KIRI - KONTEN UTAMA */
        .left-panel {
            background-color: #ffffff;
            position: relative;
            z-index: 10;
            box-shadow: 20px 0 50px rgba(0,0,0,0.05); /* Memberi kedalaman antara kiri dan kanan */
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Animasi Masuk Konten */
        .fade-in-up {
            opacity: 0;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }
        .delay-4 { animation-delay: 0.8s; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Tombol */
        .btn-primary-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #ffffff;
            padding: 14px 36px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.05rem;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4);
            color: #ffffff;
        }

        .btn-outline-custom {
            background: transparent;
            color: #0f172a;
            padding: 12px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.05rem;
            border: 1px solid #cbd5e1;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .btn-outline-custom:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        /* BAGIAN KANAN - GAMBAR & ANIMASI */
        .right-panel {
            position: relative;
            overflow: hidden;
            height: 100vh;
        }

        /* Latar Belakang Gambar ber-animasi lambat */
        .bg-image {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            transform: scale(1.05);
            animation: slowZoom 25s infinite alternate ease-in-out;
        }
        @keyframes slowZoom {
            0% { transform: scale(1.05); }
            100% { transform: scale(1.15); }
        }

        /* Overlay Gradien pada gambar agar terlihat mewah */
        .bg-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(30, 27, 75, 0.8) 0%, rgba(79, 70, 229, 0.4) 100%);
            z-index: 1;
        }

        /* Kartu Kaca Melayang (Floating Glass Cards) */
        .floating-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 20px;
            color: #ffffff;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            z-index: 2;
            animation: floatUpDown 6s infinite alternate ease-in-out;
        }
        .card-1 {
            top: 25%;
            left: 15%;
            width: 280px;
        }
        .card-2 {
            bottom: 25%;
            right: 15%;
            width: 320px;
            animation-delay: -3s; /* Berbeda timing dengan card-1 */
        }

        @keyframes floatUpDown {
            0% { transform: translateY(0); }
            100% { transform: translateY(-20px); }
        }

        .icon-box-glass {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 12px;
        }

        /* Pulse indicator */
        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #4f46e5;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7);
            animation: pulseDot 2s infinite;
        }
        @keyframes pulseDot {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(79, 70, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }
    </style>
</head>
<body>

    <div class="row g-0 h-100">

        <!-- PANEL KIRI - KONTEN -->
        <div class="col-12 col-lg-5 left-panel px-4 px-lg-5 py-4">

            <!-- Navbar / Logo -->
            <div class="d-flex justify-content-between align-items-center mb-auto fade-in-up">
                <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);border-radius:10px;color:white;box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);">
                        <i class="bi bi-hospital fs-5"></i>
                    </div>
                    <span class="fs-5 fw-bold text-slate-900" style="letter-spacing:-0.5px;">SARPRAS RSI</span>
                </a>
            </div>

            <!-- Main Hero Text -->
            <div class="my-auto">
                <div class="d-inline-flex align-items-center gap-2 bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-pill mb-4 fade-in-up delay-1 border" style="border-color: #c7d2fe !important;">
                    <span class="pulse-dot"></span>
                    <span class="fw-bold" style="font-size: 0.85rem; letter-spacing: 0.5px;">SISTEM MANAJEMEN LOGISTIK</span>
                </div>

                <h1 class="fw-bolder text-slate-900 mb-4 fade-in-up delay-2" style="font-size: clamp(2.5rem, 4vw, 3.5rem); line-height: 1.15; letter-spacing: -1px;">
                    Kelola Aset & Fasilitas Lebih <span class="text-indigo-600">Terpadu</span>
                </h1>

                <p class="text-slate-600 mb-5 fade-in-up delay-3" style="font-size: 1.1rem; line-height: 1.7; max-width: 450px;">
                    Tingkatkan efisiensi pengelolaan persediaan gudang, inventarisasi aset, dan kelancaran alur permintaan barang antar unit di Rumah Sakit Islam Jombang.
                </p>

                <div class="d-flex flex-column flex-sm-row gap-3 fade-in-up delay-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary-custom">
                            Buka Dashboard <i class="bi bi-arrow-right-short fs-4 ms-1"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary-custom">
                            Masuk Sekarang <i class="bi bi-box-arrow-in-right fs-5 ms-2"></i>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Footer Kiri -->
            <div class="mt-auto pt-4 fade-in-up delay-4">
                <p class="mb-0 text-slate-600" style="font-size: 0.85rem;">
                    &copy; {{ date('Y') }} Sistem Informasi SARPRAS.<br>Rumah Sakit Islam Jombang.
                </p>
            </div>

        </div>

        <!-- PANEL KANAN - GAMBAR & KARTU ANIMASI -->
        <div class="col-lg-7 d-none d-lg-block right-panel">
            <div class="bg-image"></div>
            <div class="bg-overlay"></div>

            <!-- Elemen UI Melayang 1 -->
            <div class="floating-card card-1 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-3" style="border-color: rgba(255,255,255,0.1) !important;">
                    <div class="icon-box-glass text-success" style="background: rgba(34, 197, 94, 0.2);">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Status Sistem</h6>
                        <small style="color: rgba(255,255,255,0.7);">Online & tersinkronisasi</small>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span style="color: rgba(255,255,255,0.8); font-size: 0.9rem;">Update terakhir:</span>
                    <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-50">Baru saja</span>
                </div>
            </div>

            <!-- Elemen UI Melayang 2 -->
            <div class="floating-card card-2">
                <div class="icon-box-glass text-info mb-2" style="background: rgba(6, 182, 212, 0.2);">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h5 class="fw-bold mb-1">Manajemen Stok Real-time</h5>
                <p class="mb-0" style="font-size: 0.9rem; color: rgba(255,255,255,0.7); line-height: 1.5;">
                    Permintaan unit dan proses stok opname terintegrasi dalam satu layar tanpa henti.
                </p>
            </div>

        </div>
    </div>

</body>
</html>
