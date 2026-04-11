<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Transaction</title>
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
        <h1>New Transaction</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('employee.transactions.store') }}">
            @csrf

            <div id="product-step" class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Products</span>
                    <button type="button" class="btn btn-success btn-sm" id="continue-button">Lanjut</button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($products as $product)
                            <div class="col-md-4">
                                <div class="card h-100">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <span class="text-muted">No image</span>
                                        </div>
                                    @endif
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-text mb-1">Price: {{ $product->harga_rupiah }}</p>
                                        <p class="card-text mb-1">Stock: {{ $product->stock }}</p>

                                        <div class="mt-auto">
                                            <label class="form-label">Quantity</label>
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary quantity-decrease" data-product-id="{{ $product->id }}">-</button>
                                                <input type="number" class="form-control quantity-input" id="product_qty_{{ $product->id }}" data-price="{{ $product->harga }}" data-name="{{ $product->name }}" name="products[{{ $product->id }}]" value="{{ old('products.' . $product->id, 0) }}" min="0" max="{{ $product->stock }}" readonly>
                                                <button type="button" class="btn btn-outline-secondary quantity-increase" data-product-id="{{ $product->id }}">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="customer-step" class="card mb-4" style="display: none;">
                <div class="card-header">Customer & Payment</div>
                <div class="card-body">
                    <div class="row gx-4">
                        <div class="col-lg-7">
                            <div class="mb-3">
                                <label for="customer_type" class="form-label">Customer Type</label>
                                <select class="form-select" id="customer_type" name="customer_type">
                                    <option value="member" {{ old('customer_type', 'member') === 'member' ? 'selected' : '' }}>Member</option>
                                    <option value="non-member" {{ old('customer_type') === 'non-member' ? 'selected' : '' }}>Non-member</option>
                                </select>
                            </div>

                            <div id="member-fields" style="display: none;">
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="081234567890">
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Boy">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="total_pay_input" class="form-label">Amount Paid</label>
                                <input type="number" class="form-control" id="total_pay_input" name="total_pay" value="{{ old('total_pay', 0) }}" min="0" step="1">
                            </div>

                            <div class="mb-3" id="points-section" style="display: none;">
                                <label class="form-label">Poin Tersedia</label>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" id="points_used_display" value="{{ old('points_used', 0) }}" disabled>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="use_points" name="use_points" value="1" {{ old('use_points') ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="use_points">
                                        Gunakan poin
                                    </label>
                                </div>
                                <input type="hidden" id="points_used" name="points_used" value="{{ old('points_used', 0) }}">
                                <div class="form-text text-muted">Jika pembelian ini merupakan pembelian pertama maka poin belum dapat ditukar.</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" id="back-to-products">Kembali</button>
                                <button type="submit" class="btn btn-success" id="submit-button">Pesan</button>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card border-secondary mb-3">
                                <div class="card-header bg-secondary text-white">Detail Pembelian</div>
                                <div class="card-body p-3" id="purchase-summary-body">
                                    <div id="purchase-summary-items"></div>
                                    <p id="purchase-summary-empty" class="text-muted mb-0">Pilih produk dan jumlahnya di langkah sebelumnya untuk melihat detail pembelian.</p>
                                </div>
                                <div class="card-footer bg-light" id="purchase-summary-totals">
                                    <div class="d-flex justify-content-between mb-1"><strong>Total Item:</strong> <span id="summary-total-items">0</span></div>
                                    <div class="d-flex justify-content-between"><strong>Total Harga:</strong> <span id="summary-total-price">Rp 0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const customerTypeSelect = document.getElementById('customer_type');
        const memberFields = document.getElementById('member-fields');
        const pointsSection = document.getElementById('points-section');
        const usePointsCheckbox = document.getElementById('use_points');
        const pointsUsedDisplay = document.getElementById('points_used_display');
        const pointsUsedHidden = document.getElementById('points_used');
        const productStep = document.getElementById('product-step');
        const customerStep = document.getElementById('customer-step');
        const continueButton = document.getElementById('continue-button');
        const backToProducts = document.getElementById('back-to-products');
        const submitButton = document.getElementById('submit-button');
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const quantityIncreaseButtons = document.querySelectorAll('.quantity-increase');
        const quantityDecreaseButtons = document.querySelectorAll('.quantity-decrease');
        const totalPayInput = document.getElementById('total_pay_input');
        const purchaseSummaryBody = document.getElementById('purchase-summary-body');
        const purchaseSummaryItems = document.getElementById('purchase-summary-items');
        const purchaseSummaryEmpty = document.getElementById('purchase-summary-empty');
        const summaryTotalItems = document.getElementById('summary-total-items');
        const summaryTotalPrice = document.getElementById('summary-total-price');

        const initialCustomerType = '{{ old('customer_type', 'member') }}';
        const initialStep = {{ $errors->any() ? 2 : 1 }};

        let currentCustomerType = initialCustomerType;
        let customerPoints = 0;

        function showStep(step) {
            productStep.style.display = step === 1 ? 'block' : 'none';
            customerStep.style.display = step === 2 ? 'block' : 'none';
        }

        function hasSelectedProducts() {
            return Array.from(quantityInputs).some((input) => parseInt(input.value, 10) > 0);
        }

        function formatRupiah(value) {
            const amount = Number(value) || 0;
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            }).format(amount);
        }

        function calculateTotal() {
            let totalItems = 0;
            let totalPrice = 0;

            quantityInputs.forEach((input) => {
                const quantity = parseInt(input.value, 10) || 0;
                if (quantity > 0) {
                    const price = parseInt(input.dataset.price, 10) || 0;
                    totalItems += quantity;
                    totalPrice += price * quantity;
                }
            });

            return { totalItems, totalPrice };
        }

        function renderPurchaseSummary() {
            const { totalItems, totalPrice } = calculateTotal();
            summaryTotalItems.textContent = totalItems;
            summaryTotalPrice.textContent = formatRupiah(totalPrice);

            purchaseSummaryItems.innerHTML = '';

            if (totalItems === 0) {
                purchaseSummaryEmpty.style.display = 'block';
                return;
            }

            purchaseSummaryEmpty.style.display = 'none';

            const selectedProducts = Array.from(quantityInputs)
                .filter((input) => parseInt(input.value, 10) > 0)
                .map((input) => {
                    const quantity = parseInt(input.value, 10) || 0;
                    return {
                        name: input.dataset.name,
                        quantity,
                        price: parseInt(input.dataset.price, 10) || 0,
                        subtotal: quantity * (parseInt(input.dataset.price, 10) || 0),
                    };
                });

            selectedProducts.forEach((item) => {
                const itemRow = document.createElement('div');
                itemRow.className = 'mb-2';
                itemRow.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${item.name}</strong><br>
                            <small class="text-muted">Qty: ${item.quantity} x ${formatRupiah(item.price)}</small>
                        </div>
                        <div class="text-end">
                            <strong>${formatRupiah(item.subtotal)}</strong>
                        </div>
                    </div>
                `;
                purchaseSummaryItems.appendChild(itemRow);
            });
        }

        function updateCustomerVisibility() {
            const isMember = customerTypeSelect.value === 'member';
            currentCustomerType = customerTypeSelect.value;
            memberFields.style.display = isMember ? 'block' : 'none';
            usePointsCheckbox.checked = false;
            pointsUsedHidden.value = 0;
            pointsUsedDisplay.value = 0;

            if (!isMember) {
                pointsSection.style.display = 'none';
                document.getElementById('phone_number').value = '';
                document.getElementById('name').value = '';
            } else {
                pointsSection.style.display = 'block';
                fetchCustomerPoints();
            }
        }


        async function fetchCustomerPoints() {
            const phoneNumber = document.getElementById('phone_number').value;
            let points = 0;
            let canUsePoints = false;

            if (phoneNumber) {
                try {
                    const response = await fetch(`/employee/check-customer-points?phone=${encodeURIComponent(phoneNumber)}`);
                    const data = await response.json();
                    points = data.points || 0;
                    canUsePoints = data.can_use_points || false;
                } catch (error) {
                    console.error('Error fetching customer points:', error);
                    points = 0;
                    canUsePoints = false;
                }
            }

            customerPoints = points;
            pointsSection.style.display = 'block';
            pointsUsedDisplay.value = customerPoints;
            usePointsCheckbox.disabled = customerPoints === 0 || !canUsePoints;
            usePointsCheckbox.checked = false;
            pointsUsedHidden.value = 0;
        }

        function adjustQuantity(productId, delta) {
            const input = document.getElementById('product_qty_' + productId);
            const max = parseInt(input.getAttribute('max'), 10) || 0;
            let value = parseInt(input.value, 10) || 0;
            value = Math.max(0, Math.min(max, value + delta));
            input.value = value;
        }

        quantityIncreaseButtons.forEach((button) => {
            button.addEventListener('click', () => {
                adjustQuantity(button.dataset.productId, 1);
                renderPurchaseSummary();
            });
        });

        quantityDecreaseButtons.forEach((button) => {
            button.addEventListener('click', () => {
                adjustQuantity(button.dataset.productId, -1);
                renderPurchaseSummary();
            });
        });

        customerTypeSelect.addEventListener('change', () => {
            updateCustomerVisibility();
        });

        document.getElementById('phone_number').addEventListener('input', () => {
            if (currentCustomerType === 'member') {
                fetchCustomerPoints();
            }
        });

        usePointsCheckbox.addEventListener('change', () => {
            if (usePointsCheckbox.checked && customerPoints > 0) {
                pointsUsedHidden.value = customerPoints;
            } else {
                pointsUsedHidden.value = 0;
            }
        });

        continueButton.addEventListener('click', () => {
            if (!hasSelectedProducts()) {
                alert('Silakan pilih minimal satu produk sebelum lanjut.');
                return;
            }
            renderPurchaseSummary();
            showStep(2);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        backToProducts.addEventListener('click', () => {
            showStep(1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        const transactionForm = document.querySelector('form');
        transactionForm.addEventListener('submit', (event) => {
            if (!hasSelectedProducts()) {
                alert('Silakan pilih minimal satu produk sebelum melakukan pembayaran.');
                event.preventDefault();
                return;
            }

            const { totalPrice } = calculateTotal();
            const amountPaid = parseInt(totalPayInput.value, 10) || 0;
            if (amountPaid < totalPrice) {
                alert('Amount paid harus minimal total harga produk.');
                event.preventDefault();
            }
        });

        window.addEventListener('load', () => {
            customerTypeSelect.value = initialCustomerType;
            updateCustomerVisibility();
            renderPurchaseSummary();
            showStep(initialStep);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
