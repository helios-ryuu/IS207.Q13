# Schema Cơ Sở Dữ Liệu VietMarket

## Tổng quan
- **Hệ thống:** VietMarket E-commerce Platform
- **CSDL:** MySQL 9.4
- **Tổng số bảng:** 28 bảng
- **Ngày cập nhật:** 25/11/2025

---

## 1. Bảng users

| Tên Thuộc Tính | Kiểu Dữ Liệu                                   | Ràng Buộc                    | Mô Tả                   |
| -------------- | ---------------------------------------------- | ---------------------------- | ----------------------- |
| id             | BIGINT UNSIGNED                                | PRIMARY KEY, AUTO_INCREMENT  | Mã định danh người dùng |
| username       | VARCHAR(50)                                    | NOT NULL, UNIQUE             | Tên đăng nhập           |
| email          | VARCHAR(100)                                   | NOT NULL, UNIQUE             | Địa chỉ email           |
| password       | VARCHAR(255)                                   | NOT NULL                     | Mật khẩu đã mã hóa      |
| full_name      | VARCHAR(100)                                   | NOT NULL                     | Họ và tên đầy đủ        |
| phone_number   | VARCHAR(15)                                    | NULL, UNIQUE                 | Số điện thoại           |
| address        | TEXT                                           | NULL                         | Địa chỉ                 |
| gender         | ENUM('male','female','other')                  | NULL                         | Giới tính               |
| status         | ENUM('active','inactive','banned','suspended') | NOT NULL, DEFAULT 'active'   | Trạng thái tài khoản    |
| role           | ENUM('customer','seller','admin')              | NOT NULL, DEFAULT 'customer' | Vai trò người dùng      |
| remember_token | VARCHAR(100)                                   | NULL                         | Token ghi nhớ đăng nhập |
| created_at     | TIMESTAMP                                      | NULL                         | Ngày tạo                |
| updated_at     | TIMESTAMP                                      | NULL                         | Ngày cập nhật           |
| deleted_at     | TIMESTAMP                                      | NULL                         | Ngày xóa (soft delete)  |

---

## 2. Bảng categories

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                   | Mô Tả                  |
| -------------- | --------------- | --------------------------- | ---------------------- |
| id             | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Mã định danh danh mục  |
| name           | VARCHAR(100)    | NOT NULL, UNIQUE            | Tên danh mục           |
| created_at     | TIMESTAMP       | NULL                        | Ngày tạo               |
| updated_at     | TIMESTAMP       | NULL                        | Ngày cập nhật          |
| deleted_at     | TIMESTAMP       | NULL                        | Ngày xóa (soft delete) |

---

## 3. Bảng wallets

| Tên Thuộc Tính | Kiểu Dữ Liệu                        | Ràng Buộc                                       | Mô Tả                  |
| -------------- | ----------------------------------- | ----------------------------------------------- | ---------------------- |
| id             | BIGINT UNSIGNED                     | PRIMARY KEY, AUTO_INCREMENT                     | Mã định danh ví        |
| balance        | DECIMAL(15,2)                       | NOT NULL, DEFAULT 0.00                          | Số dư ví               |
| status         | ENUM('active','inactive','locked')  | NOT NULL, DEFAULT 'active'                      | Trạng thái ví          |
| user_id        | BIGINT UNSIGNED                     | NOT NULL, UNIQUE, FOREIGN KEY → users(id)       | Mã người dùng sở hữu   |
| created_at     | TIMESTAMP                           | NULL                                            | Ngày tạo               |
| updated_at     | TIMESTAMP                           | NULL                                            | Ngày cập nhật          |
| deleted_at     | TIMESTAMP                           | NULL                                            | Ngày xóa (soft delete) |

**Ràng buộc khóa ngoại:**
- `user_id` → `users(id)` ON DELETE CASCADE

---

## 4. Bảng bank_accounts

| Tên Thuộc Tính      | Kiểu Dữ Liệu                  | Ràng Buộc                                 | Mô Tả                      |
| ------------------- | ----------------------------- | ----------------------------------------- | -------------------------- |
| id                  | BIGINT UNSIGNED               | PRIMARY KEY, AUTO_INCREMENT               | Mã định danh TK ngân hàng  |
| account_holder_name | VARCHAR(100)                  | NOT NULL                                  | Tên chủ tài khoản          |
| account_number      | VARCHAR(50)                   | NOT NULL, UNIQUE                          | Số tài khoản               |
| bank_name           | VARCHAR(100)                  | NOT NULL                                  | Tên ngân hàng              |
| branch              | VARCHAR(100)                  | NULL                                      | Chi nhánh                  |
| status              | ENUM('active','inactive')     | NOT NULL, DEFAULT 'active'                | Trạng thái                 |
| user_id             | BIGINT UNSIGNED               | NOT NULL, FOREIGN KEY → users(id)         | Mã người dùng sở hữu       |
| created_at          | TIMESTAMP                     | NULL                                      | Ngày tạo                   |
| updated_at          | TIMESTAMP                     | NULL                                      | Ngày cập nhật              |
| deleted_at          | TIMESTAMP                     | NULL                                      | Ngày xóa (soft delete)     |

