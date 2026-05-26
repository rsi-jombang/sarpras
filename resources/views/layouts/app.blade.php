<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Nexus Admin</title>

    {{-- Vite akan otomatis inject CSS yang sudah di-compile --}}
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <!-- Global CDNs (DataTables CSS) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    @stack('styles')
</head>

<body>

    <div class="layout-wrapper">

        {{-- Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Main Content --}}
        <div id="mainContent" class="main-content">

            {{-- Navbar --}}
            @include('layouts.partials.navbar')

            {{-- Flash Message --}}
            @if (session('success'))
                <div class="mx-4 mt-3">
                    <div class="alert alert-success alert-dismissible fade show
                            d-flex align-items-center gap-2"
                        role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 mt-3">
                    <div class="alert alert-danger alert-dismissible fade show
                            d-flex align-items-center gap-2"
                        role="alert">
                        <i class="bi bi-x-circle-fill"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            {{-- Page Content --}}
            <main class="page-content">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer
                class="px-4 py-3 border-top d-flex align-items-center
                        justify-content-between"
                style="border-color:var(--border-color)!important;
                       background:var(--bg-surface);">
                <span style="font-size:12.5px;color:var(--text-muted);">
                    © {{ date('Y') }} Nexus Admin.
                </span>
            </footer>

        </div>
    </div>

    <!-- Global CDNs (jQuery, DataTables, SweetAlert2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>

</html>
