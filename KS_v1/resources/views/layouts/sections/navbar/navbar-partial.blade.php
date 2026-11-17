<!-- Brand demo (display only for navbar-full and hide below xl) -->
@if(isset($navbarFull))
<div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
    <a href="{{ url('/') }}" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">@include('_partials.macros')</span>
    </a>
</div>
@endif

<!-- Not required for layout-without-menu -->
@if(!isset($navbarHideToggle))
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 {{ isset($contentNavbar) ? 'd-xl-none' : '' }}">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
        <i class="icon-base bx bx-menu icon-md"></i>
    </a>
</div>
@endif

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <div class="navbar-nav align-items-center position-relative">
        <div class="nav-item d-flex align-items-center">
            <i class="icon-base bx bx-search icon-md"></i>
            <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="Search..." aria-label="Search...">
        </div>
    </div>

    <ul class="navbar-nav flex-row align-items-center ms-auto">
        <li class="nav-item lh-1 me-4">
            <a class="github-button" href="{{ config('variables.repository') }}" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star themeselection/sneat-html-laravel-admin-template-free on GitHub">Star</a>
        </li>

        @auth
        @php
            $unreadNotifications = auth()->user()->unreadNotifications()->latest()->take(8)->get();
            $unreadCount = auth()->user()->unreadNotifications()->count();
        @endphp
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <span class="position-relative">
                    <i class="icon-base bx bx-bell icon-md"></i>
                    @if ($unreadCount > 0)
                        <span class="badge rounded-pill bg-danger badge-dot badge-notifications border" id="procurement-unread-count">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end py-0" style="min-width: 320px;">
                <li class="dropdown-menu-header border-bottom">
                    <div class="dropdown-header d-flex align-items-center py-3">
                        <h6 class="mb-0 me-auto">Notifications</h6>
                        @if ($unreadCount > 0)
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                <button type="submit" class="btn btn-text-secondary btn-sm p-0">Mark all read</button>
                            </form>
                        @endif
                    </div>
                </li>
                <li class="dropdown-notifications-list scrollable-container" style="max-height: 320px; overflow:auto;">
                    <ul class="list-group list-group-flush">
                        @forelse ($unreadNotifications as $notification)
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <a href="{{ $notification->data['url'] ?? 'javascript:void(0);' }}" class="d-block text-body">
                                    <div class="fw-semibold">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                    <div class="small text-muted">{{ $notification->data['message'] ?? '' }}</div>
                                    <div class="small text-muted">{{ $notification->created_at?->diffForHumans() }}</div>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4">No new notifications</li>
                        @endforelse
                    </ul>
                </li>
            </ul>
        </li>
        <script>
            (function () {
                const count = {{ (int) $unreadCount }};
                const key = 'procurement_unread_seen';
                const previous = parseInt(localStorage.getItem(key) || '0', 10);
                if (count > previous) {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const oscillator = ctx.createOscillator();
                        const gain = ctx.createGain();
                        oscillator.type = 'sine';
                        oscillator.frequency.value = 880;
                        gain.gain.value = 0.05;
                        oscillator.connect(gain);
                        gain.connect(ctx.destination);
                        oscillator.start();
                        setTimeout(function () {
                            oscillator.stop();
                            ctx.close();
                        }, 180);
                    } catch (e) {}
                }
                localStorage.setItem(key, String(count));
            })();
        </script>
        @endauth

        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                <small class="text-muted">{{ auth()->user()->designation->designation_name ?? '--' }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li><div class="dropdown-divider my-1"></div></li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="icon-base bx bx-cog icon-md me-3"></i><span>Settings</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <span class="d-flex align-items-center align-middle">
                            <i class="flex-shrink-0 icon-base bx bx-credit-card icon-md me-3"></i>
                            <span class="flex-grow-1 align-middle">Billing Plan</span>
                            <span class="flex-shrink-0 badge rounded-pill bg-danger">4</span>
                        </span>
                    </a>
                </li>
                <li><div class="dropdown-divider my-1"></div></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span>
                        </button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</div>