**Ràng buộc khóa ngoại:**
- `user_id` → `users(id)` ON DELETE CASCADE

---

## 5. Bảng shipping_addresses

| Tên Thuộc Tính | Kiểu Dữ Liệu                  | Ràng Buộc                                 | Mô Tả                      |
| -------------- | ----------------------------- | ----------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED               | PRIMARY KEY, AUTO_INCREMENT               | Mã định danh địa chỉ       |
| receiver_name  | VARCHAR(100)                  | NOT NULL                                  | Tên người nhận             |
| phone_number   | VARCHAR(15)                   | NOT NULL                                  | Số điện thoại người nhận   |
| street_address | VARCHAR(255)                  | NOT NULL                                  | Địa chỉ đường              |
| ward           | VARCHAR(100)                  | NOT NULL                                  | Phường/Xã                  |
| district       | VARCHAR(100)                  | NOT NULL                                  | Quận/Huyện                 |
| province       | VARCHAR(100)                  | NOT NULL                                  | Tỉnh/Thành phố             |
| is_default     | BOOLEAN                       | NOT NULL, DEFAULT false                   | Địa chỉ mặc định           |
| status         | ENUM('active','inactive')     | NOT NULL, DEFAULT 'active'                | Trạng thái                 |
| user_id        | BIGINT UNSIGNED               | NOT NULL, FOREIGN KEY → users(id)         | Mã người dùng sở hữu       |
| created_at     | TIMESTAMP                     | NULL                                      | Ngày tạo                   |
| updated_at     | TIMESTAMP                     | NULL                                      | Ngày cập nhật              |
| deleted_at     | TIMESTAMP                     | NULL                                      | Ngày xóa (soft delete)     |

**Ràng buộc khóa ngoại:**
- `user_id` → `users(id)` ON DELETE CASCADE

---

## 6. Bảng promotions

| Tên Thuộc Tính   | Kiểu Dữ Liệu                           | Ràng Buộc                          | Mô Tả                      |
| ---------------- | -------------------------------------- | ---------------------------------- | -------------------------- |
| id               | BIGINT UNSIGNED                        | PRIMARY KEY, AUTO_INCREMENT        | Mã định danh khuyến mãi    |
| name             | VARCHAR(200)                           | NOT NULL                           | Tên chương trình khuyến mãi|
| description      | TEXT                                   | NULL                               | Mô tả chi tiết             |
| type             | ENUM('fixed','percentage')             | NOT NULL                           | Loại giảm giá              |
| discount_amount  | DECIMAL(15,2)                          | NOT NULL                           | Giá trị giảm               |
| conditions       | TEXT                                   | NULL                               | Điều kiện áp dụng          |
| start_date       | DATETIME                               | NOT NULL                           | Ngày bắt đầu               |
| end_date         | DATETIME                               | NOT NULL                           | Ngày kết thúc              |
| status           | ENUM('active','inactive','expired')    | NOT NULL, DEFAULT 'active'         | Trạng thái                 |
| max_usage_limit  | INTEGER                                | NULL                               | Giới hạn số lần sử dụng    |
| usage_count      | INTEGER                                | NOT NULL, DEFAULT 0                | Số lần đã sử dụng          |
| created_at       | TIMESTAMP                              | NULL                               | Ngày tạo                   |
| updated_at       | TIMESTAMP                              | NULL                               | Ngày cập nhật              |
| deleted_at       | TIMESTAMP                              | NULL                               | Ngày xóa (soft delete)     |

---

## 7. Bảng products

| Tên Thuộc Tính | Kiểu Dữ Liệu                                | Ràng Buộc                             | Mô Tả                      |
| -------------- | ------------------------------------------- | ------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED                             | PRIMARY KEY, AUTO_INCREMENT           | Mã định danh sản phẩm      |
| name           | VARCHAR(255)                                | NOT NULL                              | Tên sản phẩm               |
| description    | TEXT                                        | NULL                                  | Mô tả sản phẩm             |
| status         | ENUM('active','inactive','out_of_stock')    | NOT NULL, DEFAULT 'active'            | Trạng thái sản phẩm        |
| seller_id      | BIGINT UNSIGNED                             | NOT NULL, FOREIGN KEY → users(id)     | Mã người bán               |
| created_at     | TIMESTAMP                                   | NULL                                  | Ngày tạo                   |
| updated_at     | TIMESTAMP                                   | NULL                                  | Ngày cập nhật              |
| deleted_at     | TIMESTAMP                                   | NULL                                  | Ngày xóa (soft delete)     |

