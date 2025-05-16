Ứng dụng Web Thương mại Điện tử
Tổng quan
Đây là một API RESTful cho nền tảng thương mại điện tử được xây dựng bằng Laravel. Ứng dụng cung cấp các chức năng như xác thực người dùng, quản lý sản phẩm, giỏ hàng, xử lý đơn hàng và danh sách yêu thích. Ứng dụng được thiết kế để hỗ trợ một cửa hàng trực tuyến có khả năng mở rộng, với tương tác người dùng an toàn và xử lý dữ liệu hiệu quả.
Tính năng

Xác thực người dùng: Đăng ký, đăng nhập, đăng xuất và lấy thông tin người dùng với phân quyền theo vai trò (sử dụng Sanctum cho xác thực bằng token).
Quản lý sản phẩm: Tạo, đọc, cập nhật, xóa (CRUD) sản phẩm với hỗ trợ tải ảnh và liên kết danh mục.
Quản lý giỏ hàng: Thêm, cập nhật, xóa sản phẩm trong giỏ hàng và tiến hành thanh toán với kiểm tra tồn kho.
Quản lý đơn hàng: Xem đơn hàng của người dùng, quyền admin xem tất cả đơn hàng và cập nhật trạng thái đơn hàng.
Danh sách yêu thích: Thêm/xóa sản phẩm vào/ra khỏi danh sách yêu thích và kiểm tra trạng thái yêu thích.
Quản lý danh mục: Thực hiện các thao tác CRUD cho danh mục sản phẩm.
Xuất sản phẩm: Xuất dữ liệu sản phẩm sang file Excel.
Phân trang & Lọc: Hỗ trợ phản hồi phân trang, tìm kiếm và sắp xếp sản phẩm.

Công nghệ sử dụng

Backend: Laravel (PHP)
Cơ sở dữ liệu: MySQL (hoặc bất kỳ cơ sở dữ liệu nào được Laravel hỗ trợ)
Xác thực: Laravel Sanctum
Lưu trữ tệp: Lưu trữ cục bộ cho ảnh sản phẩm
Xuất Excel: Maatwebsite Laravel Excel
Ghi log: Hệ thống ghi log tích hợp của Laravel

Yêu cầu hệ thống

PHP >= 8.0
Composer
MySQL >= 5.7 (hoặc cơ sở dữ liệu tương thích)
Node.js (cho các công cụ frontend nếu cần)
Web server (Apache/Nginx)

Hướng dẫn cài đặt
1. Clone repository
git clone <đường-dẫn-repository>
cd <tên-thư-mục>

2. Cài đặt các phụ thuộc
composer install

3. Cấu hình môi trường


Cập nhật các thông tin trong file .env, ví dụ:
Kết nối cơ sở dữ liệu (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
Cấu hình lưu trữ (FILESYSTEM_DISK=public)

5. Chạy migration và seeder 
php artisan migrate
php artisan db:seed 

6. Cấu hình lưu trữ
Tạo liên kết tượng trưng để truy cập ảnh sản phẩm:
php artisan storage:link

7. Khởi động server
php artisan serve

API sẽ chạy tại http://localhost:8000.
Sử dụng API
API được tổ chức theo các controller chính: AuthController, ProductController, CartController, OrderController, WishlistController, CategoryController. Dưới đây là một số endpoint chính:
Xác thực

Đăng ký: POST /api/register (name, email, password, password_confirmation)
Đăng nhập: POST /api/login (email, password)
Đăng xuất: POST /api/logout (yêu cầu token)
Thông tin người dùng: GET /api/user (yêu cầu token)

Sản phẩm

Danh sách sản phẩm: GET /api/products (hỗ trợ search, category_id, sort)
Tạo sản phẩm: POST /api/products (name, description, price, category_id, stock, image)
Chi tiết sản phẩm: GET /api/products/{id}
Cập nhật sản phẩm: PUT /api/products/{id}
Xóa sản phẩm: DELETE /api/products/{id}

Giỏ hàng

Xem giỏ hàng: GET /api/cart (yêu cầu token)
Thêm sản phẩm: POST /api/cart (product_id, quantity)
Cập nhật số lượng: PUT /api/cart/{id} (quantity)
Xóa sản phẩm: DELETE /api/cart/{id}
Thanh toán: POST /api/cart/checkout

Đơn hàng

Danh sách đơn hàng: GET /api/orders (yêu cầu token)
Chi tiết đơn hàng: GET /api/orders/{id}
(Admin) Tất cả đơn hàng: GET /api/orders/all
(Admin) Cập nhật trạng thái: PUT /api/orders/{id}/status (status)

Danh sách yêu thích

Xem danh sách: GET /api/wishlist (yêu cầu token)
Thêm sản phẩm: POST /api/wishlist (product_id)
Xóa sản phẩm: DELETE /api/wishlist/{id}
Kiểm tra yêu thích: POST /api/wishlist/check (product_ids)

Danh mục

Danh sách danh mục: GET /api/categories
Tạo danh mục: POST /api/categories (name)
Chi tiết danh mục: GET /api/categories/{id}
Cập nhật danh mục: PUT /api/categories/{id}
Xóa danh mục: DELETE /api/categories/{id}

Tài liệu API

Sử dụng công cụ như Postman hoặc Swagger để kiểm tra API.
Mỗi yêu cầu cần gửi token trong header Authorization: Bearer <token> (trừ đăng ký/đăng nhập).
Xem chi tiết trong mã nguồn hoặc tạo tài liệu API bằng php artisan l5-swagger:generate (nếu tích hợp Swagger).

Góp ý

Đảm bảo cấu hình chính xác file .env để tránh lỗi kết nối cơ sở dữ liệu.
Kiểm tra quyền truy cập file storage (storage/app/public) để lưu ảnh sản phẩm.
Nếu gặp lỗi, kiểm tra log tại storage/logs/laravel.log.

