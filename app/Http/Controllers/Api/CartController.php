<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Người dùng chưa đăng nhập'], 401);
        }
        try {
            $carts = $user->carts()->with('product')->get();
            return response()->json(['data' => $carts], 200);
        } catch (\Exception $e) {
            \Log::error('Lỗi lấy giỏ hàng: ' . $e->getMessage());
            return response()->json(['message' => 'Không thể lấy giỏ hàng'], 500);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Kiểm tra tồn kho
        $product = Product::findOrFail($request->product_id);
        if ($product->stock < $request->quantity) {
            return response()->json(['message' => "Sản phẩm {$product->name} không đủ hàng"], 400);
        }

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $cart = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            // Kiểm tra tổng số lượng
            $newQuantity = $cart->quantity + $request->quantity;
            if ($product->stock < $newQuantity) {
                return response()->json(['message' => "Sản phẩm {$product->name} không đủ hàng"], 400);
            }
            $cart->quantity = $newQuantity;
            $cart->save();
        } else {
            $cart = Cart::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Thêm vào giỏ hàng thành công'], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = Cart::findOrFail($id);

        // Kiểm tra quyền sở hữu
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền cập nhật mục này'], 403);
        }

        // Kiểm tra tồn kho
        $product = $cart->product;
        if ($product->stock < $request->quantity) {
            return response()->json(['message' => "Sản phẩm {$product->name} không đủ hàng"], 400);
        }

        $cart->update(['quantity' => $request->quantity]);
        return response()->json(['message' => 'Cập nhật số lượng thành công'], 200);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);

        // Kiểm tra quyền sở hữu
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền xóa mục này'], 403);
        }

        $cart->delete();
        return response()->json(['message' => 'Xóa sản phẩm thành công'], 200);
    }

    public function checkout()
    {
        $user = Auth::user();
        $carts = $user->carts()->with('product')->get();

        if ($carts->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng trống'], 400);
        }

        // Kiểm tra tồn kho trước khi thanh toán
        foreach ($carts as $cart) {
            $product = $cart->product;
            if ($product->stock < $cart->quantity) {
                return response()->json(['message' => "Sản phẩm {$product->name} không đủ hàng"], 400);
            }
        }

        // Tính tổng tiền
        $total = $carts->sum(function ($cart) {
            return $cart->quantity * $cart->product->price;
        });

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        return DB::transaction(function () use ($user, $carts, $total) {
            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'completed',
            ]);

            // Tạo các mục đơn hàng và cập nhật tồn kho
            foreach ($carts as $cart) {
                $product = $cart->product;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cart->quantity,
                    'price' => $product->price,
                ]);

                // Cập nhật tồn kho
                $product->stock -= $cart->quantity;
                $product->save();
            }

            // Xóa giỏ hàng
            $user->carts()->delete();

            // Load thông tin chi tiết đơn hàng
            $order->load('items.product');

            return response()->json([
                'message' => 'Thanh toán thành công',
                'order' => $order,
            ], 200);
        });
    }
}
