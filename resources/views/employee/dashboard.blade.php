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
        <h1>Employee Dashboard</h1>
        <div class="row">
            <div class="col-md-3">
                <a href="{{ route('employee.products') }}" class="btn btn-primary w-100 mb-3">List Products</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('employee.transactions.create') }}" class="btn btn-success w-100 mb-3">Create Transaction</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('employee.orders') }}" class="btn btn-info w-100 mb-3">Transaction History</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>