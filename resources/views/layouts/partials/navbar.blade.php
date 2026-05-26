<!-- ========================================================
         TOP NAVBAR
         ======================================================== -->
<nav class="top-navbar" aria-label="Top Navigation">
    <!-- Sidebar Toggle (Desktop) -->
    <button class="sidebar-toggle-btn d-none d-lg-flex" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
        <i class="bi bi-layout-sidebar-inset"></i>
    </button>

    <!-- Mobile Menu Trigger -->
    <button class="sidebar-toggle-btn d-flex d-lg-none" id="mobileSidebarToggle" aria-label="Open Sidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Breadcrumb (desktop only) -->
    <nav aria-label="breadcrumb" class="d-none d-md-flex">
        <ol class="breadcrumb mb-0" style="font-size: 12.5px">
            <li class="breadcrumb-item">
                <a href="#" class="text-decoration-none" style="color: var(--text-muted)">Home</a>
            </li>
            <li class="breadcrumb-item active" style="color: var(--text-secondary)">
                Dashboard
            </li>
        </ol>
    </nav>

    <!-- Search Bar -->
    <div class="navbar-search d-none d-lg-block">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="globalSearch" class="form-control" placeholder="Search anything…"
            aria-label="Global Search" />
        <span class="kbd-hint">⌘K</span>
    </div>

    <!-- Right Actions -->
    <div class="navbar-actions">
        <!-- Dark Mode Toggle -->
        <div class="dark-toggle" data-theme-toggle role="button" aria-label="Toggle dark mode" tabindex="0">
            <div class="toggle-icon toggle-light active">
                <i class="bi bi-sun-fill"></i>
            </div>
            <div class="toggle-icon toggle-dark">
                <i class="bi bi-moon-stars-fill"></i>
            </div>
        </div>

        <!-- Notifications -->
        <div class="dropdown">
            <button class="nav-action-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                <i class="bi bi-bell"></i>
                <span class="badge-dot"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0">
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom"
                    style="border-color: var(--border-color) !important">
                    <span class="fw-bold" style="font-size: 14px; color: var(--text-primary)">Notifications</span>
                    <span class="badge-nexus badge-purple">4 New</span>
                </div>

                <div class="notif-item unread">
                    <div class="notif-icon-wrap" style="background: rgba(79, 70, 229, 0.12); color: #4f46e5">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div class="notif-content flex-1">
                        <div class="notif-title">New User Registered</div>
                        <div class="notif-text">
                            Sarah Johnson joined your platform
                        </div>
                        <div class="notif-time">2 minutes ago</div>
                    </div>
                    <span class="unread-dot"></span>
                </div>

                <div class="notif-item unread">
                    <div class="notif-icon-wrap" style="background: rgba(34, 197, 94, 0.12); color: #22c55e">
                        <i class="bi bi-cart-check-fill"></i>
                    </div>
                    <div class="notif-content flex-1">
                        <div class="notif-title">Order Completed</div>
                        <div class="notif-text">Order #4821 has been shipped</div>
                        <div class="notif-time">18 minutes ago</div>
                    </div>
                    <span class="unread-dot"></span>
                </div>

                <div class="notif-item">
                    <div class="notif-icon-wrap" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="notif-content flex-1">
                        <div class="notif-title">Low Inventory Alert</div>
                        <div class="notif-text">
                            Product "Nexus Pro X" is running low
                        </div>
                        <div class="notif-time">1 hour ago</div>
                    </div>
                </div>

                <div class="notif-item">
                    <div class="notif-icon-wrap" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="notif-content flex-1">
                        <div class="notif-title">Monthly Report Ready</div>
                        <div class="notif-text">
                            Your May 2026 report is available
                        </div>
                        <div class="notif-time">3 hours ago</div>
                    </div>
                </div>

                <div class="p-2 border-top" style="border-color: var(--border-color) !important">
                    <a href="#" class="btn w-100"
                        style="
                      font-size: 12.5px;
                      color: var(--accent-primary);
                      font-weight: 600;
                    ">
                        View all notifications →
                    </a>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <button class="nav-action-btn d-none d-sm-flex" aria-label="Messages">
            <i class="bi bi-chat-dots"></i>
        </button>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <div class="navbar-user" data-bs-toggle="dropdown" aria-expanded="false" role="button"
                aria-label="User menu">
                <div class="user-meta d-none d-lg-block">
                    <div class="u-name">{{ auth()->user()->name }}</div>
                    <div class="u-role">{{ auth()->user()->role ?? 'Admin' }}</div>
                </div>
                <div class="avatar-wrapper">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <span class="avatar-status status-online"></span>
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end mt-2">
                <li>
                    <div class="px-3 py-2">
                        <div class="fw-bold" style="font-size: 14px; color: var(--text-primary)">
                            {{ auth()->user()->name }}
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted)">
                            {{ auth()->user()->email }}
                        </div>
                    </div>
                </li>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i> My Profile</a>
                </li>
                <li>
                    <a class="dropdown-item" href="#"><i class="bi bi-sliders"></i> Account Settings</a>
                </li>
                <li>
                    <a class="dropdown-item" href="#"><i class="bi bi-credit-card"></i> Billing & Plans</a>
                </li>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <li>
                    <a class="dropdown-item" href="#"><i class="bi bi-life-preserver"></i> Help Center</a>
                </li>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger w-100 text-start border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right"></i> Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <!-- /navbar-actions -->
</nav>
<!-- /top-navbar -->
