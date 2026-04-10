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
            <a href="{{ route('employee.orders.export') }}" class="btn btn-success">Export Excel</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
