@extends('layout.blank')
@section('content')
    @include('components.navbar')

    <div class="container" style="margin-top: -60px;">
        <div class="row pt-5 pe-3 ps-3 mb-5">
            <div class="col-md-12 d-flex justify-content-center align-items-center flex-column">
                <h1 class="text-center mt-5"
                    style="
                        font-size: 4rem;
                        background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        font-weight: bold;">
                    Chatbot
                </h1>
            </div>
        </div>
    </div>

    <div class="chat-container">
        <div class="chat-messages" id="chatMessages"></div>
        <div class="chat-input">
            <form id="chatForm" class="d-flex w-100">
                <input type="text" id="chatInput" placeholder="Masukkan pertanyaan disini..." required>
                <button type="submit" class="btn-chatbot"><i class="bi bi-send"></i></button>
            </form>
        </div>
    </div>
@endsection

