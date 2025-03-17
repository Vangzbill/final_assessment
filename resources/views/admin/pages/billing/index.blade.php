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
    <script>
        $(document).ready(function() {
            let table = $('#table1').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.billing') }}",
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
        });
    </script>
@endsection
