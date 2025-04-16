<!-- Chatbot Card -->
<div id="chatbot-card" class="position-fixed bottom-0 end-0 m-4 shadow rounded"
    style="width: 300px; display: none; z-index: 1051; background: #fff; border: 1px solid #ccc;">
    <div class="d-flex justify-content-between align-items-center bg-primary text-white p-2 rounded-top">
        <strong>Chatbot</strong>
        <button class="btn btn-sm text-white" id="chatbot-close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div id="chatbot-messages" style="height: 250px; overflow-y: auto; padding: 10px;"></div>
    <div class="input-group border-top">
        <input type="text" id="chatbot-input" class="form-control" placeholder="Ketik pertanyaan..." />
        <button class="btn btn-primary" id="chatbot-send"><i class="bi bi-send"></i></button>
    </div>
</div>
