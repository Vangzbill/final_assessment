@extends('layout.blank')
@section('content')
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4 text-white text-center" style="background-color: #1C1C1D; max-width: 500px;">
            <div class="card-header border-0">
                <img src="{{ asset('assets/images/check.png') }}" alt="success" width="100" class="my-4">
                <h4 class="fw-bold mb-3">Payment Success</h4>
            </div>
            <hr>
            <div class="card-body">
                <p class="mt-3 mb-0 fs-5">Thank you for your purchase.</p>
                <p class="mt-0 fs-6">Your payment has been successfully processed.</p>

                <p class="mt-4 fs-6">Please click the button below to return to the mobile app.</p>
                <a href="" class="btn text-white fw-bold py-2 px-4 mt-3" style="background-color: #8158F4; border-radius: 30px;">Back to App</a>
            </div>
        </div>
    </div>
@endsection
