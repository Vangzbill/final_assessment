<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pernyataan Aktivasi Layanan Starlink Bussiness Service (SBS)</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .container {
            width: 100vw;
            margin: auto;
            padding: 40px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header img {
            width: 100px;
        }

        .header h1 {
            font-size: 18px;
            margin: 10px 0;
        }

        .header p {
            margin: 5px 0;
        }

        .content {
            margin-top: 10px;
            margin-bottom: 100px;
        }

        .content table {
            margin-left: 20px;
            width: 100%;
            border-collapse: collapse;
        }

        .content table td {
            padding: 5px;
            vertical-align: top;
        }

        .content .declaration {
            margin-left: 25px;
            margin-top: 20px;
            text-align: justify;
        }

        .signature {
            margin-top: -10px;
        }

        .signature table {
            border-collapse: collapse;
            width: 100%;
        }

        .signature td {
            text-align: center;
        }

        .bold-700 {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1
            style="position: absolute;top:40%;color:#858282;padding: 10px;opacity: 0.2;font-size: 75px;transform: rotate(45deg)">
            {{ $unique_order ?? '-' }}</h1>
        <table style="width: 100%; display: table; border: 1px solid #000">
            <tr style="width: 100%;">
                <td style="width: 20%; text-align: left; border-right: 1px solid #000;">
                    <img src="assets/images/logo_dark.png" height="90" alt="telkomsat" />
                </td>
                <td style="width: 80%; text-align: center">
                    <div style="display: inline-block">
                        <span style="display: block; font-size: 2rem" class="bold-700">Surat Pernyataan Aktivasi
                            Layanan</span>
                        <hr style="margin: 5px 0">
                        <span style="display: block; font-size: 0.9rem">{{ $order['nomor_kontrak'] }}</span>
                    </div>
                </td>
            </tr>
        </table>
        <hr style="margin-top: 20px;">
        <div class="content">
            <p><strong>Lokasi:</strong> {{ $order['sid'] }}</p>
            <table>
                <tr>
                    <td><strong>DATA PELANGGAN</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Nama Pelanggan</td>
                    <td>: {{ $order['nama_perusahaan'] }}</td>
                </tr>
                <tr>
                    <td>PIC</td>
                    <td>: {{ $order['nama'] }}</td>
                </tr>
                <tr>
                    <td>No. HP</td>
                    <td>: {{ $order['no_telp'] }}</td>
                </tr>
                <tr>
                    <td><strong>LAYANAN</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Jenis Pekerjaan</td>
                    <td>: <strong>AKTIVASI</strong></td>
                </tr>
                <tr>
                    <td>Jenis layanan</td>
                    <td>: <strong>{{ $order['nama_perangkat'] }}</strong></td>
                </tr>
                <tr>
                    <td>ServicePlan</td>
                    <td>: <strong>{{ $order['nama_layanan'] }}</strong></td>
                </tr>
                <tr>
                    <td>KIT Serial Number</td>
                    <td>: <strong>{{ $order['kit_serial_number'] }}</strong></td>
                </tr>
                <tr>
                    <td>Layanan Tambahan</td>
                    <td>: ........................................................................</td>
                </tr>
                <br>
                <tr>
                <tr>
                    <td colspan="2"><strong style="white-space: nowrap;">Tanggal Aktivasi:
                            {{ $order['tgl_aktivasi'] }}</strong></td>
                </tr>
                </tr>
            </table>
            <div class="declaration">
                <p><strong>PERNYATAAN</strong></p>
                <p>Layanan telah selesai dipasang dan dites, telah disepakati dan diterima oleh pelanggan serta
                    dinyatakan telah digunakan terhitung sejak tanggal Surat Pernyataan Aktivasi ini.</p>
                <p style="margin-top: 20px;"><strong>Biaya layanan akan dihitung mulai Tanggal Aktivasi.</strong></p>
            </div>
        </div>
        <div class="signature" style="">
            <table>
                <tr>
                    <td style="width: 45%;">
                        <p><strong>PENYEDIA</strong></p>
                        <p><strong>PT. XYZ</strong></p>
                        <img src='assets/images/spa-no-bg.png' width="100px" height="75px" alt="ttd" />
                        {{-- <h1 style="font-size: 60;">{{ '' }}</h1> --}}
                        <p><strong>Mr XYZ</strong></p>
                        <hr>
                        <p>Manager Service Activation</p>
                    </td>
                    <td style="width: 30%;"></td>
                    <td style="width: 35%; ">
                        <p style="margin-top: -10px; margin-bottom: 0px;">Bogor, {{ $order['tgl_aktivasi'] }}</p>
                        <p><strong style="margin-top: -60px;">PELANGGAN</strong></p>
                        @if ($order['is_ttd'] == 1)
                            <h1 style="font-size: 37;">{{ $order['initial_nama'] }}</h1>
                        @else
                            <h1 style="font-size: 37;">{{ '' }}</h1>
                        @endif
                        <strong>{{ $order['nama'] }}</strong>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
