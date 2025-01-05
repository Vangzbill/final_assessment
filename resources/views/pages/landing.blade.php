@extends('layout.blank')
@section('content')
    <div class="container mt-5">
        <div class="row pt-5 pe-3 ps-3 mb-5">
            <div class="col-md-12 d-flex justify-content-center align-items-center flex-column">
                <h1 class="text-center mt-5" style="
                    font-size: 4rem;
                    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    font-weight: bold;">
                    Discover Our Product
                </h1>
                <h1 class="text-center" style="
                    font-size: 4rem;
                    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    font-weight: bold;">
                    Digital Touchpoint
                </h1>
                <h5 class="text-center text-white ps-5 mt-3 pe-5">PT XYZ sebagai penyedia jasa layanan satelit yang Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, velit vero necessitatibus sit corporis explicabo eligendi numquam earum nesciunt optio quas obcaecati! Quibusdam sequi nostrum similique voluptate earum, recusandae magnam!</h5>
                <button class="btn btn-primary mt-4 px-5 py-3" style="
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
                <h1 class="text-center" style="
                    font-size: 4rem;
                    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    font-weight: bold;">
                    Pilih Produkmu
                </h1>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                            <div class="card-body p-0" style="position: relative;">
                                <img src="{{ asset('assets/images/produk.png') }}" class="w-100" style="height: 400px; object-fit: cover;">
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                    <h5 class="card-title mb-0">Service 1</h5>
                                    <p class="card-text">Lorem ipsum dolor sit amet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                            <div class="card-body p-0" style="position: relative;">
                                <img src="{{ asset('assets/images/produk2.png') }}" class="w-100" style="height: 400px; object-fit: cover;">
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                    <h5 class="card-title mb-0">Service 2</h5>
                                    <p class="card-text">Lorem ipsum dolor sit amet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
