<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KasirKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>

    <nav class="navbar bg-white border-bottom px-3">
        <span class="navbar-brand fw-bold text-success mb-0">
        <img src="/logo.jpg" alt="Logo KasirKu" style="width: 30px; height: 30px; border-radius: 5px;">
            KasirKu
        </span>
        <a href="{{ route('login') }}" class="btn btn-success btn-sm px-3">Login</a>
    </nav>

    <section class="bg-success text-white text-center py-5">
        <div class="container py-4">
            <h1 class="display-5 fw-bold mb-3">Kelola Toko Lebih Mudah</h1>
            <p class="text-white-50 mb-1">Catat transaksi penjualan, pantau stok barang,</p>
            <p class="text-white-50 mb-4">dan lihat laporan harian — semuanya dalam satu sistem.</p>
            <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5 fw-semibold">Masuk Sekarang</a>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <h5 class="fw-bold text-center mb-4">Lokasi &amp; Kontak</h5>
            <div class="row justify-content-center g-3 text-center">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 py-2">
                        <div class="card-body">
                            <p class="text-muted small fw-semibold mb-2">ALAMAT</p>
                            <p class="mb-0 small">Jl. Wikrama No. 45<br>123, Kota Bogor</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 py-2">
                        <div class="card-body">
                            <p class="text-muted small fw-semibold mb-2">TELEPON</p>
                            <p class="mb-0 small">+62 21 5555-1234</p>
                            <p class="mb-0 small text-muted">Senin – Sabtu, 08.00–17.00</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 py-2">
                        <div class="card-body">
                            <p class="text-muted small fw-semibold mb-2">EMAIL</p>
                            <p class="mb-0 small">halo@kasirku.id</p>
                            <p class="mb-0 small text-muted">Respon dalam 1x24 jam</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4 text-center">
        <p class="fw-semibold mb-1">KasirKu</p>
        <p class="text-white-50 small mb-3">Sistem kasir sederhana untuk toko Anda.</p>
        <p class="text-white-50 small mb-0">&copy; 2025 KasirKu. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>