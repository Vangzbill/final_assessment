<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<nav id="mainNavbar" class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <img src="{{ secure_asset('assets/images/logo_dark.png') }}" alt="logo" class="me-3"
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

            <ul class="navbar-nav d-flex align-items-center">
                <li class="nav-item me-3">
                    <a class="nav-link text-dark" href="#" data-bs-toggle="modal" data-bs-target="#helpdeskModal">
                        <i class="bi bi-headset"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-warning me-2" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary" href="{{ route('register') }}">Register</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