**Ràng buộc khóa ngoại:**
- `seller_id` → `users(id)` ON DELETE CASCADE

---

## 8. Bảng product_variants

| Tên Thuộc Tính | Kiểu Dữ Liệu                                | Ràng Buộc                                | Mô Tả                      |
| -------------- | ------------------------------------------- | ---------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED                             | PRIMARY KEY, AUTO_INCREMENT              | Mã định danh biến thể      |
| color          | VARCHAR(50)                                 | NULL                                     | Màu sắc                    |
| size           | VARCHAR(20)                                 | NULL                                     | Kích thước                 |
| quantity       | INTEGER                                     | NOT NULL, DEFAULT 0                      | Số lượng tồn kho           |
| status         | ENUM('active','inactive','out_of_stock')    | NOT NULL, DEFAULT 'active'               | Trạng thái                 |
| price          | DECIMAL(15,2)                               | NOT NULL                                 | Giá bán                    |
| product_id     | BIGINT UNSIGNED                             | NOT NULL, FOREIGN KEY → products(id)     | Mã sản phẩm                |
| created_at     | TIMESTAMP                                   | NULL                                     | Ngày tạo                   |
| updated_at     | TIMESTAMP                                   | NULL                                     | Ngày cập nhật              |
| deleted_at     | TIMESTAMP                                   | NULL                                     | Ngày xóa (soft delete)     |

**Ràng buộc khóa ngoại:**
- `product_id` → `products(id)` ON DELETE CASCADE

---

## 9. Bảng product_images

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                                      | Mô Tả                      |
| -------------- | --------------- | ---------------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT                    | Mã định danh hình ảnh      |
| image_url      | VARCHAR(500)    | NOT NULL                                       | URL hình ảnh               |
| variant_id     | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → product_variants(id)   | Mã biến thể                |
| created_at     | TIMESTAMP       | NULL                                           | Ngày tạo                   |
| updated_at     | TIMESTAMP       | NULL                                           | Ngày cập nhật              |

**Ràng buộc khóa ngoại:**
- `variant_id` → `product_variants(id)` ON DELETE CASCADE

---

## 10. Bảng services

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                   | Mô Tả                      |
| -------------- | --------------- | --------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Mã định danh dịch vụ       |
| name           | VARCHAR(100)    | NOT NULL                    | Tên dịch vụ                |
| price          | DECIMAL(15,2)   | NOT NULL                    | Giá dịch vụ                |
| description    | TEXT            | NULL                        | Mô tả dịch vụ              |
| created_at     | TIMESTAMP       | NULL                        | Ngày tạo                   |
| updated_at     | TIMESTAMP       | NULL                        | Ngày cập nhật              |
| deleted_at     | TIMESTAMP       | NULL                        | Ngày xóa (soft delete)     |

---

## 11. Bảng product_posts

| Tên Thuộc Tính | Kiểu Dữ Liệu                                   | Ràng Buộc                                 | Mô Tả                      |
| -------------- | ---------------------------------------------- | ----------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED                                | PRIMARY KEY, AUTO_INCREMENT               | Mã định danh bài đăng      |
| title          | VARCHAR(255)                                   | NOT NULL                                  | Tiêu đề bài đăng           |
| description    | TEXT                                           | NULL                                      | Mô tả bài đăng             |
| posted_date    | DATETIME                                       | NOT NULL, DEFAULT CURRENT_TIMESTAMP       | Ngày đăng                  |
| status         | ENUM('draft','published','hidden','rejected')  | NOT NULL, DEFAULT 'draft'                 | Trạng thái bài đăng        |
| product_id     | BIGINT UNSIGNED                                | NOT NULL, FOREIGN KEY → products(id)      | Mã sản phẩm                |
| admin_id       | BIGINT UNSIGNED                                | NULL, FOREIGN KEY → users(id)             | Mã admin duyệt             |
| seller_id      | BIGINT UNSIGNED                                | NOT NULL, FOREIGN KEY → users(id)         | Mã người bán               |
| created_at     | TIMESTAMP                                      | NULL                                      | Ngày tạo                   |
| updated_at     | TIMESTAMP                                      | NULL                                      | Ngày cập nhật              |
| deleted_at     | TIMESTAMP                                      | NULL                                      | Ngày xóa (soft delete)     |

**Ràng buộc khóa ngoại:**
- `product_id` → `products(id)` ON DELETE CASCADE
- `admin_id` → `users(id)` ON DELETE SET NULL
- `seller_id` → `users(id)` ON DELETE CASCADE

