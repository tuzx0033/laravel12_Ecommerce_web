<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Xem danh sách đơn hàng của người dùng hiện tại
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('items.product')->paginate(10);

        return response()->json($orders, 200);
    }

    // Xem chi tiết đơn hàng
    public function show($id)
    {
        $user = Auth::user();
        $order = Order::with('items.product')->findOrFail($id);

        // Kiểm tra quyền sở hữu
        if ($order->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Bạn không có quyền xem đơn hàng này'], 403);
        }

        return response()->json($order, 200);
    }

    // (Admin) Xem tất cả đơn hàng
    public function allOrders()
    {
        $orders = Order::with('items.product', 'user')->paginate(10);

        return response()->json($orders, 200);
    }

    // (Admin) Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Cập nhật trạng thái đơn hàng thành công', 'order' => $order], 200);
    }
}
