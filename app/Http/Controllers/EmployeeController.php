<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeeOrdersExport;
use App\Models\Produk;
use App\Models\Order;
use App\Models\Customer;
use App\Models\DetailOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $lowStockProducts = Produk::where('stock', '<=', 5)->orderBy('stock')->limit(5)->get();

        $totalRevenue = Order::where('employee_id', Auth::id())->sum('total_price');
        $dailyRevenue = Order::where('employee_id', Auth::id())
            ->whereDate('sale_date', Carbon::today())
            ->sum('total_price');

        $monthlyRevenue = Order::where('employee_id', Auth::id())
            ->whereYear('sale_date', Carbon::now()->year)
            ->whereMonth('sale_date', Carbon::now()->month)
            ->sum('total_price');

        $yearlyRevenue = Order::where('employee_id', Auth::id())
            ->whereYear('sale_date', Carbon::now()->year)
            ->sum('total_price');

        $memberCount = Customer::where('phone_number', '!=', '0000000000')->count();
        $nonMemberCount = Customer::where('phone_number', '0000000000')->count();

        return view(
            'employee.dashboard',
            compact(
                'lowStockProducts',
                'totalRevenue',
                'dailyRevenue',
                'monthlyRevenue',
                'yearlyRevenue',
                'memberCount',
                'nonMemberCount'
            )
        );
    }

    public function products()
    {
        $products = Produk::all();
        return view('employee.products', compact('products'));
    }

    public function createTransaction()
    {
        $products = Produk::all();
        return view('employee.transaction', compact('products'));
    }

    public function checkCustomerPoints(Request $request)
    {
        $phoneNumber = $request->query('phone');
        if (!$phoneNumber) {
            return response()->json(['points' => 0, 'can_use_points' => false]);
        }

        $customer = Customer::where('phone_number', $phoneNumber)->first();
        if (!$customer) {
            return response()->json(['points' => 0, 'can_use_points' => false]);
        }

        // Check if customer has previous orders (can use points)
        $hasPreviousOrders = Order::where('customer_id', $customer->id)->exists();

        return response()->json([
            'points' => $customer->total_poin,
            'can_use_points' => $hasPreviousOrders
        ]);
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'customer_type' => 'required|in:member,non-member',
            'phone_number' => 'nullable|string|required_if:customer_type,member',
            'name' => 'nullable|string|required_if:customer_type,member',
            'points_used' => 'nullable|integer|min:0',
            'products' => 'required|array',
            'products.*' => 'integer|min:0',
            'total_pay' => 'required|integer|min:0',
        ], [
            'customer_type.required' => 'Tipe customer wajib dipilih.',
            'customer_type.in' => 'Tipe customer tidak valid.',
            'phone_number.required_if' => 'Nomor telepon wajib diisi untuk member.',
            'name.required_if' => 'Nama customer wajib diisi untuk member.',
            'use_points.boolean' => 'Pilihan gunakan poin tidak valid.',
            'points_used.integer' => 'Poin harus berupa angka bulat.',
            'points_used.min' => 'Poin tidak boleh negatif.',
            'products.required' => 'Pilih minimal satu produk.',
            'products.array' => 'Daftar produk tidak valid.',
            'products.*.integer' => 'Jumlah produk harus berupa angka.',
            'products.*.min' => 'Jumlah produk tidak boleh negatif.',
            'total_pay.required' => 'Total bayar wajib diisi.',
            'total_pay.integer' => 'Total bayar harus berupa angka bulat.',
            'total_pay.min' => 'Total bayar tidak boleh negatif.',
        ]);

        $productQuantities = array_filter($request->input('products', []), fn ($qty) => $qty > 0);

        if (empty($productQuantities)) {
            return back()->withErrors(['products' => 'Silakan pilih minimal satu produk.'])->withInput();
        }

        $products = Produk::whereIn('id', array_keys($productQuantities))->get()->keyBy('id');

        $totalPrice = 0;
        $detailItems = [];

        foreach ($productQuantities as $productId => $quantity) {
            if (!isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];
            if ($quantity > $product->stock) {
                return back()->withErrors(["products.{$productId}" => "The selected quantity for {$product->name} exceeds stock."])->withInput();
            }

            $subTotal = $product->price * $quantity;
            $detailItems[] = [
                'product_id' => $product->id,
                'amount' => $quantity,
                'sub_total' => $subTotal,
            ];

            $totalPrice += $subTotal;
        }

        if (empty($detailItems)) {
            return back()->withErrors(['products' => 'Please select at least one valid product.'])->withInput();
        }

        $pointsUsed = 0;
        if ($request->input('customer_type') === 'member') {
            $customer = Customer::firstOrCreate(
                ['phone_number' => $request->input('phone_number')],
                [
                    'name' => $request->input('name'),
                    'email' => $request->input('phone_number') . '@member.local',
                    'total_poin' => 0,
                ]
            );
            $customer->name = $request->input('name');
            $customer->email = $request->input('phone_number') . '@member.local';

            // Check if customer has previous orders to allow point usage
            $hasPreviousOrders = Order::where('customer_id', $customer->id)->exists();

            if ($hasPreviousOrders && $request->has('use_points') && $request->boolean('use_points')) {
                $pointsUsed = (int) $request->input('points_used', 0);
                if ($pointsUsed > $customer->total_poin) {
                    return back()->withErrors(['points_used' => 'Points used cannot exceed customer points balance.'])->withInput();
                }
            }
        } else {
            $customer = Customer::create([
                'name' => 'Non-member',
                'email' => 'nonmember_' . uniqid() . '@example.com',
                'phone_number' => '0000000000',
                'total_poin' => 0,
            ]);
        }

        $finalPrice = max($totalPrice - $pointsUsed, 0);
        $totalPay = (int) $request->input('total_pay', 0);

        if ($totalPay < $finalPrice) {
            return back()->withErrors(['total_pay' => 'Total bayar kurang dari total harga.'])->withInput();
        }

        $totalReturn = $totalPay - $finalPrice;
        // Member gets 1% points from the final price (after discount)
        $pointEarned = $request->input('customer_type') === 'member' ? intval($finalPrice * 0.01) : 0;

        $customer->total_poin = $request->input('customer_type') === 'member'
            ? $customer->total_poin - $pointsUsed + $pointEarned
            : $pointEarned;

        $order = null;
        DB::transaction(function () use ($customer, $detailItems, $totalPrice, $totalPay, $totalReturn, $pointsUsed, $pointEarned, &$order) {
            $customer->save();

            $order = Order::create([
                'employee_id' => Auth::id(),
                'customer_id' => $customer->id,
                'sale_date' => Carbon::now(),
                'total_price' => $totalPrice,
                'total_pay' => $totalPay,
                'total_return' => $totalReturn,
                'point_earned' => $pointEarned,
                'point_used' => $pointsUsed,
            ]);

            foreach ($detailItems as $item) {
                $order->detailOrders()->create($item);
                Produk::find($item['product_id'])->decrement('stock', $item['amount']);
            }
        });

        return redirect()->route('employee.order.detail', $order->id)->with('success', 'Transaction created successfully.');
    }

    public function orders(Request $request)
    {
        $date = $request->input('date');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = Order::with('customer')->where('employee_id', Auth::id());

        if ($fromDate && $toDate) {
            $query->whereDate('sale_date', '>=', Carbon::parse($fromDate))
                  ->whereDate('sale_date', '<=', Carbon::parse($toDate));
        } elseif ($fromDate) {
            $query->whereDate('sale_date', '>=', Carbon::parse($fromDate));
        } elseif ($toDate) {
            $query->whereDate('sale_date', '<=', Carbon::parse($toDate));
        } elseif ($date) {
            $query->whereDate('sale_date', Carbon::parse($date));
        }

        $orders = $query->get();

        return view('employee.orders', compact('orders', 'date', 'fromDate', 'toDate'));
    }

    public function orderDetail($id)
    {
        $order = Order::with('employee', 'customer', 'detailOrders.produk')->findOrFail($id);

        if ($order->employee_id !== Auth::id()) {
            abort(403);
        }

        return view('employee.order-detail', compact('order'));
    }

    public function exportOrders(Request $request)
    {
        return Excel::download(new EmployeeOrdersExport($request->only(['from_date', 'to_date', 'date'])), 'employee_orders.xlsx');
    }
}