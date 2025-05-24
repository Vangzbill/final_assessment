@extends('admin.pages.layout-admin')
@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Pesanan</h3>
                    <p class="text-subtitle text-muted">Data data pesanan yang dilakukan pelanggan</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">Pesanan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">All
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Order ID</th>
                                <th>Tanggal Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Produk</th>
                                <th>Jenis Pengiriman</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    @include('admin.pages.order.modal')

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let table = $('#table1').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url('/admin/pesanan') }}',
                    type: "GET",
                    error: function(xhr, status, error) {
                        console.log("AJAX Error: ", xhr.responseText);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'unique_order',
                        name: 'unique_order',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'order_date',
                        name: 'order_date',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'produk',
                        name: 'produk',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'jenis_pengiriman',
                        name: 'jenis_pengiriman',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'total_harga',
                        name: 'total_harga',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#table1_filter input').unbind().bind('keypress', function(e) {
                if (e.keyCode === 13) {
                    table.search(this.value).draw();
                }
            });

            $('#table1').on('click', '.update-status-btn', function() {
                let orderId = $(this).data('id');

                Swal.fire({
                    title: "Konfirmasi Update Pengiriman",
                    text: "Apakah kamu yakin ingin update status pesanan ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Update!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/pesanan/update-status/${orderId}`, {
                                method: "GET",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                        "content")
                                },
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status == 'success') {
                                    Swal.fire({
                                        title: "Berhasil!",
                                        text: "Pesanan berhasil diupdate.",
                                        icon: "success",
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location
                                            .reload();
                                    });
                                } else {
                                    Swal.fire("Gagal!", "Terjadi kesalahan saat mengedit.",
                                        "error");
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                            });
                    }
                });
            });

            $('#table1').on('click', '.view-order-btn', function() {
                let orderId = $(this).data('id');

                $.ajax({
                    url: `/admin/pesanan/${orderId}`,
                    type: "GET",
                    success: function(data) {
                        if (data.status === 'success') {
                            let order = data.data;

                            $('#modal-order-id').text(order.unique_order);
                            $('#modal-order-date').text(order.order_date);
                            $('#modal-payment-date').text(order.payment_date);
                            $('#modal-customer').text(order.customer.nama_perusahaan);
                            $('#modal-product').text(order.produk.nama_produk);
                            $('#modal-shipping').text(order.jenis_pengiriman);
                            $('#modal-kit-sn').text(order.kit_serial_number);
                            $('#modal-total').text("Rp " + order.total_harga.toLocaleString());
                            $('#modal-status-list').empty();

                            order.status_list.forEach(status => {
                                $('#modal-status-list').append(`
                                    <li class="list-group-item">
                                        <strong>${status.nama_status}</strong><br>
                                        <small>${status.tanggal}</small><br>
                                        <em>${status.keterangan}</em>
                                    </li>
                                `);
                            });

                            $('#orderModal').modal('show');
                        } else {
                            alert("Gagal mengambil data pesanan!");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
