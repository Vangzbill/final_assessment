@extends('admin.pages.layout-admin')
@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Pesanan</h3>
                <p class="text-subtitle text-muted">Data data pesanan yang dilakukan pelanggan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Pesanan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">All
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body">
                <p>Vertical Navbar is a layout option that you can use with Mazer. </p>

                <p>In case you want the navbar to be sticky on top while scrolling, add
                    <code>.navbar-fixed</code> class alongside with <code>.layout-navbar</code> class.
                </p>
            </div>
        </div>
    </section>
</div>
@endsection
