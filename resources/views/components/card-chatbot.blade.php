<div id="chatbot-card" class="position-fixed end-0 me-4 shadow"
    style="width: 350px; bottom: 100px; display: none; z-index: 1051;
           background: #fff; border: 1px solid #ccc; border-radius: 25px; overflow: hidden;">

    <!-- Header -->
    <div class="d-flex bg-primary text-white p-3">
        <strong>Chatbot</strong>
    </div>

    <!-- Message Container -->
    <div id="chatbot-messages" style="height: 430px; overflow-y: auto; padding: 10px;"></div>

    <!-- Input Bubble Style -->
    <div class="p-3 bg-white">
        <div class="d-flex align-items-center px-3 py-2 shadow-sm"
            style="background-color: #f4f0fa; border-radius: 999px;">
            <!-- Input -->
            <input type="text" id="chatbot-input" class="form-control border-0 bg-transparent shadow-none"
                placeholder="Apa yang bisa saya bantu..." style="font-size: 0.95rem;" />

            <!-- Send Button -->
            <button class="btn p-0 ms-0 text-primary" id="chatbot-send" style="border: none;">
                <i class="bi bi-send-fill fs-5"></i>
            </button>
        </div>
    </div>

</div>
