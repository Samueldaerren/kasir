<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdminOrdersExport;
use App\Models\User;
use App\Models\Produk;
use App\Models\Order;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
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
        ]);

        $user->update($request->only(['name', 'email', 'role']));

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
            'price' => 'required|integer|min:0|max:100000',
            'stock' => 'required|integer|min:0|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $existingProduct = Produk::where('name', $request->name)->first();

        $data = $request->only(['name', 'price', 'stock']);

        if ($existingProduct) {
            $newStock = $existingProduct->stock + $request->stock;

            if ($newStock > 100) {
                return back()->withErrors(['stock' => 'Total stock cannot exceed 100 for an existing product.'])->withInput();
            }

            $data['stock'] = $newStock;
            // Keep the existing product image when merging by name

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
            'price' => 'required|integer|min:0|max:100000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
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
        $request->validate([
            'stock' => 'required|integer|min:0|max:100',
        ]);

        $product = Produk::findOrFail($id);
        $product->update(['stock' => $request->stock]);

        return redirect()->route('admin.products')->with('success', 'Stock updated successfully.');
    }

    // Orders history
    public function orders()
    {
        $orders = Order::with('customer', 'detailOrders.produk')->get();
        return view('admin.orders', compact('orders'));
    }

    public function orderDetail($id)
    {
        $order = Order::with('customer', 'detailOrders.produk')->findOrFail($id);
        return view('admin.order-detail', compact('order'));
    }

    public function exportOrders()
    {
        return Excel::download(new AdminOrdersExport, 'admin_orders.xlsx');
    }
}