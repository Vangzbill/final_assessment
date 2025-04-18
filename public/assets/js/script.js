$('#togglePassword').on('click', function () {
    const passwordField = $('#password');
    const icon = $(this).find('i');

    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
        passwordField.attr('type', 'password');
        icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
});

$(document).ready(function () {
    $.ajax({
        url: "/api/wilayah/provinsi",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                let selectProvinsi = $("#provinsi_id");
                selectProvinsi.empty();
                selectProvinsi.append('<option value="">Pilih Provinsi</option>');

                response.data.forEach(function (provinsi) {
                    selectProvinsi.append(`<option value="${provinsi.id}">${provinsi.name}</option>`);
                });
            } else {
                console.error("Gagal mengambil data provinsi:", response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching provinsi:", error);
        }
    });

    $("#provinsi_id").change(function () {
        let provinsiId = $(this).val();
        if (provinsiId) {
            $.ajax({
                url: `/api/wilayah/kabupaten/${provinsiId}`,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    let selectKabupaten = $("#kota_id");
                    let selectKecamatan = $("#kecamatan_id");
                    let selectKelurahan = $("#kelurahan_id");
                    selectKabupaten.empty();
                    selectKecamatan.empty();
                    selectKelurahan.empty();
                    selectKabupaten.append('<option value="">Pilih Kota / Kabupaten</option>');
                    selectKecamatan.append('<option value="">Pilih Kecamatan</option>');
                    selectKelurahan.append('<option value="">Pilih Kelurahan / Desa</option>');

                    response.data.forEach(function (kabupaten) {
                        selectKabupaten.append(`<option value="${kabupaten.id}">${kabupaten.name}</option>`);
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching kabupaten:", error);
                }
            });
        }
    });

    $("#kota_id").change(function () {
        let kabupatenId = $(this).val();
        if (kabupatenId) {
            $.ajax({
                url: `/api/wilayah/kecamatan/${kabupatenId}`,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    let selectKecamatan = $("#kecamatan_id");
                    let selectKelurahan = $("#kelurahan_id");
                    selectKecamatan.empty();
                    selectKelurahan.empty();
                    selectKecamatan.append('<option value="">Pilih Kecamatan</option>');
                    selectKelurahan.append('<option value="">Pilih Kelurahan / Desa</option>');

                    response.data.forEach(function (kecamatan) {
                        selectKecamatan.append(`<option value="${kecamatan.id}">${kecamatan.name}</option>`);
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching kecamatan:", error);
                }
            });
        }
    });

    $("#kecamatan_id").change(function () {
        let kecamatanId = $(this).val();
        if (kecamatanId) {
            $.ajax({
                url: `/api/wilayah/kelurahan/${kecamatanId}`,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    let selectKelurahan = $("#kelurahan_id");
                    selectKelurahan.empty();
                    selectKelurahan.append('<option value="">Pilih Kelurahan / Desa</option>');

                    response.data.forEach(function (kelurahan) {
                        selectKelurahan.append(`<option value="${kelurahan.id}">${kelurahan.name}</option>`);
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching kelurahan:", error);
                }
            });
        }
    });

    $('#chatbot-toggle').on('click', function () {
        $('#chatbot-card').toggle();

        const icon = $('#chatbot-toggle .toggle-icon');
        if ($('#chatbot-card').is(':visible')) {
            icon.removeClass('bi-chat-dots').addClass('bi-x');
        } else {
            icon.removeClass('bi-x').addClass('bi-chat-dots');
        }
    });

    $('#chatbot-close').on('click', function () {
        $('#chatbot-card').hide();
        $('#chatbot-toggle .toggle-icon').removeClass('bi-x').addClass('bi-chat-dots');
    });

    $('#chatbot-send').on('click', function () {
        const message = $('#chatbot-input').val().trim();
        if (!message) return;

        addMessage(message, false);
        $('#chatbot-input').val('');

        $.ajax({
            url: "http://127.0.0.1:5000/api/chat",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({ question: message }),
            success: function (response) {
                addMessage(response.answer, true);
            },
            error: function (xhr, status, error) {
                console.error("Error:", status, error);
                addMessage("Maaf, terjadi kesalahan saat mendapatkan jawaban!", true);
            }
        });
    });

    function addMessage(message, isBot) {
        const bubbleClass = isBot ? 'bot-message' : 'user-message';
        const msg = `
            <div class="d-flex ${isBot ? 'justify-content-start' : 'justify-content-end'} mb-3">
                <div class="chat-bubble ${bubbleClass}">
                    ${message}
                </div>
            </div>`;
        $('#chatbot-messages').append(msg).scrollTop($('#chatbot-messages')[0].scrollHeight);
    }

    $('#chatbot-input').on('keypress', function (e) {
        if (e.which === 13) {
            $('#chatbot-send').click();
        }
    });

    $(window).on("scroll", function () {
        const navbar = $("#mainNavbar");
        if ($(this).scrollTop() > 10) {
            navbar.css({
                "background-color": "rgba(0, 123, 255, 0.8)",
                "backdrop-filter": "blur(6px)",
                "box-shadow": "0 2px 6px rgba(0, 0, 0, 0.1)"
            });
        } else {
            navbar.css({
                "background-color": "transparent",
                "box-shadow": "none",
                "backdrop-filter": "none"
            });
        }
    });

});
