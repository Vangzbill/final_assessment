<nav id="mainNavbar" class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <img src="{{ asset('assets/images/logo_dark.png') }}" alt="logo" class="me-3"
            style="width: 40px; max-height: 40px;">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list bg-black"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-dark {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark {{ request()->routeIs('product.index') ? 'active' : '' }}"
                        href="{{ route('product.index') }}">Produk</a>
                </li>
            </ul>

            @php
                $isLoggedIn = Session::get('jwt_token');
                $username = Session::get('username');
            @endphp

            <ul class="navbar-nav d-flex align-items-center">
                <li class="nav-item me-3">
                    <a class="nav-link text-dark" href="#" data-bs-toggle="modal" data-bs-target="#helpdeskModal">
                        <i class="bi bi-headset"></i>
                    </a>
                </li>

                @if ($isLoggedIn)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-outline-dark d-flex align-items-center"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                            style="background-color: transparent; color: #000;">
                            <i class="bi bi-person-circle me-1" style="color: #000;"></i>
                            <span class="ms-1" style="color: #000;">{{ $username }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 180px;">
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-bold"
                                        style="text-align: left;">
                                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-warning me-2" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="{{ route('register') }}">Register</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
