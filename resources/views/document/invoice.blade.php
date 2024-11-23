<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Inovice</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap");

        body {
            margin: 2rem 5rem;
            font-family: "Roboto", sans-serif;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 0.5rem;
            font-weight: 400;
            width: 100%;
        }

        .row {
            display: table-row;
        }

        .label,
        .colon,
        .value {
            display: table-cell;
        }

        .label {
            font-size: 1rem;
            text-align: left;
            font-weight: 400;
        }

        .colon {
            width: 2%;
            text-align: center;
        }

        td {
            padding: 0;
            margin: 0;
        }

        .divider {
            margin: 0em;
            border-top: 1px solid black;
        }

        .red {
            color: red;
        }

        .underline {
            text-decoration: underline;
        }

        .bold-500 {
            font-weight: 500;
        }

        .bold-600 {
            font-weight: 600;
        }

        .bold-700 {
            font-weight: 700;
        }

        .width-30 {
            min-width: 1%;
            width: 30%;
            max-width: 3rem;
        }

        .width-38 {
            min-width: 1%;
            width: 38%;
            max-width: 3rem;
        }

        .width-43 {
            min-width: 2%;
            width: 65%;
            max-width: 6rem;
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .text-left {
            text-align: left;
        }

        .float-left {
            float: left;
        }

        .float-right {
            float: right;
        }

        .blue {
            color: blue;
        }

        table.fill,
        th.fill,
        td.fill {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <img src="assets/images/logo.png" width="200" height="100" alt="logo xyz" />
    @php
        $sum_amount = 0;
    @endphp
    <br />
    <table style="width: 100%; display: table">
        <tr style="width: 100%">
            <td style="width: 50%; text-align: left">
            </td>
            <td style="width: 50%; text-align: right">
                <div style="display: inline-block">
                    <span style="display: block; font-size: 2rem" class="bold-700">INVOICE</span>
                    <span style="display: block; font-size: 1.1rem">{{ $nomor_invoice }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="table" cellspacing="0">
        <tr class="row">
            <td class="label width-30">Invoice Date</td>
            <td class="colon">:</td>
            <td class="value">{{ $invoice_date }}</td>
        </tr>
        <tr class="row">
            <td class="label width-30">Jatuh Tempo</td>
            <td class="colon">:</td>
            <td class="value">{{ $jatuh_tempo }}</td>
        </tr>
    </table>
    <br />
    <table style="width: 100%; display: table">
        <tr style="width: 100%">
            <td style="width: 50%; vertical-align: top">
                <span style="display: block" class="bold-700">Nama Badan</span>
                <span style="display: block">{{ $jenis_perusahaan . ' ' . $nama_perusahaan }}</span>
            </td>
            <td style="width: 50%; text-align: left">
                <span style="display: block" class="bold-700">Detail</span>
                <table class="table">
                    <tr class="row">
                        <td class="label width-30" style="vertical-align: top">Address</td>
                        <td class="colon">:</td>
                        <td class="value">
                            {{ $alamat_perusahaan }}
                        </td>
                    </tr>
                    <tr class="row">
                        <td class="label width-30" style="vertical-align: top">NPWP</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $npwp_perusahaan }}</td>
                    </tr>
                    <tr class="row">
                        <td class="label width-30" style="vertical-align: top">Amount</td>
                        <td class="colon">:</td>
                        <td class="value">{!! formatRupiah($amount) !!}</td>
                    </tr>
                    <tr class="row">
                        <td class="label width-30" style="vertical-align: top">Terbilang</td>
                        <td class="colon">:</td>
                        <td class="value">
                            {{ $terbilang }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div style="margin: 4.2rem 0">
        <span>Penyediaan Sewa dan Managed Service Layanan {{ $nama_layanan }}
            untuk {{ $nama_perusahaan }}</span>
    </div>
    <div>
        <span class="bold-700">{{ $nama_layanan }} - One Time Charge</span>
        <table style="border-collapse: separate; border-spacing: 0 0.5rem; width: 100%">
            <tr>
                <td style="text-align: left">No.</td>
                <td style="text-align: left">Description</td>
                <td style="text-align: left">Speed</td>
                <td style="text-align: left">Period</td>
                <td style="text-align: left">Unit Price</td>
                <td style="text-align: left">Amount</td>
            </tr>
            @foreach ($recaps as $recap)
                @php
                    $sum_amount += $recap['amount'];
                @endphp
                <tr>
                    <td style="text-align: left;">{{ $loop->iteration }}</td>
                    <td style="text-align: left;">
                        {{ $recap['deskripsi'] }}
                    </td>
                    <td style="text-align: left;">
                        {{ $recap['speed'] }}
                    </td>
                    <td style="text-align: left;">
                        {{ $recap['period'] }}
                    </td>
                    <td style="text-align: left;">
                        {!! formatRupiah($recap['unit_price']) !!}
                    </td>
                    <td style="text-align: left;">
                        {!! formatRupiah($recap['amount']) !!}
                    </td>
                </tr>

                <tr>
                    <td colspan="4"></td>
                    <td class="bold-700" style="text-align: left;">
                        Sub Total
                    </td>
                    <td style="text-align: left;">{!! formatRupiah($recap['amount']) !!}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"></td>
                <td class="bold-700" style="text-align: left;">
                    Total Amount Due
                </td>
                <td style="text-align: left;">{!! formatRupiah($sum_amount) !!}</td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td class="bold-700" style="text-align: left;">
                    PPN 11%
                </td>
                <td style="text-align: left;">- {!! formatRupiah($ppn) !!}</td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td class="bold-700" style="text-align: left;">
                    Total Biaya Keseluruhan
                </td>
                <td style="text-align: left;">{!! formatRupiah($sum_amount - $ppn) !!}</td>
            </tr>
        </table>
    </div>
</body>

</html>
