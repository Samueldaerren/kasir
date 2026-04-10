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
                                                <input type="number" class="form-control quantity-input" id="product_qty_{{ $product->id }}" name="products[{{ $product->id }}]" value="{{ old('products.' . $product->id, 0) }}" min="0" max="{{ $product->stock }}" readonly>
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
                <div class="card-header">Customer</div>
                <div class="card-body">
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

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="back-to-products">Kembali</button>
                        <button type="button" class="btn btn-success" id="to-payment-button">Next</button>
                    </div>
                </div>
            </div>

            <div id="payment-step" style="display: none;">
                <div class="card mb-4">
                    <div class="card-header">Pembayaran</div>
                    <div class="card-body">
                        <div id="member-payment" style="display: none;">
                            <div class="mb-3">
                                <label for="total_pay" class="form-label">Amount Paid</label>
                                <input type="number" class="form-control" id="total_pay_member" value="{{ old('total_pay', 0) }}" min="0" step="1">
                            </div>

                            <div class="mb-3" id="points-section" style="display: none;">
                                <p>Available Points: <span id="available_points">0</span></p>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="use_points" name="use_points" value="1" {{ old('use_points') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="use_points">
                                        Use all points
                                    </label>
                                </div>
                                <div class="mt-2" id="points-input-section" style="display: none;">
                                    <label for="points_used_display" class="form-label">Points to use</label>
                                    <input type="number" class="form-control" id="points_used_display" value="{{ old('points_used', 0) }}" min="0" step="1" disabled>
                                    <input type="hidden" id="points_used" name="points_used" value="{{ old('points_used', 0) }}">
                                </div>
                            </div>
                        </div>

                        <div id="non-member-payment" style="display: none;">
                            <div class="mb-3">
                                <label for="total_pay" class="form-label">Amount Paid</label>
                                <input type="number" class="form-control" id="total_pay_non_member" value="{{ old('total_pay', 0) }}" min="0" step="1">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-success" id="show-receipt-button">Next</button>
                        </div>

                        <div id="receipt-block" style="display: none;">
                            <h5>Struk Pembayaran</h5>
                            <div class="mb-2"><strong>Customer Name:</strong> <span id="receipt_customer_name">-</span></div>
                            <div class="mb-2"><strong>Address:</strong> <span id="receipt_address">Jl. Palsu No. 123, Kota Contoh</span></div>
                            <div class="mb-2"><strong>Total Produk:</strong> <span id="receipt_total_items">0</span></div>
                            <div class="mb-2"><strong>Total Harga:</strong> <span id="receipt_total_price">0</span></div>
                            <div class="mb-2"><strong>Customer Type:</strong> <span id="receipt_customer_type">-</span></div>
                            <div class="mb-2"><strong>Amount Paid:</strong> <span id="receipt_amount_paid">0</span></div>
                            <div class="mb-2"><strong>Change:</strong> <span id="receipt_change">0</span></div>
                            <div class="text-muted">Pastikan jumlah sudah benar sebelum menyelesaikan transaksi.</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" id="back-to-customer">Kembali</button>
                    <button type="submit" class="btn btn-primary" id="submit-button">Bayar dan Selesai</button>
                </div>
                <input type="hidden" name="total_pay" id="total_pay_hidden" value="{{ old('total_pay', 0) }}">
            </div>
        </form>
    </div>

    <script>
        const customerTypeSelect = document.getElementById('customer_type');
        const memberFields = document.getElementById('member-fields');
        const nonMemberPayment = document.getElementById('non-member-payment');
        const memberPayment = document.getElementById('member-payment');
        const pointsSection = document.getElementById('points-section');
        const pointsInputSection = document.getElementById('points-input-section');
        const usePointsCheckbox = document.getElementById('use_points');
        const pointsUsedInput = document.getElementById('points_used');
        const availablePointsSpan = document.getElementById('available_points');
        const productStep = document.getElementById('product-step');
        const customerStep = document.getElementById('customer-step');
        const paymentStep = document.getElementById('payment-step');
        const continueButton = document.getElementById('continue-button');
        const backToProducts = document.getElementById('back-to-products');
        const toPaymentButton = document.getElementById('to-payment-button');
        const backToCustomer = document.getElementById('back-to-customer');
        const submitButton = document.getElementById('submit-button');
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const quantityIncreaseButtons = document.querySelectorAll('.quantity-increase');
        const quantityDecreaseButtons = document.querySelectorAll('.quantity-decrease');
        const totalPayMember = document.getElementById('total_pay_member');
        const totalPayNonMember = document.getElementById('total_pay_non_member');
        const totalPayHidden = document.getElementById('total_pay_hidden');
        const pointsUsedDisplay = document.getElementById('points_used_display');
        const pointsUsedHidden = document.getElementById('points_used');
        const showReceiptButton = document.getElementById('show-receipt-button');
        const receiptTotalItems = document.getElementById('receipt_total_items');
        const receiptTotalPrice = document.getElementById('receipt_total_price');
        const receiptAmountPaid = document.getElementById('receipt_amount_paid');
        const receiptChange = document.getElementById('receipt_change');
        const receiptCustomerType = document.getElementById('receipt_customer_type');
        const receiptCustomerName = document.getElementById('receipt_customer_name');
        const receiptAddress = document.getElementById('receipt_address');
        const receiptBlock = document.getElementById('receipt-block');
        const fakeReceiptAddress = 'Jl. Wikrama No. 123, Kota Bogor';

        const initialCustomerType = '{{ old('customer_type', 'member') }}';
        const initialStep = {{ $errors->any() ? (old('total_pay', 0) > 0 ? 3 : 2) : 1 }};

        let currentCustomerType = initialCustomerType;
        let customerPoints = 0;

        function showStep(step) {
            productStep.style.display = step === 1 ? 'block' : 'none';
            customerStep.style.display = step === 2 ? 'block' : 'none';
            paymentStep.style.display = step === 3 ? 'block' : 'none';
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
                    const productId = input.id.replace('product_qty_', '');
                    const card = input.closest('.card');
                    const priceText = card.querySelector('.card-text').textContent;
                    const price = parseInt(priceText.replace(/[^0-9]/g, ''), 10) || 0;
                    totalItems += quantity;
                    totalPrice += price * quantity;
                }
            });

            return { totalItems, totalPrice };
        }

        function updateCustomerVisibility() {
            const isMember = customerTypeSelect.value === 'member';
            currentCustomerType = customerTypeSelect.value;
            memberFields.style.display = isMember ? 'block' : 'none';
            if (!isMember) {
                document.getElementById('phone_number').value = '';
                document.getElementById('name').value = '';
            }
        }

        function updatePaymentVisibility() {
            const isMember = currentCustomerType === 'member';
            memberPayment.style.display = isMember ? 'block' : 'none';
            nonMemberPayment.style.display = isMember ? 'none' : 'block';
            receiptBlock.style.display = 'none';
            showReceiptButton.style.display = 'inline-block';
            submitButton.disabled = true;

            if (isMember) {
                // For member, check if they can use points (only if they have previous orders)
                fetchCustomerPoints();
            }
        }

        function renderReceipt() {
            const { totalItems, totalPrice } = calculateTotal();
            const amountPaid = currentCustomerType === 'member'
                ? (parseInt(totalPayMember.value, 10) || 0)
                : (parseInt(totalPayNonMember.value, 10) || 0);
            const change = amountPaid - totalPrice;
            const customerName = currentCustomerType === 'member'
                ? document.getElementById('name').value || 'Member'
                : 'Non-member';

            receiptTotalItems.textContent = totalItems;
            receiptTotalPrice.textContent = formatRupiah(totalPrice);
            receiptCustomerType.textContent = currentCustomerType === 'member' ? 'Member' : 'Non-member';
            receiptCustomerName.textContent = customerName;
            receiptAddress.textContent = fakeReceiptAddress;
            receiptAmountPaid.textContent = formatRupiah(amountPaid);
            receiptChange.textContent = formatRupiah(change);
        }

        function syncTotalPay() {
            const amountPaid = currentCustomerType === 'member'
                ? (parseInt(totalPayMember.value, 10) || 0)
                : (parseInt(totalPayNonMember.value, 10) || 0);
            totalPayHidden.value = amountPaid;
        }

        async function fetchCustomerPoints() {
            const phoneNumber = document.getElementById('phone_number').value;
            if (!phoneNumber) {
                pointsSection.style.display = 'none';
                return;
            }

            try {
                const response = await fetch(`/employee/check-customer-points?phone=${encodeURIComponent(phoneNumber)}`);
                const data = await response.json();
                customerPoints = data.points || 0;
                const canUsePoints = data.can_use_points || false;

                if (canUsePoints && customerPoints > 0) {
                    pointsSection.style.display = 'block';
                    availablePointsSpan.textContent = customerPoints;
                    usePointsCheckbox.disabled = false;
                    if (usePointsCheckbox.checked) {
                        pointsUsedDisplay.value = customerPoints;
                        pointsUsedHidden.value = customerPoints;
                        pointsInputSection.style.display = 'block';
                    } else {
                        pointsUsedDisplay.value = 0;
                        pointsUsedHidden.value = 0;
                        pointsInputSection.style.display = 'none';
                    }
                } else {
                    pointsSection.style.display = 'none';
                    usePointsCheckbox.checked = false;
                    pointsUsedDisplay.value = 0;
                    pointsUsedHidden.value = 0;
                }
            } catch (error) {
                console.error('Error fetching customer points:', error);
                pointsSection.style.display = 'none';
            }
        }

        function adjustQuantity(productId, delta) {
            const input = document.getElementById('product_qty_' + productId);
            const max = parseInt(input.getAttribute('max'), 10) || 0;
            let value = parseInt(input.value, 10) || 0;
            value = Math.max(0, Math.min(max, value + delta));
            input.value = value;
        }

        quantityIncreaseButtons.forEach((button) => {
            button.addEventListener('click', () => adjustQuantity(button.dataset.productId, 1));
        });

        quantityDecreaseButtons.forEach((button) => {
            button.addEventListener('click', () => adjustQuantity(button.dataset.productId, -1));
        });

        customerTypeSelect.addEventListener('change', updateCustomerVisibility);

        document.getElementById('phone_number').addEventListener('input', () => {
            if (currentCustomerType === 'member') {
                fetchCustomerPoints();
            }
        });

        usePointsCheckbox.addEventListener('change', () => {
            pointsInputSection.style.display = usePointsCheckbox.checked ? 'block' : 'none';
            if (usePointsCheckbox.checked) {
                pointsUsedDisplay.value = customerPoints;
                pointsUsedHidden.value = customerPoints;
            } else {
                pointsUsedDisplay.value = 0;
                pointsUsedHidden.value = 0;
            }
        });

        totalPayNonMember.addEventListener('input', () => {
            syncTotalPay();
            if (receiptBlock.style.display === 'block') {
                renderReceipt();
            }
        });
        totalPayMember.addEventListener('input', () => {
            syncTotalPay();
            if (receiptBlock.style.display === 'block') {
                renderReceipt();
            }
        });

        continueButton.addEventListener('click', () => {
            if (!hasSelectedProducts()) {
                alert('Silakan pilih minimal satu produk sebelum lanjut.');
                return;
            }
            showStep(2);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        backToProducts.addEventListener('click', () => {
            showStep(1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        showReceiptButton.addEventListener('click', () => {
            syncTotalPay();
            renderReceipt();
            receiptBlock.style.display = 'block';
            showReceiptButton.style.display = 'none';
            submitButton.disabled = false;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        toPaymentButton.addEventListener('click', () => {
            syncTotalPay();
            updatePaymentVisibility();
            showStep(3);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        submitButton.addEventListener('click', () => {
            syncTotalPay();
            renderReceipt();
        });

        backToCustomer.addEventListener('click', () => {
            showStep(2);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Remove duplicate non-member-only receipt update; renderReceipt handles both customer types.

        window.addEventListener('load', () => {
            customerTypeSelect.value = initialCustomerType;
            updateCustomerVisibility();
            syncTotalPay();
            if (initialStep === 3) {
                updatePaymentVisibility();
                receiptBlock.style.display = 'block';
                showReceiptButton.style.display = 'none';
                submitButton.disabled = false;
                renderReceipt();
            }
            showStep(initialStep);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
