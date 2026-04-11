<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – KasirKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0fdf4; }
    </style>
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center">

    <div class="card border rounded-3 p-4" style="width: 100%; max-width: 360px;">
        <h5 class="fw-bold mb-1">KasirKu</h5>
        <p class="text-muted small mb-4">Masuk ke akun Anda</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small">Email</label>
                <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label small">Password</label>
                <input type="password" name="password" class="form-control form-control-sm" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Masuk</button>
        </form>

        @if ($errors->any())
            <div class="alert alert-danger mt-3 mb-0 small">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>