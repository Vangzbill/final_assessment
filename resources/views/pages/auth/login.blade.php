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
                    Login
                </h1>

                <form action="{{ route('login') }}" method="POST" class="mt-5 w-100" style="max-width: 400px;">
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-primary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <div class="text-small text-center mt-3">
                    <span>Belum punya akun?<a href="">Daftar sekarang</a></span>
                </div>
            </div>
        </div>
    </div>
@endsection
