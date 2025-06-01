<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice Billing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .letterhead-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .letterhead-table td {
            border: none;
            padding: 10px;
            vertical-align: middle;
        }

        .logo-cell {
            width: 30%;
        }

        .logo {
            width: 200px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .company-header {
            text-align: center;
        }

        .company-header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }

        .company-header p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }

        .header-divider {
            border-bottom: 2px solid #000;
            margin: 20px 0;
        }

        .header {
            text-align: center;
            margin: 30px 0;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .company-info {
            float: left;
            width: 50%;
        }

        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }

        .clear {
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .total-section {
            float: right;
            width: 300px;
            margin-top: 20px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
        }

        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 70px;
        }
    </style>
</head>

<body>
    <table class="letterhead-table">
        <tr>
            <td class="logo-cell">
                <div class="logo">
                    <img src="assets/images/logo_dark.png" width="200" height="100" alt="logo xyz" />
                </div>
            </td>
            <td class="company-header">
                <h1>PT XYZ</h1>
                <p>Jl. Contoh No. 123, Jakarta Selatan, 12345</p>
                <p>Telp: (021) 123-4567 | Fax: (021) 123-4568</p>
                <p>Email: info@ptxyz.com</p>
                <p>Website: www.ptxyz.com</p>
            </td>
        </tr>
    </table>
    <div class="header-divider"></div>

    <div class="header">
        <div class="invoice-title">INVOICE</div>
    </div>

    <div class="invoice-info">
        <p>
            <strong>No. Invoice:</strong>
            INV/{{ $order['tahun'] }}/{{ $order['bulan'] }}/{{ $order['no_proforma_invoice'] }}<br>
            <strong>No. Invoice:</strong>
            INV/{{ $order['tahun'] }}/{{ $order['bulan'] }}/{{ $order['no_proforma_invoice'] }}<br>
            <strong>Tanggal tagih:</strong> {{ $order['tanggal_tagih'] }}<br>
            <strong>Periode:</strong> {{ $order['periode'] }}<br>
            <strong>Jatuh Tempo:</strong> {{ $order['jatuh_tempo'] }}
        </p>
    </div>

    <div class="clear"></div>

    <div class="client-info">
        <p>
            <strong>Kepada:</strong><br>
            {{ $order['nama_customer'] }}<br>
            {{ $order['alamat_customer'] }}<br>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Deskripsi Layanan</th>
                <th>Periode</th>
                <th>Harga</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Layanan {{ $order['nama_layanan'] }}</td>
                <td>{{ $order['periode'] }}</td>
                <td>Rp. {{ $order['total_tagihan'] }}</td>
                <td>Rp. {{ $order['total_tagihan'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <table>
            {{-- <tr>
                <td>Subtotal</td>
                <td>Rp. {{ $order['total_tagihan'] }}</td>
            </tr> --}}
            {{-- <tr>
            {{-- <tr>
                <td>PPN 11%</td>
                <td>Rp. {{ $order['total_ppn'] }}</td>
            </tr> --}}
            {{-- </tr> --}}
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>Rp. {{ $order['total_tagihan'] }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    {{-- <div class="payment-info"> --}}
    {{-- <p><strong>Informasi Pembayaran:</strong></p>
        <p>
            Bank [Nama Bank]<br>
            No. Rekening: [Nomor Rekening]<br>
            Atas Nama: PT Layanan Satelit
        </p> --}}
    {{-- </div> --}}

    <div class="signature">
        <div class="signature-box">
            <p>Disetujui oleh</p>
            <img src="assets/images/invoice_billing-no-bg.png" width="100px" height="75px" alt="signature" />
            <div class="signature-line"></div>
            <p>Divisi Billing PT XYZ</p>
        </div>
    </div>

    <div class="footer">
        <p><em>Terima kasih atas kerja sama Anda</em></p>
    </div>
</body>

</html>
