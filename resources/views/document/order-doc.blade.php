<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Pemesanan - {{ $data['unique_order'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2cm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }

        .logo {
            max-width: 200px;
            max-height: 100px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24pt;
            margin: 0 0 10px 0;
        }

        .header p {
            margin: 5px 0;
            font-size: 12pt;
        }

        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #000;
        }

        .info-section h2 {
            font-size: 14pt;
            margin: 0 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }

        .info-section p {
            margin: 8px 0;
            font-size: 12pt;
        }

        .cost-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        .cost-table th {
            text-align: left;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #000;
        }

        .cost-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .cost-row.total {
            border-top: 2px solid #000;
            border-bottom: none;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
        }

        .cost-label {
            font-weight: normal;
        }

        .cost-value {
            text-align: right;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="assets/images/logo_dark.png" alt="logo xyz" class="logo"/>
            <h1>Rincian Pemesanan</h1>
            <p><strong>Nomor Order:</strong> {{ $data['unique_order'] }}</p>
            <p><strong>Tanggal:</strong> {{ $data['order_date'] }}</p>
        </div>

        <div class="info-section">
            <h2>Informasi Pemesan</h2>
            <p><strong>Nama:</strong> {{ $data['penerima']['nama'] }}</p>
            <p><strong>Email:</strong> {{ $data['penerima']['email'] }}</p>
            <p><strong>No. Telepon:</strong> {{ $data['penerima']['no_telp'] }}</p>
        </div>

        <div class="info-section">
            <h2>Detail Produk</h2>
            <p><strong>Nama Perangkat:</strong> {{ $data['nama_perangkat'] }}</p>
        </div>

        <div class="info-section">
            <h2>Rincian Biaya</h2>
            <div class="cost-row">
                <span class="cost-label">Harga Perangkat</span>
                <span class="cost-value">Rp {{ number_format($data['rincian']['harga_perangkat'], 0, ',', '.') }}</span>
            </div>
            <div class="cost-row">
                <span class="cost-label">Deposit Layanan</span>
                <span class="cost-value">Rp {{ number_format($data['rincian']['deposit_layanan'], 0, ',', '.') }}</span>
            </div>
            <div class="cost-row">
                <span class="cost-label">PPN</span>
                <span class="cost-value">Rp {{ number_format($data['rincian']['ppn'], 0, ',', '.') }}</span>
            </div>
            <div class="cost-row total">
                <span class="cost-label">Total Biaya</span>
                <span class="cost-value">Rp {{ number_format($data['rincian']['total_biaya'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</body>
</html>
