<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Employee Panel</a>
            <div class="navbar-nav ms-auto">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="mb-4">
            <h2>Selamat datang, {{ auth()->user()->name }}!</h2>
            <p class="text-muted">Ringkasan transaksi dan arahan restok.</p>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Pemasukan</h5>
                        <p class="card-text fs-4">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pemasukan Harian</h5>
                        <p class="card-text fs-4">Rp {{ number_format($dailyRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pemasukan Bulanan</h5>
                        <p class="card-text fs-4">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body text-dark">
                        <h5 class="card-title">Pemasukan Tahunan</h5>
                        <p class="card-text fs-4">Rp {{ number_format($yearlyRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Jumlah Member</h5>
                        <p class="card-text display-6">{{ $memberCount }}</p>
                        <p class="text-muted mb-0">Total customer member.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Jumlah Non Member</h5>
                        <p class="card-text display-6">{{ $nonMemberCount }}</p>
                        <p class="text-muted mb-0">Total customer non-member.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        Produk yang perlu di restok
                    </div>
                    <div class="card-body">
                        @if($lowStockProducts->isEmpty())
                            <div class="alert alert-success mb-0">Semua produk stoknya cukup.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Produk</th>
                                            <th>Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lowStockProducts as $product)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->stock }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-3">
                <a href="{{ route('employee.products') }}" class="btn btn-primary w-100">List Products</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('employee.transactions.create') }}" class="btn btn-success w-100">Create Transaction</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('employee.orders') }}" class="btn btn-info w-100">Transaction History</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>