---

## 12. Bảng orders

| Tên Thuộc Tính  | Kiểu Dữ Liệu                                                                      | Ràng Buộc                                         | Mô Tả                      |
| --------------- | --------------------------------------------------------------------------------- | ------------------------------------------------- | -------------------------- |
| id              | BIGINT UNSIGNED                                                                   | PRIMARY KEY, AUTO_INCREMENT                       | Mã định danh đơn hàng      |
| order_date      | DATETIME                                                                          | NOT NULL, DEFAULT CURRENT_TIMESTAMP               | Ngày đặt hàng              |
| delivery_date   | DATETIME                                                                          | NULL                                              | Ngày giao hàng dự kiến     |
| shipping_fee    | DECIMAL(15,2)                                                                     | NOT NULL, DEFAULT 0.00                            | Phí vận chuyển             |
| status          | ENUM('pending','confirmed','processing','shipped','delivered','cancelled','refunded') | NOT NULL, DEFAULT 'pending'                   | Trạng thái đơn hàng        |
| notes           | TEXT                                                                              | NULL                                              | Ghi chú                    |
| payment_method  | ENUM('cash','bank_transfer','wallet','credit_card')                               | NOT NULL                                          | Phương thức thanh toán     |
| tracking_code   | VARCHAR(100)                                                                      | NULL, UNIQUE                                      | Mã vận đơn                 |
| user_id         | BIGINT UNSIGNED                                                                   | NOT NULL, FOREIGN KEY → users(id)                 | Mã khách hàng              |
| address_id      | BIGINT UNSIGNED                                                                   | NOT NULL, FOREIGN KEY → shipping_addresses(id)    | Mã địa chỉ giao hàng       |
| created_at      | TIMESTAMP                                                                         | NULL                                              | Ngày tạo                   |
| updated_at      | TIMESTAMP                                                                         | NULL                                              | Ngày cập nhật              |

**Ràng buộc khóa ngoại:**
- `user_id` → `users(id)` ON DELETE RESTRICT
- `address_id` → `shipping_addresses(id)` ON DELETE RESTRICT

---

## 13. Bảng transactions

| Tên Thuộc Tính   | Kiểu Dữ Liệu                                        | Ràng Buộc                             | Mô Tả                      |
| ---------------- | --------------------------------------------------- | ------------------------------------- | -------------------------- |
| id               | BIGINT UNSIGNED                                     | PRIMARY KEY, AUTO_INCREMENT           | Mã định danh giao dịch     |
| order_id         | BIGINT UNSIGNED                                     | NOT NULL, FOREIGN KEY → orders(id)    | Mã đơn hàng                |
| amount           | DECIMAL(15,2)                                       | NOT NULL                              | Số tiền giao dịch          |
| payment_method   | ENUM('cash','bank_transfer','wallet','credit_card') | NOT NULL                              | Phương thức thanh toán     |
| status           | ENUM('pending','completed','failed','refunded')     | NOT NULL, DEFAULT 'pending'           | Trạng thái giao dịch       |
| transaction_code | VARCHAR(100)                                        | NULL, UNIQUE                          | Mã giao dịch               |
| payment_gateway  | VARCHAR(50)                                         | NULL                                  | Cổng thanh toán            |
| transaction_date | DATETIME                                            | NOT NULL, DEFAULT CURRENT_TIMESTAMP   | Ngày giao dịch             |
| response_data    | TEXT                                                | NULL                                  | Dữ liệu phản hồi từ gateway|
| created_at       | TIMESTAMP                                           | NULL                                  | Ngày tạo                   |
| updated_at       | TIMESTAMP                                           | NULL                                  | Ngày cập nhật              |

**Ràng buộc khóa ngoại:**
- `order_id` → `orders(id)` ON DELETE RESTRICT

---

## 14. Bảng refunds

| Tên Thuộc Tính | Kiểu Dữ Liệu                                     | Ràng Buộc                             | Mô Tả                      |
| -------------- | ------------------------------------------------ | ------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED                                  | PRIMARY KEY, AUTO_INCREMENT           | Mã định danh yêu cầu hoàn  |
| refund_amount  | DECIMAL(15,2)                                    | NOT NULL                              | Số tiền hoàn               |
| reason         | TEXT                                             | NULL                                  | Lý do hoàn tiền            |
| status         | ENUM('pending','approved','rejected','completed')| NOT NULL, DEFAULT 'pending'           | Trạng thái                 |
| request_date   | DATETIME                                         | NOT NULL, DEFAULT CURRENT_TIMESTAMP   | Ngày yêu cầu               |
| approval_date  | DATETIME                                         | NULL                                  | Ngày duyệt                 |
| notes          | TEXT                                             | NULL                                  | Ghi chú                    |
| order_id       | BIGINT UNSIGNED                                  | NOT NULL, FOREIGN KEY → orders(id)    | Mã đơn hàng                |
| admin_id       | BIGINT UNSIGNED                                  | NULL, FOREIGN KEY → users(id)         | Mã admin duyệt             |
| created_at     | TIMESTAMP                                        | NULL                                  | Ngày tạo                   |
| updated_at     | TIMESTAMP                                        | NULL                                  | Ngày cập nhật              |

