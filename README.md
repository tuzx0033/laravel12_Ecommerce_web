# Ứng dụng Web Thương mại Điện tử (API RESTful)

## Tổng quan

Đây là một API RESTful cho nền tảng thương mại điện tử, được xây dựng bằng Laravel (dựa trên ngôn ngữ PHP). Ứng dụng hỗ trợ các chức năng như xác thực người dùng, quản lý sản phẩm, giỏ hàng, đơn hàng, danh sách yêu thích và quản lý danh mục. Dự án được thiết kế để tạo ra một cửa hàng trực tuyến có khả năng mở rộng, đảm bảo tương tác an toàn và xử lý dữ liệu hiệu quả.

## Ngôn ngữ và Công nghệ

* **Ngôn ngữ lập trình:** PHP (>= 8.0)
* **Framework Backend:** Laravel
* **Cơ sở dữ liệu:** MySQL (hoặc PostgreSQL, SQLite tương thích với Laravel)
* **Xác thực:** Laravel Sanctum (dùng token)
* **Lưu trữ tệp:** Laravel Storage (lưu trữ cục bộ cho ảnh sản phẩm)
* **Xuất Excel:** Maatwebsite Laravel Excel
* **Ghi log:** Hệ thống ghi log tích hợp của Laravel
* **Frontend (tùy chọn):** Có thể tích hợp JavaScript (như Vue.js, React) cho giao diện
* **API Client:** Đề xuất dùng Postman hoặc Swagger để kiểm tra API

## Tính năng

* **Xác thực người dùng:** Đăng ký, đăng nhập, đăng xuất, lấy thông tin người dùng với phân quyền theo vai trò.
* **Quản lý sản phẩm:** Tạo, đọc, cập nhật, xóa (CRUD) sản phẩm, hỗ trợ tải ảnh và liên kết danh mục.
* **Quản lý giỏ hàng:** Thêm, cập nhật, xóa sản phẩm trong giỏ, thanh toán với kiểm tra tồn kho.
* **Quản lý đơn hàng:** Xem đơn hàng cá nhân, admin xem tất cả đơn hàng, cập nhật trạng thái.
* **Danh sách yêu thích:** Thêm/xóa sản phẩm, kiểm tra trạng thái yêu thích.
* **Quản lý danh mục:** CRUD danh mục sản phẩm.
* **Xuất dữ liệu:** Xuất danh sách sản phẩm sang file Excel.
* **Phân trang & Lọc:** Hỗ trợ phân trang, tìm kiếm, sắp xếp cho danh sách sản phẩm.

## Yêu cầu hệ thống

* PHP >= 8.0
* Composer
* MySQL >= 5.7 (hoặc cơ sở dữ liệu tương thích)
* Node.js (nếu cần công cụ frontend)
* Web server (Apache/Nginx)

## Hướng dẫn cài đặt

1.  **Clone repository**
    ```bash
    git clone <đường-dẫn-repository>
    cd <tên-thư-mục>
    ```

2.  **Cài đặt phụ thuộc**
    ```bash
    composer install
    ```

3.  **Cấu hình môi trường**

    * Sao chép file `.env.example` thành `.env`:
        ```bash
        cp .env.example .env
        ```
    * Cập nhật thông tin trong file `.env`:
        * Kết nối cơ sở dữ liệu: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
        * Cấu hình lưu trữ: `FILESYSTEM_DISK=public`

4.  **Tạo khóa ứng dụng**
    ```bash
    php artisan key:generate
    ```

5.  **Chạy migration và seeder**
    ```bash
    php artisan migrate
    php artisan db:seed  # Nếu có dữ liệu mẫu
    ```

6.  **Cấu hình lưu trữ**

    * Tạo liên kết tượng trưng cho ảnh sản phẩm:
        ```bash
        php artisan storage:link
        ```

7.  **Khởi động server**
    ```bash
    php artisan serve
    ```

    API sẽ chạy tại: `http://localhost:8000`

## Sử dụng API

API được tổ chức qua các controller: `AuthController`, `ProductController`, `CartController`, `OrderController`, `WishlistController`, `CategoryController`. Dưới đây là các endpoint chính:

### Xác thực

* **Đăng ký:** `POST /api/register` (`name`, `email`, `password`, `password_confirmation`)
* **Đăng nhập:** `POST /api/login` (`email`, `password`)
* **Đăng xuất:** `POST /api/logout` (yêu cầu token)
* **Thông tin người dùng:** `GET /api/user` (yêu cầu token)

### Sản phẩm

* **Danh sách sản phẩm:** `GET /api/products` (hỗ trợ `search`, `category_id`, `sort`)
* **Tạo sản phẩm:** `POST /api/products` (`name`, `description`, `price`, `category_id`, `stock`, `image`)
* **Chi tiết sản phẩm:** `GET /api/products/{id}`
* **Cập nhật sản phẩm:** `PUT /api/products/{id}`
* **Xóa sản phẩm:** `DELETE /api/products/{id}`

### Giỏ hàng

* **Xem giỏ hàng:** `GET /api/cart` (yêu cầu token)
* **Thêm sản phẩm:** `POST /api/cart` (`product_id`, `quantity`)
* **Cập nhật số lượng:** `PUT /api/cart/{id}` (`quantity`)
* **Xóa sản phẩm:** `DELETE /api/cart/{id}`
* **Thanh toán:** `POST /api/cart/checkout`

### Đơn hàng

* **Danh sách đơn hàng:** `GET /api/orders` (yêu cầu token)
* **Chi tiết đơn hàng:** `GET /api/orders/{id}`
* **(Admin) Tất cả đơn hàng:** `GET /api/orders/all`
* **(Admin) Cập nhật trạng thái:** `PUT /api/orders/{id}/status` (`status`)

### Danh sách yêu thích

* **Xem danh sách:** `GET /api/wishlist` (yêu cầu token)
* **Thêm sản phẩm:** `POST /api/wishlist` (`product_id`)
* **Xóa sản phẩm:** `DELETE /api/wishlist/{id}`
* **Kiểm tra yêu thích:** `POST /api/wishlist/check` (`product_ids`)

### Danh mục

* **Danh sách danh mục:** `GET /api/categories`
* **Tạo danh mục:** `POST /api/categories` (`name`)
* **Chi tiết danh mục:** `GET /api/categories/{id}`
* **Cập nhật danh mục:** `PUT /api/categories/{id}`
* **Xóa danh mục:** `DELETE /api/categories/{id}`

## Tài liệu API

* Sử dụng **Postman** hoặc **Swagger** để kiểm tra API.
* Các yêu cầu (trừ đăng ký/đăng nhập) cần gửi **token** trong header:
    ```
    Authorization: Bearer <token>
    ```
* Nếu tích hợp Swagger, tạo tài liệu bằng:
    ```bash
    php artisan l5-swagger:generate
    ```

## Lưu ý

* Kiểm tra kỹ cấu hình trong file `.env` để tránh lỗi kết nối cơ sở dữ liệu.
* Đảm bảo web server có quyền truy cập vào thư mục `storage/app/public` để lưu trữ ảnh sản phẩm.
* Xem log lỗi chi tiết tại: `storage/logs/laravel.log` nếu bạn gặp bất kỳ sự cố nào.
