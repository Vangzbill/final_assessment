<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - Order {{ $order['order_id'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 20px; }
        .invoice-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .invoice-details { margin: 20px 0; }
        .invoice-details div { margin-bottom: 10px; }
        .invoice-table { width: 100%; border-collapse: collapse; }
        .invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .invoice-total { text-align: right; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="invoice-header">
        <img src="assets/images/logo_dark.png" width="200" height="100" alt="logo xyz" />
        <div>
            <h1>Invoice</h1>
            <p>Order {{ $order['order_id'] }}</p>
        </div>
    </div>

    <div class="invoice-details">
        <div>
            <strong>Contact Person:</strong> {{ $order['nama_cp'] }}<br>
            <strong>Email:</strong> {{ $order['email_cp'] }}<br>
            <strong>Phone:</strong> {{ $order['no_telp_cp'] }}
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $order['nama_perangkat'] }}</td>
                    <td>1</td>
                    <td>Rp {{ number_format($order['harga_perangkat'], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($order['harga_perangkat'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>{{ $order['nama_layanan'] }}</td>
                    <td>1</td>
                    <td>Rp {{ number_format($order['deposit_layanan'], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($order['deposit_layanan'], 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="invoice-total">
            <p>Subtotal: Rp {{ number_format($order['harga_perangkat'] + $order['deposit_layanan'], 0, ',', '.') }}</p>
            <p>PPN (10%): Rp {{ number_format($order['ppn'], 0, ',', '.') }}</p>
            <h3>Total: Rp {{ number_format($order['total_biaya'], 0, ',', '.') }}</h3>
        </div>
    </div>
</body>
</html>
