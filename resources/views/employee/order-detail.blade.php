<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Detail</title>
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
        <h1>Order #{{ $order->id }}</h1>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Customer</div>
                    <div class="card-body">
                        <p><strong>Name:</strong> {{ $order->customer->name }}</p>
                        <p><strong>Phone:</strong> {{ $order->customer->phone_number }}</p>
                        <p><strong>Points Balance:</strong> {{ $order->customer->total_poin }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Payment</div>
                    <div class="card-body">
                        <p><strong>Total Price:</strong> {{ $order->total_price_rupiah }}</p>
                        <p><strong>Points Used:</strong> {{ $order->point_used }}</p>
                        <p><strong>Amount Paid:</strong> {{ $order->total_pay_rupiah }}</p>
                        <p><strong>Total Return:</strong> {{ $order->total_return_rupiah }}</p>
                        <p><strong>Points Earned:</strong> {{ $order->point_earned }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Products</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->detailOrders as $detail)
                            <tr>
                                <td>{{ $detail->produk->name }}</td>
                                <td>{{ $detail->harga_rupiah }}</td>
                                <td>{{ $detail->amount }}</td>
                                <td>{{ $detail->sub_total_rupiah }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('employee.orders') }}" class="btn btn-secondary">Back to Orders</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
