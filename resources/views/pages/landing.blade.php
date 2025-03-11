@extends('layout.blank')
@section('content')
    @include('components.navbar')
    <div class="container mt-5">
        <div class="row pt-5 pe-3 ps-3 mb-5">
            <div class="col-md-12 d-flex justify-content-center align-items-center flex-column">
                <h1 class="text-center mt-5"
                    style="
                    font-size: 4rem;
                    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    font-weight: bold;">
                    Discover Our Product
                </h1>
                <h1 class="text-center"
                    style="
                    font-size: 4rem;
                    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    font-weight: bold;">
                    Digisatlink
                </h1>
                <h5 class="text-center text ps-5 mt-3 pe-5">PT XYZ sebagai penyedia jasa layanan satelit yang Lorem ipsum
                    dolor sit amet, consectetur adipisicing elit. Iusto, velit vero necessitatibus sit corporis explicabo
                    eligendi numquam earum nesciunt optio quas obcaecati! Quibusdam sequi nostrum similique voluptate earum,
                    recusandae magnam!</h5>
                <button class="btn btn-primary mt-4 px-5 py-3"
                    style="
                    font-size: 1.25rem;
                    border-radius: 25px;
                    box-shadow: 0 10px 58px rgba(0, 29, 176, 0.97);
                    transition: all 0.3s ease;">
                    Get Started
                </button>
                <br class="mb-5 pb-5"><br><br><br>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-12 d-flex justify-content-center align-items-center flex-column">
                <h1 class="text-center"
                    style="
                    font-size: 4rem;
                    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    font-weight: bold;">
                    Pilih Produkmu
                </h1>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card" style="background: rgba(0, 0, 0, 0.1); backdrop-filter: blur(10px);">
                            <div class="card-body p-0" style="position: relative;">
                                <img src="{{ asset('assets/images/produk.png') }}" class="w-100"
                                    style="height: 400px; object-fit: cover;">
                                <div
                                    style="position: absolute; bottom: 20px; left: 20px; color: white; text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.5);">
                                    <h2 class="card-title mb-0 fw-bold">Fixed Satmobile</h2>
                                    <p class="card-text">Data Standar Tanpa Batas, Prioritas Jaringan, Dukungan Prioritas
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card" style="background: rgba(0, 0, 0, 0.1); backdrop-filter: blur(10px);">
                            <div class="card-body p-0" style="position: relative;">
                                <img src="{{ asset('assets/images/produk2.png') }}" class="w-100"
                                    style="height: 400px; object-fit: cover;">
                                <div
                                    style="position: absolute; bottom: 20px; left: 20px; color: white; text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.5);">
                                    <h2 class="card-title mb-0 fw-bold">Land Satmobile</h2>
                                    <p class="card-text">Data Daratan Tanpa Batas, Dalam Perjalanan + Penggunaan Laut
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6 mt-5">
                <div id="carouselExampleCaptions" class="carousel slide ms-1 ps-2" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="3" aria-label="Slide 4"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active" data-bs-interval="3000">
                            <img src="{{ asset('assets/images/card-bg.png') }}" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-md-block text-start position-absolute pe-5" style="bottom: 30%; left: 5%;">
                                <h2 class="fw-bold">50 GB</h2>
                                <p>Layanan internet satelit dengan kecepatan >200mbps jika belum mencapai batas kuota diatas.</p>
                                <a href="{{ route('product.index') }}" class="btn btn-outline-warning">Lihat Produk</a>
                            </div>
                        </div>
                        <div class="carousel-item" data-bs-interval="3000">
                            <img src="{{ asset('assets/images/card-bg.png') }}" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-md-block text-start position-absolute pe-5" style="bottom: 30%; left: 5%;">
                                <h2 class="fw-bold">1 TB</h2>
                                <p>Layanan internet satelit dengan kecepatan >200mbps jika belum mencapai batas kuota diatas.</p>
                                <a href="{{ route('product.index') }}" class="btn btn-outline-warning">Lihat Produk</a>
                            </div>
                        </div>
                        <div class="carousel-item" data-bs-interval="3000">
                            <img src="{{ asset('assets/images/card-bg.png') }}" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-md-block text-start position-absolute pe-5" style="bottom: 30%; left: 5%;">
                                <h2 class="fw-bold">3 TB</h2>
                                <p>Layanan internet satelit dengan kecepatan >200mbps jika belum mencapai batas kuota diatas.</p>
                                <a href="{{ route('product.index') }}" class="btn btn-outline-warning">Lihat Produk</a>
                            </div>
                        </div>
                        <div class="carousel-item" data-bs-interval="3000">
                            <img src="{{ asset('assets/images/card-bg.png') }}" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-md-block text-start position-absolute pe-5" style="bottom: 30%; left: 5%;">
                                <h2 class="fw-bold">5 TB</h2>
                                <p>Layanan internet satelit dengan kecepatan >200mbps jika belum mencapai batas kuota diatas.</p>
                                <a href="{{ route('product.index') }}" class="btn btn-outline-warning">Lihat Produk</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-5">
                <h1 class="text-end mt-5"
                    style="
                font-size: 4rem;
                background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                font-weight: bold;">
                    Layanan Kami
                </h1>
                <h5 class="text-end text ps-2 mt-3">Dapatkan berbagai keuntungan melalui produk pada layanan kami</h5>
            </div>
        </div>

        <div class="row mt-5 mx-3">
            <div class="col-md-12 mt-5 d-flex justify-content-center align-items-center flex-column">
                <h1 class="text-center mt-5"
                    style="
                    font-size: 4rem;
                    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    font-weight: bold;">
                    Tunggu Apalagi? Dapatkan Produkmu Sekarang!
                </h1>
                <a class="btn btn-primary mt-4 px-5 py-3" href="{{ route('product.index') }}"
                    style="
                    font-size: 0.75rem;
                    border-radius: 25px;
                    box-shadow: 0 10px 58px rgba(0, 29, 176, 0.97);
                    transition: all 0.3s ease;">
                    Beli Produk
                </a>
                <br class="mb-5 pb-5"><br>
                <h3 class="text-center mt-5">Ingin mendapatkan info lebih banyak?</h3>
                <button class="helpdesk btn btn-warning mt-3 px-5 py-3" data-bs-toggle="modal" data-bs-target="#helpdeskModal"
                    style="
                    font-size: 0.6rem;
                    border-radius: 25px;
                    box-shadow: 0 10px 58px rgba(0, 29, 176, 0.97);
                    transition: all 0.3s ease;">
                    <i class="bi bi-telephone"></i> Hubungi Kami
                </button>
            </div>
        </div>
    </div>
@endsection
