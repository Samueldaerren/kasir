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
        return view('employee.dashboard');
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
        ]);

        $productQuantities = array_filter($request->input('products', []), fn ($qty) => $qty > 0);

        if (empty($productQuantities)) {
            return back()->withErrors(['products' => 'Please select at least one product.'])->withInput();
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
            return back()->withErrors(['total_pay' => 'Amount paid must be at least the final total price.'])->withInput();
        }

        $totalReturn = $totalPay - $finalPrice;
        // Member gets 1% points from the final price (after discount)
        $pointEarned = $request->input('customer_type') === 'member' ? intval($finalPrice * 0.01) : 0;

        $customer->total_poin = $request->input('customer_type') === 'member'
            ? $customer->total_poin - $pointsUsed + $pointEarned
            : $pointEarned;

        DB::transaction(function () use ($customer, $detailItems, $totalPrice, $totalPay, $totalReturn, $pointsUsed, $pointEarned) {
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

        return redirect()->route('employee.orders')->with('success', 'Transaction created successfully.');
    }

    public function orders()
    {
        $orders = Order::with('customer')->where('employee_id', Auth::id())->get();
        return view('employee.orders', compact('orders'));
    }

    public function orderDetail($id)
    {
        $order = Order::with('customer', 'detailOrders.produk')->findOrFail($id);

        if ($order->employee_id !== Auth::id()) {
            abort(403);
        }

        return view('employee.order-detail', compact('order'));
    }

    public function exportOrders()
    {
        return Excel::download(new EmployeeOrdersExport, 'employee_orders.xlsx');
    }
}