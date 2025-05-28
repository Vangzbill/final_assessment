<ul class="menu">
    <li class="sidebar-title">Menu</li>

    <li class="sidebar-item {{ Request::is('*pesanan*') ? 'active' : '' }}">
        <a href="{{ route('admin.order') }}" class='sidebar-link'>
            <i class="bi bi-cart-fill"></i>
            <span>Pesanan</span>
        </a>
    </li>
    <li class="sidebar-item {{ Request::is('*tagihan*') ? 'active' : '' }}">
        <a href="{{ route('admin.billing') }}" class='sidebar-link'>
            <i class="bi bi-receipt"></i>
            <span>Tagihan</span>
        </a>
    </li>
    {{-- <li class="sidebar-item {{ Request::is('*produk*') ? 'active' : '' }}">
        <a href="" class='sidebar-link'>
            <i class="bi bi-tags-fill"></i>
            <span>Produk & Layanan</span>
        </a>
    </li> --}}
</ul>
