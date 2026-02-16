<?php
// Shared admin header + sidebar.
// Optional: set $activePage to one of: dashboard, messages
$activePage = $activePage ?? '';
?>

<header class="admin-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid">
            <!-- Logo/Brand -->
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="data:image/svg+xml,%3csvg%20width='32'%20height='32'%20viewBox='0%200%2032%2032'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3c!--%20Background%20circle%20for%20the%20M%20--%3e%3ccircle%20cx='16'%20cy='16'%20r='16'%20fill='url(%23logoGradient)'/%3e%3c!--%20Centered%20Letter%20M%20--%3e%3cpath%20d='M10%2024V8h2.5l2.5%206.5L17.5%208H20v16h-2V12.5L16.5%2020h-1L14%2012.5V24H10z'%20fill='white'%20font-weight='700'/%3e%3c!--%20Gradient%20definition%20--%3e%3cdefs%3e%3clinearGradient%20id='logoGradient'%20x1='0%25'%20y1='0%25'%20x2='100%25'%20y2='100%25'%3e%3cstop%20offset='0%25'%20style='stop-color:%236366f1;stop-opacity:1'%20/%3e%3cstop%20offset='100%25'%20style='stop-color:%238b5cf6;stop-opacity:1'%20/%3e%3c/linearGradient%3e%3c/defs%3e%3c/svg%3e" alt="Logo" height="32" class="d-inline-block align-text-top me-2">
                <h1 class="h4 mb-0 fw-bold text-primary">Metis</h1>
            </a>

            <!-- Search Bar with Alpine.js -->
            <div class="search-container flex-grow-1 mx-4" x-data="searchComponent">
                <div class="position-relative">
                    <input type="search" 
                           class="form-control" 
                           placeholder="Search... (Ctrl+K)"
                           x-model="query"
                           @input="search()"
                           data-search-input
                           aria-label="Search">
                    <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3"></i>

                    <!-- Search Results Dropdown -->
                    <div x-show="results.length > 0" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="position-absolute top-100 start-0 w-100 bg-white border rounded-2 shadow-lg mt-1 z-3">
                        <template x-for="result in results" :key="result.title">
                            <a :href="result.url" class="d-block px-3 py-2 text-decoration-none text-dark border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-text me-2 text-muted"></i>
                                    <span x-text="result.title"></span>
                                    <small class="ms-auto text-muted" x-text="result.type"></small>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right Side Icons -->
            <div class="navbar-nav flex-row">
                <!-- Theme Toggle with Alpine.js -->
                <div x-data="themeSwitch">
                    <button class="btn btn-outline-secondary me-2" 
                            type="button" 
                            @click="toggle()"
                            data-bs-toggle="tooltip"
                            data-bs-placement="bottom"
                            title="Toggle theme">
                        <i class="bi bi-sun-fill" x-show="currentTheme === 'light'"></i>
                        <i class="bi bi-moon-fill" x-show="currentTheme === 'dark'"></i>
                    </button>
                </div>

                <!-- Fullscreen Toggle -->
                <button class="btn btn-outline-secondary me-2" 
                        type="button" 
                        data-fullscreen-toggle
                        data-bs-toggle="tooltip"
                        data-bs-placement="bottom"
                        title="Toggle fullscreen">
                    <i class="bi bi-arrows-fullscreen icon-hover"></i>
                </button>

                <!-- Notifications -->
                <div class="dropdown me-2">
                    <button class="btn btn-outline-secondary position-relative" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item" href="#">New user registered</a></li>
                        <li><a class="dropdown-item" href="#">Server status update</a></li>
                        <li><a class="dropdown-item" href="#">New message received</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary d-flex align-items-center" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <img src="data:image/svg+xml,%3csvg%20width='32'%20height='32'%20viewBox='0%200%2032%2032'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3c!--%20Background%20circle%20--%3e%3ccircle%20cx='16'%20cy='16'%20r='16'%20fill='url(%23avatarGradient)'/%3e%3c!--%20Person%20silhouette%20--%3e%3cg%20fill='white'%20opacity='0.9'%3e%3c!--%20Head%20--%3e%3ccircle%20cx='16'%20cy='12'%20r='5'/%3e%3c!--%20Body%20--%3e%3cpath%20d='M16%2018c-5.5%200-10%202.5-10%207v1h20v-1c0-4.5-4.5-7-10-7z'%3e%3c/path%3e%3c/g%3e%3c!--%20Subtle%20border%20--%3e%3ccircle%20cx='16'%20cy='16'%20r='15.5'%20fill='none'%20stroke='rgba(255,255,255,0.2)'%20stroke-width='1'/%3e%3c!--%20Gradient%20definition%20--%3e%3cdefs%3e%3clinearGradient%20id='avatarGradient'%20x1='0%25'%20y1='0%25'%20x2='100%25'%20y2='100%25'%3e%3cstop%20offset='0%25'%20style='stop-color:%236b7280;stop-opacity:1'%20/%3e%3cstop%20offset='100%25'%20style='stop-color:%234b5563;stop-opacity:1'%20/%3e%3c/linearGradient%3e%3c/defs%3e%3c/svg%3e" 
                             alt="User Avatar" 
                             width="24" 
                             height="24" 
                             class="rounded-circle me-2">
                        <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_nom'] ?? 'non touver'); ?></span>
                        <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- Sidebar -->
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-content">
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link<?php echo $activePage === 'dashboard' ? ' active' : ''; ?>" href="/bngrc/dashboard">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./analytics.html">
                        <i class="bi bi-graph-up"></i>
                        <span>Analytics</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./users.html">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./products.html">
                        <i class="bi bi-box"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./orders.html">
                        <i class="bi bi-bag-check"></i>
                        <span>Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./forms.html">
                        <i class="bi bi-ui-checks"></i>
                        <span>Forms</span>
                        <span class="badge bg-success rounded-pill ms-auto">New</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#elementsSubmenu" aria-expanded="false">
                        <i class="bi bi-puzzle"></i>
                        <span>Elements</span>
                        <span class="badge bg-primary rounded-pill ms-2 me-2">New</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="elementsSubmenu">
                        <ul class="nav nav-submenu">
                            <li class="nav-item">
                                <a class="nav-link" href="./elements.html">
                                    <i class="bi bi-grid"></i>
                                    <span>Overview</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./elements-buttons.html">
                                    <i class="bi bi-square"></i>
                                    <span>Buttons</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./elements-alerts.html">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <span>Alerts</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./elements-badges.html">
                                    <i class="bi bi-award"></i>
                                    <span>Badges</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./elements-cards.html">
                                    <i class="bi bi-card-text"></i>
                                    <span>Cards</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./elements-modals.html">
                                    <i class="bi bi-window"></i>
                                    <span>Modals</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./elements-forms.html">
                                    <i class="bi bi-ui-checks"></i>
                                    <span>Forms</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./elements-tables.html">
                                    <i class="bi bi-table"></i>
                                    <span>Tables</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./reports.html">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo $activePage === 'messages' ? ' active' : ''; ?>" href="/message">
                        <i class="bi bi-chat-dots"></i>
                        <span>Messages</span>
                        <span class="badge bg-danger rounded-pill ms-auto">3</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./calendar.html">
                        <i class="bi bi-calendar-event"></i>
                        <span>Calendar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./files.html">
                        <i class="bi bi-folder2-open"></i>
                        <span>Files</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <small class="text-muted px-3 text-uppercase fw-bold">Admin</small>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./settings.html">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./security.html">
                        <i class="bi bi-shield-check"></i>
                        <span>Security</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./help.html">
                        <i class="bi bi-question-circle"></i>
                        <span>Help & Support</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Floating Hamburger Menu -->
<button class="hamburger-menu" 
        type="button" 
        data-sidebar-toggle
        aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
</button>