**Ràng buộc khóa ngoại:**
- `order_id` → `orders(id)` ON DELETE RESTRICT
- `admin_id` → `users(id)` ON DELETE SET NULL

---

## 15. Bảng refund_details

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                                      | Mô Tả                      |
| -------------- | --------------- | ---------------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT                    | Mã định danh chi tiết hoàn |
| refund_id      | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → refunds(id)            | Mã yêu cầu hoàn tiền       |
| variant_id     | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → product_variants(id)   | Mã biến thể                |
| quantity       | INTEGER         | NOT NULL                                       | Số lượng hoàn              |
| refund_amount  | DECIMAL(15,2)   | NOT NULL                                       | Số tiền hoàn cho SP này    |
| created_at     | TIMESTAMP       | NULL                                           | Ngày tạo                   |
| updated_at     | TIMESTAMP       | NULL                                           | Ngày cập nhật              |

**Ràng buộc khóa ngoại:**
- `refund_id` → `refunds(id)` ON DELETE CASCADE
- `variant_id` → `product_variants(id)` ON DELETE RESTRICT

---

## 16. Bảng applied_promotions

**Bảng trung gian (Many-to-Many) giữa promotions và orders**

| Tên Thuộc Tính   | Kiểu Dữ Liệu    | Ràng Buộc                              | Mô Tả                      |
| ---------------- | --------------- | -------------------------------------- | -------------------------- |
| promotion_id     | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → promotions(id) | Mã khuyến mãi           |
| order_id         | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → orders(id)  | Mã đơn hàng                |
| discounted_amount| DECIMAL(15,2)   | NOT NULL                               | Số tiền được giảm          |
| created_at       | TIMESTAMP       | NULL                                   | Ngày áp dụng               |

**Khóa chính kép:** (`promotion_id`, `order_id`)

**Ràng buộc khóa ngoại:**
- `promotion_id` → `promotions(id)` ON DELETE CASCADE
- `order_id` → `orders(id)` ON DELETE CASCADE

---

## 17. Bảng order_details

**Bảng trung gian (Many-to-Many) giữa orders và product_variants**

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                                      | Mô Tả                      |
| -------------- | --------------- | ---------------------------------------------- | -------------------------- |
| order_id       | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → orders(id)          | Mã đơn hàng                |
| variant_id     | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → product_variants(id)| Mã biến thể                |
| quantity       | INTEGER         | NOT NULL                                       | Số lượng                   |
| unit_price     | DECIMAL(15,2)   | NOT NULL                                       | Đơn giá tại thời điểm mua  |
| created_at     | TIMESTAMP       | NULL                                           | Ngày tạo                   |

**Khóa chính kép:** (`order_id`, `variant_id`)

**Ràng buộc khóa ngoại:**
- `order_id` → `orders(id)` ON DELETE CASCADE
- `variant_id` → `product_variants(id)` ON DELETE RESTRICT

---

## 18. Bảng cart_items

**Bảng trung gian (Many-to-Many) giữa users và product_variants**

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                                      | Mô Tả                      |
| -------------- | --------------- | ---------------------------------------------- | -------------------------- |
| user_id        | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → users(id)           | Mã người dùng              |
| variant_id     | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → product_variants(id)| Mã biến thể                |
| quantity       | INTEGER         | NOT NULL                                       | Số lượng trong giỏ         |
| created_at     | TIMESTAMP       | NULL                                           | Ngày thêm vào giỏ          |
| updated_at     | TIMESTAMP       | NULL                                           | Ngày cập nhật              |

**Khóa chính kép:** (`user_id`, `variant_id`)

**Ràng buộc khóa ngoại:**
- `user_id` → `users(id)` ON DELETE CASCADE
- `variant_id` → `product_variants(id)` ON DELETE CASCADE

---

## 19. Bảng reviews

