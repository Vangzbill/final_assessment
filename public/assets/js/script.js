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

    let chatMessages = $("#chatMessages");

    function addMessage(sender, message, isBot = false) {
        let messageElement = $("<div>").addClass("message").text(message);

        if (isBot) {
            messageElement.addClass("bot");
        } else {
            messageElement.addClass("user");
        }

        chatMessages.append(messageElement);
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    addMessage("Bot", "Hallo! Ada yang bisa saya bantu?", true);

    $("#chatForm").submit(function (event) {
        event.preventDefault();
        let input = $("#chatInput");
        let message = input.val().trim();

        if (message) {
            addMessage("You", message, false);

            $.ajax({
                url: "http://127.0.0.1:5000/api/chat",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    question: message,
                }),
                success: function(response) {
                    addMessage("Bot", response.answer, true);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", status, error);
                    addMessage("Bot", "Maaf, terjadi kesalahan saat mendapatkan jawaban!", true);
                }
            });

            input.val("");
        }
    });

});
