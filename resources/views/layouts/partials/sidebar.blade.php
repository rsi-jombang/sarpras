<aside id="sidebar" class="sidebar" aria-label="Main Navigation">

    {{-- Brand --}}
    <a href="" class="sidebar-brand">
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
            <a href="" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill nav-icon"></i>
                <span class="nav-label">Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="" class="nav-link {{ request()->routeIs('analytics*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill nav-icon"></i>
                <span class="nav-label">Analytics</span>
            </a>
        </li>

        <li>
            <div class="sidebar-label">Management</div>
        </li>

        {{-- Multi-level: Users --}}
        <li class="nav-item">
            <a href="#usersMenu" class="nav-link
               {{ request()->routeIs('users*') ? 'active' : '' }}"
                data-bs-toggle="collapse">
                <i class="bi bi-people-fill nav-icon"></i>
                <span class="nav-label">Users</span>
                <i class="bi bi-chevron-right menu-arrow ms-auto"></i>
            </a>
            <div id="usersMenu" class="collapse {{ request()->routeIs('users*') ? 'show' : '' }}">
                <ul class="sidebar-submenu">
                    <li>
                        <a href="" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                            All Users
                        </a>
                    </li>
                    <li>
                        <a href="" class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}">
                            Add User
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- dst sesuai kebutuhan... --}}

        <li>
            <div class="sidebar-label">System</div>
        </li>

        <li class="nav-item">
            <a href="" class="nav-link {{ request()->routeIs('settings*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill nav-icon"></i>
                <span class="nav-label">Settings</span>
            </a>
        </li>

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
