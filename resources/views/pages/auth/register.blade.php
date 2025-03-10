@extends('layout.blank')
@section('content')
    @include('components.navbar')
    <div class="container"  style="margin-top: -40px;">
        <div class="row pt-5 pe-3 ps-3 mb-5">
            <div class="col-md-12 d-flex justify-content-center align-items-center flex-column">
                <h1 class="text-center mt-5"
                    style="
                        font-size: 4rem;
                        background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        font-weight: bold;">
                    Register
                </h1>

                <form method="POST" class="mt-5 w-100" style="max-width: 400px;">
                    @csrf

                    <div class="mb-3">
                        <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                        <input type="text" class="form-control bg-transparent" id="nama_perusahaan" name="nama_perusahaan" required>
                    </div>

                    <div class="mb-3">
                        <label for="email_perusahaan" class="form-label">Email Perusahaan</label>
                        <input type="email" class="form-control bg-transparent" id="email_perusahaan" name="email_perusahaan" required>
                    </div>

                    <div class="mb-3">
                        <label for="no_telp_perusahaan" class="form-label">Nomor Telepon Perusahaan</label>
                        <input type="text" class="form-control bg-transparent" id="no_telp_perusahaan" name="no_telp_perusahaan" required>
                    </div>

                    <div class="mb-3">
                        <label for="npwp_perusahaan" class="form-label">NPWP Perusahaan</label>
                        <input type="text" class="form-control bg-transparent" id="npwp_perusahaan" name="npwp_perusahaan" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control bg-transparent" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control bg-transparent" id="password" name="password" required>
                            <button class="btn btn-outline-primary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control bg-transparent" id="alamat" name="alamat" required></textarea>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="provinsi_id" class="form-label">Provinsi</label>
                            <select class="form-select" id="provinsi_id" name="provinsi_id" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kota_id" class="form-label">Kota / Kabupaten</label>
                            <select class="form-select" id="kota_id" name="kota_id" required>
                                <option value="">Pilih Kota / Kabupaten</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="kecamatan_id" class="form-label">Kecamatan</label>
                            <select class="form-select" id="kecamatan_id" name="kecamatan_id" required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kelurahan_id" class="form-label">Kelurahan / Desa</label>
                            <select class="form-select" id="kelurahan_id" name="kelurahan_id" required>
                                <option value="">Pilih Kelurahan / Desa</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                <div class="text-small text-center mt-3">
                    <span>Sudah punya akun?<a href="{{ route('login') }}">Login sekarang</a></span>
                </div>

            </div>
        </div>
    </div>

@endsection
