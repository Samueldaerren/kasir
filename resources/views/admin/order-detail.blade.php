<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f5f7;
        }
        .receipt-card {
            max-width: 720px;
            margin: 0 auto 2rem;
            border-radius: 1rem;
            overflow: hidden;
        }
        .receipt-header {
            background: #2c3e50;
            color: #fff;
            padding: 1rem 1.5rem;
        }
        .receipt-header h1 {
            font-size: 1.3rem;
            margin-bottom: 0.2rem;
        }
        .receipt-header small {
            color: #d1d5db;
        }
        .receipt-section {
            padding: 1.25rem 1.5rem;
            font-size: 0.95rem;
        }
        .receipt-section + .receipt-section {
            border-top: 1px dashed #dee2e6;
        }
        .receipt-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.45rem 0;
            border-bottom: 1px solid rgba(0,0,0,.05);
        }
        .receipt-line:last-child {
            border-bottom: none;
        }
        .receipt-label {
            color: #6b7280;
            font-size: 0.85rem;
        }
        .receipt-total {
            font-weight: 700;
        }
        .receipt-small {
            font-size: 0.88rem;
            color: #6b7280;
        }
        .receipt-summary {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 0.7rem;
            padding: 1rem;
            margin-top: 1rem;
        }
        .receipt-summary .receipt-line {
            border-bottom: none;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .receipt-card { box-shadow: none; margin: 0; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="receipt-card shadow-sm bg-white">
            <div class="receipt-header d-flex justify-content-between align-items-start">
                <div>
                    <h1>Detail Pesanan</h1>
                    <small>Order #{{ $order->id }}</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-info">{{ optional($order->customer)->phone_number === '0000000000' ? 'Non-member' : 'Member' }}</span>
                    <p class="receipt-small mb-0 mt-2">{{ optional($order->customer)->name ?? 'Customer tidak diketahui' }}</p>
                </div>
            </div>

            <div class="receipt-section">
                <div class="receipt-line">
                    <div>
                        <div class="receipt-label">Toko</div>
                        <strong>Kasir Sederhana</strong>
                    </div>
                    <div class="text-end receipt-small">Jl. Wikrama No. 123<br>Kota Bogor</div>
                </div>
                <div class="receipt-line">
                    <span class="receipt-label">Tanggal</span>
                    <strong>{{ $order->sale_date }}</strong>
                </div>
            </div>

            <div class="receipt-section">
                <h5 class="mb-3">Detail Produk</h5>
                @foreach($order->detailOrders as $detail)
                    <div class="receipt-line">
                        <div>
                            <strong>{{ $detail->produk->name }}</strong><br>
                            <span class="receipt-small">{{ $detail->amount }} x {{ $detail->harga_rupiah }}</span>
                        </div>
                        <strong>{{ $detail->sub_total_rupiah }}</strong>
                    </div>
                @endforeach

                <div class="receipt-summary">
                    <div class="receipt-line">
                        <span>Total Harga</span>
                        <strong>{{ $order->total_price_rupiah }}</strong>
                    </div>
                    <div class="receipt-line">
                        <span>Poin Digunakan</span>
                        <strong>{{ $order->point_used ? 'Rp ' . number_format($order->point_used, 0, ',', '.') : 'Rp 0' }}</strong>
                    </div>
                    <div class="receipt-line">
                        <span>Bayar</span>
                        <strong>{{ $order->total_pay_rupiah }}</strong>
                    </div>
                    <div class="receipt-line receipt-total">
                        <span>Kembalian</span>
                        <strong>{{ $order->total_return_rupiah }}</strong>
                    </div>
                </div>
            </div>

            <div class="receipt-section d-flex justify-content-between align-items-center no-print">
                <a href="{{ route('admin.orders') }}" class="btn btn-secondary">Kembali</a>
                <button type="button" class="btn btn-primary" onclick="window.print()">Cetak Struk</button>
            </div>

            <div class="receipt-section receipt-small text-center">
                Terima kasih telah melakukan transaksi.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
