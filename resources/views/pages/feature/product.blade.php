@extends('layout.blank')
@section('content')
    @include('components.navbar')
    <div class="container mt-2">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center mt-5"
                    style="
                        font-size: 4rem;
                        background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        font-weight: bold;">
                    Product
                </h1>
            </div>
        </div>
        <div class="row mt-5">
            @foreach ($product as $pr)
                <div class="col-md-4 d-flex align-items-stretch">
                    <div class="card mb-4 w-100">
                        <img src="{{ asset('assets/images/' . $pr->image) }}" class="card-img-top" alt="{{ $pr->nama }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $pr->nama }}</h5>
                            <p class="card-text flex-grow-1">{{ $pr->deskripsi }}</p>
                            {{-- <div class="justify-content-end d-flex text">
                                <a href="" class="btn btn-primary">Detail</a>
                            </div> --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection
