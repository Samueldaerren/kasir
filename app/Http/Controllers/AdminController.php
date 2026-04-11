<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdminOrdersExport;
use App\Models\User;
use App\Models\Produk;
use App\Models\Order;
use App\Models\Customer;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalRevenue = Order::sum('total_price');
        $dailyRevenue = Order::whereDate('sale_date', Carbon::today())->sum('total_price');
        $monthlyRevenue = Order::whereYear('sale_date', Carbon::now()->year)
            ->whereMonth('sale_date', Carbon::now()->month)
            ->sum('total_price');
        $yearlyRevenue = Order::whereYear('sale_date', Carbon::now()->year)->sum('total_price');

        $memberCount = Customer::where('phone_number', '!=', '0000000000')->count();
        $nonMemberCount = Customer::where('phone_number', '0000000000')->count();

        $lowStockProducts = Produk::where('stock', '<=', 5)->orderBy('stock')->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'dailyRevenue',
            'monthlyRevenue',
            'yearlyRevenue',
            'memberCount',
            'nonMemberCount',
            'lowStockProducts'
        ));
    }

    // User management
    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,employee',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal :min karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus admin atau employee.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,employee',
            'password' => 'nullable|min:6',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus admin atau employee.',
            'password.min' => 'Password minimal :min karakter jika ingin diganti.',
        ]);

        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        // Mencegah admin menghapus akun mereka sendiri
        if (auth()->id() == $id) {
            return redirect()->route('admin.users')->with('error', 'Anda tidak dapat menghapus akun admin Anda sendiri.');
        }

        User::findOrFail($id)->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    // Product management
    public function products()
    {
        $products = Produk::all();
        return view('admin.products', compact('products'));
    }

    public function createProduct()
    {
        return view('admin.create-product');
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|integer|min:500|max:500000',
            'stock' => 'required|integer|min:0|max:100',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka bulat.',
            'price.min' => 'Harga minimal Rp :min.',
            'price.max' => 'Harga tidak boleh lebih dari Rp :max.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka bulat.',
            'stock.min' => 'Stok minimal :min.',
            'stock.max' => 'Stok maksimal :max.',
            'image.required' => 'Gambar produk wajib diunggah.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG, JPEG, PNG, GIF, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $existingProduct = Produk::where('name', $request->name)->first();

        $data = $request->only(['name', 'price', 'stock']);

        if ($existingProduct) {
            $newStock = $existingProduct->stock + $request->stock;

            if ($newStock > 100) {
                return back()->withErrors(['stock' => 'Stok produk ini sudah mencapai batas 100.'])->withInput();
            }

            $data['stock'] = $newStock;

            if ($request->hasFile('image')) {
                if ($existingProduct->image && Storage::disk('public')->exists($existingProduct->image)) {
                    Storage::disk('public')->delete($existingProduct->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $existingProduct->update($data);

            return redirect()->route('admin.products')->with('success', 'Existing product stock updated successfully.');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Produk::create($data);

        return redirect()->route('admin.products')->with('success', 'Product created successfully.');
    }

    public function editProduct($id)
    {
        $product = Produk::findOrFail($id);
        return view('admin.edit-product', compact('product'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Produk::findOrFail($id);
        $request->validate([
            'name' => ['required', Rule::unique('produks', 'name')->ignore($product->id)],
            'price' => 'required|integer|min:500|max:500000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'name.unique' => 'Nama produk sudah ada.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka bulat.',
            'price.min' => 'Harga minimal Rp :min.',
            'price.max' => 'Harga tidak boleh lebih dari Rp :max.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG, JPEG, PNG, GIF, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data = $request->only(['name', 'price']);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    public function deleteProduct($id)
    {
        Produk::findOrFail($id)->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }

    public function updateStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'stock' => 'required|integer|min:0|max:100',
        ], [
            'stock.required' => 'Stok baru wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka bulat.',
            'stock.min' => 'Stok minimal :min.',
            'stock.max' => 'Stok maksimal :max.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.products')
                ->withErrors($validator, 'stock')
                ->withInput()
                ->with('stock_modal_id', $id);
        }

        $product = Produk::findOrFail($id);
        $product->update(['stock' => $request->stock]);

        return redirect()->route('admin.products')->with('success', 'Stok berhasil diperbarui.');
    }

    // Orders history
    public function orders(Request $request)
    {
        $date = $request->input('date');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = Order::with('customer', 'detailOrders.produk');

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

        return view('admin.orders', compact('orders', 'date', 'fromDate', 'toDate'));
    }

    public function orderDetail($id)
    {
        $order = Order::with('customer', 'detailOrders.produk')->findOrFail($id);

        return view('admin.order-detail', compact('order'));
    }

    public function exportOrders(Request $request)
    {
        return Excel::download(new AdminOrdersExport($request->only(['from_date', 'to_date', 'date'])), 'admin_orders.xlsx');
    }
}