**Bảng trung gian (Many-to-Many) giữa products và users**

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                             | Mô Tả                      |
| -------------- | --------------- | ------------------------------------- | -------------------------- |
| product_id     | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → products(id)| Mã sản phẩm               |
| user_id        | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → users(id)  | Mã người dùng              |
| rating         | INTEGER         | NOT NULL                              | Điểm đánh giá (1-5)        |
| content        | TEXT            | NULL                                  | Nội dung đánh giá          |
| review_date    | DATETIME        | NOT NULL, DEFAULT CURRENT_TIMESTAMP   | Ngày đánh giá              |
| created_at     | TIMESTAMP       | NULL                                  | Ngày tạo                   |
| updated_at     | TIMESTAMP       | NULL                                  | Ngày cập nhật              |

**Khóa chính kép:** (`product_id`, `user_id`)

**Ràng buộc khóa ngoại:**
- `product_id` → `products(id)` ON DELETE CASCADE
- `user_id` → `users(id)` ON DELETE CASCADE

---

## 20. Bảng messages

| Tên Thuộc Tính | Kiểu Dữ Liệu                   | Ràng Buộc                             | Mô Tả                      |
| -------------- | ------------------------------ | ------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED                | PRIMARY KEY, AUTO_INCREMENT           | Mã định danh tin nhắn      |
| receiver_id    | BIGINT UNSIGNED                | NOT NULL, FOREIGN KEY → users(id)     | Mã người nhận              |
| sender_id      | BIGINT UNSIGNED                | NOT NULL, FOREIGN KEY → users(id)     | Mã người gửi               |
| sent_date      | DATETIME                       | NOT NULL, DEFAULT CURRENT_TIMESTAMP   | Ngày gửi                   |
| content        | TEXT                           | NOT NULL                              | Nội dung tin nhắn          |
| status         | ENUM('sent','read','deleted')  | NOT NULL, DEFAULT 'sent'              | Trạng thái tin nhắn        |
| created_at     | TIMESTAMP                      | NULL                                  | Ngày tạo                   |
| updated_at     | TIMESTAMP                      | NULL                                  | Ngày cập nhật              |

**Ràng buộc khóa ngoại:**
- `receiver_id` → `users(id)` ON DELETE CASCADE
- `sender_id` → `users(id)` ON DELETE CASCADE

---

## 21. Bảng applied_services

**Bảng trung gian (Many-to-Many) giữa product_posts và services**

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                                   | Mô Tả                      |
| -------------- | --------------- | ------------------------------------------- | -------------------------- |
| post_id        | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → product_posts(id)| Mã bài đăng               |
| service_id     | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → services(id)     | Mã dịch vụ                 |
| created_at     | TIMESTAMP       | NULL                                        | Ngày áp dụng               |
| updated_at     | TIMESTAMP       | NULL                                        | Ngày cập nhật              |

**Khóa chính kép:** (`post_id`, `service_id`)

**Ràng buộc khóa ngoại:**
- `post_id` → `product_posts(id)` ON DELETE CASCADE
- `service_id` → `services(id)` ON DELETE RESTRICT

---

## 22. Bảng service_payments

| Tên Thuộc Tính | Kiểu Dữ Liệu                        | Ràng Buộc                             | Mô Tả                      |
| -------------- | ----------------------------------- | ------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED                     | PRIMARY KEY, AUTO_INCREMENT           | Mã định danh thanh toán DV |
| seller_id      | BIGINT UNSIGNED                     | NOT NULL, FOREIGN KEY → users(id)     | Mã người bán               |
| service_id     | BIGINT UNSIGNED                     | NOT NULL, FOREIGN KEY → services(id)  | Mã dịch vụ                 |
| status         | ENUM('pending','completed','failed')| NOT NULL, DEFAULT 'pending'           | Trạng thái thanh toán      |
| purchase_date  | DATETIME                            | NOT NULL, DEFAULT CURRENT_TIMESTAMP   | Ngày mua                   |
| created_at     | TIMESTAMP                           | NULL                                  | Ngày tạo                   |
| updated_at     | TIMESTAMP                           | NULL                                  | Ngày cập nhật              |

**Ràng buộc khóa ngoại:**
- `seller_id` → `users(id)` ON DELETE CASCADE
- `service_id` → `services(id)` ON DELETE RESTRICT

---

## 23. Bảng product_categories

**Bảng trung gian (Many-to-Many) giữa products và categories**

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                                | Mô Tả                      |
| -------------- | --------------- | ---------------------------------------- | -------------------------- |
| product_id     | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → products(id)  | Mã sản phẩm                |
| category_id    | BIGINT UNSIGNED | PRIMARY KEY, FOREIGN KEY → categories(id)| Mã danh mục                |
| created_at     | TIMESTAMP       | NULL                                     | Ngày gán danh mục          |

**Khóa chính kép:** (`product_id`, `category_id`)

