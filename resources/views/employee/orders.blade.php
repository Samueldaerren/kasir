<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Employee Panel</a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('employee.dashboard') }}" class="nav-link">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Your Orders</h1>

            <form method="GET" action="{{ route('employee.orders') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label for="from_date" class="form-label small mb-1">Start Date</label>
                    <input id="from_date" type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate ?? '' }}">
                </div>
                <div class="col-auto">
                    <label for="to_date" class="form-label small mb-1">End Date</label>
                    <input id="to_date" type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate ?? '' }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('employee.orders.export', ['from_date' => $fromDate ?? null, 'to_date' => $toDate ?? null, 'date' => $date ?? null]) }}" class="btn btn-success btn-sm">Export Excel</a>
                </div>
            </form>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sale Date</th>
                    <th>Customer</th>
                    <th>Total Price</th>
                    <th>Total Pay</th>
                    <th>Total Return</th>
                    <th>Used Points</th>
                    <th>Earned Points</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->sale_date }}</td>
                        <td>{{ $order->customer->name }}</td>
                        <td>{{ $order->total_price_rupiah }}</td>
                        <td>{{ $order->total_pay_rupiah }}</td>
                        <td>{{ $order->total_return_rupiah }}</td>
                        <td>{{ $order->point_used }}</td>
                        <td>{{ $order->point_earned }}</td>
                        <td><a href="{{ route('employee.order.detail', $order->id) }}" class="btn btn-sm btn-primary">Detail</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
