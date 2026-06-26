<aside id="sidebar" class="sidebar" aria-label="Main Navigation">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-hexagon-fill"></i>
        </div>
        <div class="brand-text">
            <span class="brand-name">Nexus</span>
            <span class="brand-tagline">Admin Dashboard</span>
        </div>
    </a>

    <ul class="sidebar-nav mt-2">

        <li>
            <div class="sidebar-label">Main</div>
        </li>

        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill nav-icon"></i>
                <span class="nav-label">Dashboard</span>
            </a>
        </li>

        @if(!auth()->user()->isUnit())
        <li class="nav-item">
            <a href="#" class="nav-link {{ request()->routeIs('analytics*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill nav-icon"></i>
                <span class="nav-label">Analytics</span>
            </a>
        </li>
        @endif

        {{-- Management — hidden for unit --}}
        @if(!auth()->user()->isUnit())
        <li>
            <div class="sidebar-label">Management</div>
        </li>

        {{-- Manajemen Pengguna (admin only) --}}
        @can('viewAny', App\Models\User::class)
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users*') ? 'active' : '' }}">
                <i class="bi bi-people-fill nav-icon"></i>
                <span class="nav-label">Manajemen Pengguna</span>
            </a>
        </li>
        @endcan

        {{-- Master Unit --}}
        <li class="nav-item">
            <a href="{{ route('units.index') }}" class="nav-link {{ request()->routeIs('units*') ? 'active' : '' }}">
                <i class="bi bi-hospital nav-icon"></i>
                <span class="nav-label">Master Unit</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('category.index') }}"
                class="nav-link {{ request()->routeIs('category*') ? 'active' : '' }}">
                <i class="bi bi-tags nav-icon"></i>
                <span class="nav-label">Master Kategori</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('items.index') }}" class="nav-link {{ request()->routeIs('items*') ? 'active' : '' }}">
                <i class="bi bi-box-seam nav-icon"></i>
                <span class="nav-label">Master Barang</span>
            </a>
        </li>

        <li>
            <div class="sidebar-label">Transaksi</div>
        </li>

        <li class="nav-item">
            <a href="{{ route('purchase-orders.index') }}"
                class="nav-link {{ request()->routeIs('purchase-orders*') ? 'active' : '' }}">
                <i class="bi bi-cart-fill nav-icon"></i>
                <span class="nav-label">Purchase Order</span>
            </a>
        </li>
        @endif

        {{-- Permintaan Barang (semua role) --}}
        <li class="nav-item">
            <a href="{{ route('requests.index') }}"
                class="nav-link {{ request()->routeIs('requests*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data-fill nav-icon"></i>
                <span class="nav-label">Permintaan Barang</span>
            </a>
        </li>

        {{-- Stock Opname & Laporan — hidden untuk unit --}}
        @if(!auth()->user()->isUnit())
        <div class="nav-item">
            <a href="{{ route('stock-opname.index') }}"
                class="nav-link {{ request()->routeIs('stock-opname*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data-fill nav-icon"></i>
                <span class="nav-label">Stock Opname</span>
            </a>
        </div>

        <li class="nav-item">
            <a href="{{ route('stock-adjustments.index') }}"
                class="nav-link {{ request()->routeIs('stock-adjustments*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right nav-icon"></i>
                <span class="nav-label">Penyesuaian Stock</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('reports.index') }}"
                class="nav-link {{ request()->routeIs('reports*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph-fill nav-icon"></i>
                <span class="nav-label">Laporan</span>
            </a>
        </li>
        @endif

    </ul>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer">
        <div class="dropdown">
            <a href="#" class="sidebar-user dropdown-toggle" data-bs-toggle="dropdown">
                <div class="avatar-wrapper">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <span class="avatar-status status-online"></span>
                </div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role ?? 'Admin' }}</div>
                </div>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person"></i> Profile
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right"></i> Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

</aside>
