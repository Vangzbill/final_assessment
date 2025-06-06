@extends('admin.pages.layout-admin')
@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tagihan</h3>
                    <p class="text-subtitle text-muted">Data data tagihan pelanggan</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">Tagihan</a></li>
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
                    <button id="generateBillingBtn" class="btn btn-primary mb-3 ">
                        <i class="bi bi-arrow-repeat"></i> Generate Billing
                    </button>
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Tagihan</th>
                                <th>Tanggal Tagihan</th>
                                <th>Pelanggan</th>
                                <th>Total Tagihan</th>
                                <th>Status</th>
                                <th>Jatuh Tempo</th>
                                <th>Bukti PPN</th>
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

    @include('admin.pages.billing.modal')

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let table = $('#table1').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('/admin/tagihan') }}",
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
                        data: 'tanggal_tagih',
                        name: 'tanggal_tagih',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'total_akhir',
                        name: 'total_akhir',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'jatuh_tempo',
                        name: 'jatuh_tempo',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'bukti_ppn',
                        name: 'bukti_ppn',
                        orderable: false,
                        searchable: false
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

            $(document).on('click', '.view-billing-btn', function() {
                var billingId = $(this).data('id');

                $.ajax({
                    url: "/admin/billing/" + billingId,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        $("#modal-billing-id").text(response.id);
                        $("#modal-customer").text(response.customer);
                        $("#modal-billing-date").text(response.tanggal_tagih);
                        $("#modal-out-date").text(response.jatuh_tempo);
                        $("#modal-pay-date").text(response.tanggal_pembayaran);
                        $("#modal-service").text(response.layanan);
                        $("#modal-sid").text(response.sid);
                        $("#modal-status").text(response.status);
                        $("#modal-total").text(response.total);

                        $("#billingModal").modal("show");
                    },
                    error: function() {
                        alert("Gagal mengambil data tagihan.");
                    }
                });
            });

            $(document).on('click', '.terima-btn', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Terima Tagihan?',
                    text: "Apakah data bukti ppn sudah sesuai?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Terima',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/api/billing/accept-ppn",
                            type: "POST",
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                billing_id: id
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message
                            });
                                $('#table1').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan saat menerima tagihan.'
                                });
                            }
                        });
                    }
                });
            });


            $(document).on('click', '.tolak-btn', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Tolak Tagihan?',
                    text: "Apakah data bukti ppn tidak sesuai?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tolak',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/api/billing/reject-ppn",
                            type: "POST",
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                billing_id: id
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message
                                });
                                $('#table1').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan saat menolak tagihan.'
                                });
                            }
                        });
                    }
                });
            });


            $('#generateBillingBtn').click(function() {
                Swal.fire({
                    title: 'Generate Billing?',
                    text: "Billing akan dibuat untuk kontrak node-link yang belum ditagihkan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, generate!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/admin/generate-billing",
                            type: "POST",
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message
                                });
                                $('#table1').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan saat generate billing.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
