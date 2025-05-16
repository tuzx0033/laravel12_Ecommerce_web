<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Lấy danh sách yêu thích của người dùng
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Người dùng chưa đăng nhập'], 401);
        }

        try {
            $wishlist = $user->wishlists()->with('product')->get();
            return response()->json(['data' => $wishlist], 200);
        } catch (\Exception $e) {
            \Log::error('Lỗi lấy danh sách yêu thích: ' . $e->getMessage());
            return response()->json(['message' => 'Không thể lấy danh sách yêu thích'], 500);
        }
    }

    // Thêm sản phẩm vào danh sách yêu thích
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Kiểm tra sản phẩm đã có trong danh sách yêu thích chưa
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Sản phẩm đã có trong danh sách yêu thích'], 400);
        }

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Thêm vào danh sách yêu thích thành công', 'data' => $wishlist], 201);
    }

    // Xóa sản phẩm khỏi danh sách yêu thích
    public function destroy($id)
    {
        $wishlist = Wishlist::findOrFail($id);

        // Kiểm tra quyền sở hữu
        if ($wishlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền xóa mục này'], 403);
        }

        $wishlist->delete();
        return response()->json(['message' => 'Xóa sản phẩm khỏi danh sách yêu thích thành công'], 200);
    }
    public function check(Request $request)
    {
        $request->validate(['product_ids' => 'required|array']);
        $user = auth()->user();
        $favorites = $user->wishlists()->whereIn('product_id', $request->product_ids)->pluck('product_id')->toArray();
        return response()->json(array_fill_keys($request->product_ids, in_array($request->product_ids, $favorites)));
    }
}