**Ràng buộc khóa ngoại:**
- `product_id` → `products(id)` ON DELETE CASCADE
- `category_id` → `categories(id)` ON DELETE CASCADE

---

## 24. Bảng inventory_histories

| Tên Thuộc Tính | Kiểu Dữ Liệu                                       | Ràng Buộc                                      | Mô Tả                      |
| -------------- | -------------------------------------------------- | ---------------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED                                    | PRIMARY KEY, AUTO_INCREMENT                    | Mã định danh lịch sử       |
| variant_id     | BIGINT UNSIGNED                                    | NOT NULL, FOREIGN KEY → product_variants(id)   | Mã biến thể                |
| change_type    | ENUM('import','export','adjust','return','damaged')| NOT NULL                                       | Loại thay đổi              |
| quantity_change| INTEGER                                            | NOT NULL                                       | Số lượng thay đổi          |
| old_quantity   | INTEGER                                            | NOT NULL                                       | Số lượng cũ                |
| new_quantity   | INTEGER                                            | NOT NULL                                       | Số lượng mới               |
| reason         | VARCHAR(255)                                       | NULL                                           | Lý do thay đổi             |
| created_by     | BIGINT UNSIGNED                                    | NOT NULL, FOREIGN KEY → users(id)              | Người thực hiện            |
| created_at     | TIMESTAMP                                          | NULL                                           | Ngày tạo                   |
| updated_at     | TIMESTAMP                                          | NULL                                           | Ngày cập nhật              |

**Ràng buộc khóa ngoại:**
- `variant_id` → `product_variants(id)` ON DELETE CASCADE
- `created_by` → `users(id)` ON DELETE RESTRICT

---

## 25. Bảng seller_reviews

| Tên Thuộc Tính   | Kiểu Dữ Liệu    | Ràng Buộc                             | Mô Tả                      |
| ---------------- | --------------- | ------------------------------------- | -------------------------- |
| id               | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT           | Mã định danh đánh giá      |
| seller_id        | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → users(id)     | Mã người bán               |
| user_id          | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → users(id)     | Mã người đánh giá          |
| order_id         | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → orders(id)    | Mã đơn hàng liên quan      |
| rating           | INTEGER         | NOT NULL                              | Điểm đánh giá (1-5)        |
| content          | TEXT            | NULL                                  | Nội dung đánh giá          |
| response_time    | INTEGER         | NULL                                  | Đánh giá thời gian phản hồi|
| shipping_quality | INTEGER         | NULL                                  | Đánh giá chất lượng vận chuyển|
| review_date      | DATETIME        | NOT NULL, DEFAULT CURRENT_TIMESTAMP   | Ngày đánh giá              |
| created_at       | TIMESTAMP       | NULL                                  | Ngày tạo                   |
| updated_at       | TIMESTAMP       | NULL                                  | Ngày cập nhật              |

**Ràng buộc khóa ngoại:**
- `seller_id` → `users(id)` ON DELETE CASCADE
- `user_id` → `users(id)` ON DELETE CASCADE
- `order_id` → `orders(id)` ON DELETE CASCADE

---

## 26. Bảng notifications

| Tên Thuộc Tính | Kiểu Dữ Liệu                                     | Ràng Buộc                             | Mô Tả                      |
| -------------- | ------------------------------------------------ | ------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED                                  | PRIMARY KEY, AUTO_INCREMENT           | Mã định danh thông báo     |
| user_id        | BIGINT UNSIGNED                                  | NOT NULL, FOREIGN KEY → users(id)     | Mã người nhận thông báo    |
| type           | ENUM('order','promotion','system','review','message')| NOT NULL                          | Loại thông báo             |
| title          | VARCHAR(255)                                     | NOT NULL                              | Tiêu đề thông báo          |
| content        | TEXT                                             | NOT NULL                              | Nội dung thông báo         |
| is_read        | BOOLEAN                                          | NOT NULL, DEFAULT false               | Đã đọc hay chưa            |
| link           | VARCHAR(500)                                     | NULL                                  | Đường dẫn liên quan        |
| expired_date   | DATETIME                                         | NULL                                  | Ngày hết hạn               |
| created_at     | TIMESTAMP                                        | NULL                                  | Ngày tạo                   |
| updated_at     | TIMESTAMP                                        | NULL                                  | Ngày cập nhật              |

**Ràng buộc khóa ngoại:**
- `user_id` → `users(id)` ON DELETE CASCADE

---

## 27. Bảng sessions

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                             | Mô Tả                      |
| -------------- | --------------- | ------------------------------------- | -------------------------- |
| id             | VARCHAR(255)    | PRIMARY KEY                           | Mã session                 |
| user_id        | BIGINT UNSIGNED | NULL, INDEX                           | Mã người dùng (nếu đăng nhập)|
| ip_address     | VARCHAR(45)     | NULL                                  | Địa chỉ IP                 |
| user_agent     | TEXT            | NULL                                  | User Agent của trình duyệt |
| payload        | LONGTEXT        | NOT NULL                              | Dữ liệu session            |
| last_activity  | INTEGER         | NOT NULL, INDEX                       | Thời gian hoạt động cuối   |

**Lưu ý:** Bảng này được Laravel quản lý tự động cho session-based authentication.

---

## 28. Bảng personal_access_tokens

| Tên Thuộc Tính | Kiểu Dữ Liệu    | Ràng Buộc                             | Mô Tả                      |
| -------------- | --------------- | ------------------------------------- | -------------------------- |
| id             | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT           | Mã định danh token         |
| tokenable_type | VARCHAR(255)    | NOT NULL                              | Loại model (User)          |
| tokenable_id   | BIGINT UNSIGNED | NOT NULL, INDEX                       | ID của model               |
| name           | TEXT            | NOT NULL                              | Tên token                  |
| token          | VARCHAR(64)     | NOT NULL, UNIQUE                      | Token (hashed)             |
| abilities      | TEXT            | NULL                                  | Quyền hạn của token        |
| last_used_at   | TIMESTAMP       | NULL                                  | Lần sử dụng cuối           |
| expires_at     | TIMESTAMP       | NULL, INDEX                           | Ngày hết hạn               |
| created_at     | TIMESTAMP       | NULL                                  | Ngày tạo                   |
| updated_at     | TIMESTAMP       | NULL                                  | Ngày cập nhật              |

**Lưu ý:** Bảng này được Laravel Sanctum quản lý cho API token authentication.

---

## 📊 Biểu Đồ Quan Hệ (ERD Summary)

### **Quan hệ chính:**

1. **users (1) → (n) products** - Một người bán có nhiều sản phẩm
2. **products (1) → (n) product_variants** - Một sản phẩm có nhiều biến thể
3. **product_variants (1) → (n) product_images** - Một biến thể có nhiều ảnh
4. **users (1) → (n) orders** - Một user có nhiều đơn hàng
5. **orders (n) ↔ (n) product_variants** qua **order_details**
6. **users (n) ↔ (n) product_variants** qua **cart_items**
7. **products (n) ↔ (n) categories** qua **product_categories**
8. **products (n) ↔ (n) users** qua **reviews**
9. **users (1) → (n) messages** - Tin nhắn giữa users
10. **orders (1) → (n) refunds** - Đơn hàng có thể có nhiều yêu cầu hoàn
11. **promotions (n) ↔ (n) orders** qua **applied_promotions**
12. **product_posts (n) ↔ (n) services** qua **applied_services**

---

## 🔐 Chỉ Mục (Indexes) Quan Trọng

### **Đã có sẵn trong migrations:**
- PRIMARY KEY trên tất cả bảng
- UNIQUE trên: username, email, account_number, tracking_code, transaction_code, token
- FOREIGN KEY indexes tự động
- INDEX trên: user_id (sessions), last_activity (sessions), expires_at (personal_access_tokens)

### **Nên thêm thủ công để tối ưu performance:**

```sql
-- Products search
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_products_seller_status ON products(seller_id, status);

-- Orders filter
CREATE INDEX idx_orders_user_status ON orders(user_id, status, created_at);
CREATE INDEX idx_orders_date ON orders(order_date);

-- Messages
CREATE INDEX idx_messages_sender_receiver ON messages(sender_id, receiver_id, created_at);
CREATE INDEX idx_messages_status ON messages(status);

-- Reviews
CREATE INDEX idx_reviews_product ON reviews(product_id, created_at);
CREATE INDEX idx_seller_reviews_seller ON seller_reviews(seller_id, created_at);

-- Notifications
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);
```

---

## 📝 Ghi Chú

1. **Soft Deletes:** 14 bảng sử dụng soft delete (deleted_at column)
2. **Timestamps:** Tất cả bảng có created_at, updated_at (trừ bảng trung gian đơn giản)
3. **ENUM Values:** Tất cả ENUM được định nghĩa rõ ràng trong migrations
4. **Cascade Rules:**
   - ON DELETE CASCADE: Xóa dữ liệu liên quan
   - ON DELETE RESTRICT: Không cho phép xóa nếu có dữ liệu liên quan
   - ON DELETE SET NULL: Đặt NULL khi xóa
5. **Decimal(15,2):** Dùng cho tất cả trường tiền tệ (hỗ trợ đến 999,999,999,999.99 VND)

---

**Tài liệu được tạo tự động từ Laravel Migrations**  
**Ngày:** 25/11/2025  
**Phiên bản:** 1.0
