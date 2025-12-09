<p align="center">
  <a href="https://www.uit.edu.vn/" title="Trường Đại học Công nghệ Thông tin">
    <img src="https://i.imgur.com/WmMnSRt.png" alt="Trường Đại học Công nghệ Thông tin | University of Information Technology">
  </a>
</p>
<h1 align="center"><b>IS207 - PHÁT TRIỂN ỨNG DỤNG WEB</b></h1>

## BẢNG MỤC LỤC
* [Giới thiệu môn học](#giới-thiệu-môn-học)
* [Giới thiệu đồ án môn học](#giới-thiệu-đồ-án-môn-học)
* [Seminar](#seminar)
* [Thành viên nhóm](#thành-viên-nhóm)
* [Cài đặt phần mềm](#cài-đặt-phần-mềm)
* [Khởi chạy dự án](#khởi-chạy-dự-án)
* [Công nghệ sử dụng](#công-nghệ-sử-dụng)


## GIỚI THIỆU MÔN HỌC
* **Tên môn học**: Phát triển ứng dụng web - Web Application Development
* **Mã môn học**: IS207
* **Lớp học**: IS207.Q13
* **Năm học**: HK1 2025-2026
* **Giảng viên hướng dẫn:** ThS. **Tạ Việt Phương**
* **Email:** *phuongtv@uit.edu.vn*

---

## GIỚI THIỆU ĐỒ ÁN MÔN HỌC
* **Đề tài đồ án nhóm:** Website Thương mại điện tử (C2C)

---

## SEMINAR
Seminar nhóm: SEO trang web trong thời đại ngày nay khi có AI Search. Cần làm gì để cải thiện quảng bá, tăng tiếp cận trang web cả trên AI Search và SEO. Trình bày một số cách thực hiện, phương pháp bao gồm kỹ thuật và nội dung.

---

## THÀNH VIÊN NHÓM
| STT |   MSSV   |           Họ và Tên |                                                      Github |                  Email |
|-----|:--------:|--------------------:|------------------------------------------------------------:|-----------------------:|
| 1   | 23520641 | Nguyễn Văn Mạnh Huy |                         [HuynFZ](https://github.com/HuynFZ) | 23520641@gm.uit.edu.vn |
| 2   | 23521434 |         Ngô Tiến Sỹ |               [helios-ryuu](https://github.com/helios-ryuu) | 23521367@gm.uit.edu.vn |
| 3   | 23521030 |  Nguyễn Lê Bảo Ngọc |               [ngochoccode](https://github.com/ngochoccode) | 23521030@gm.uit.edu.vn |
| 4   | 23520698 |  Nguyễn Thành Khang | [Nguyen-Thanh-Khang](https://github.com/Nguyen-Thanh-Khang) | 23520698@gm.uit.edu.vn |
| 5   | 23521434 |        Lê Vĩnh Thái |     [VinhThaideptraia](https://github.com/VinhThaideptraia) | 23521417@gm.uit.edu.vn |
| 6   | 23521434 |      Phạm Nhật Khoa |                     [Khoa0216](https://github.com/Khoa0216) | 23520753@gm.uit.edu.vn |
| 7   | 23521434 |      Nguyễn Văn Nam |               [Sinister-VN](https://github.com/Sinister-VN) | 23520982@gm.uit.edu.vn |

---

## CÀI ĐẶT PHẦN MỀM
- [X] [Docker và Docker Desktop](https://www.docker.com/)
- [X] [Git](https://git-scm.com/)
- [X] [PhpStorm](https://www.jetbrains.com/phpstorm/)
- [X] [PHP](https://www.php.net/)
- [X] [Composer](https://getcomposer.org/)
- [X] [NVM (Node Version Manager)](https://github.com/nvm-sh/nvm)

### Hướng dẫn cài đặt PHP

1. **Tải PHP phiên bản phù hợp**
   - Truy cập [https://windows.php.net/download/](https://windows.php.net/download/)
   - Tải phiên bản **PHP 8.5.0** (Non Thread Safe) - Zip file

2. **Giải nén và di chuyển**
   - Giải nén file zip vừa tải
   - Đổi tên thành `php-8.5.0`
   - Di chuyển thư mục PHP vào ổ C: `C:\php-8.4.14`

3. **Cấu hình biến môi trường**
   - Mở **View advance system setting** → **Environment Variables...**
   - Chọn Path trên khung User variables và nhấn **Edit...**
   - Click **New**
   - Thêm `C:\php-8.5.0`
   - Click **OK** để lưu

4. **Kiểm tra cài đặt PHP**
   ```cmd
   php -v
   ```

### Hướng dẫn cài đặt Composer

1. **Tải Composer**
   - Truy cập [https://getcomposer.org/download/](https://getcomposer.org/download/)
   - Tải **Composer-Setup.exe**

2. **Chạy trình cài đặt**
   - Chạy file **Composer-Setup.exe**
   - Chọn đúng đường dẫn PHP (`C:\php-8.5.0\php.exe`)
   - Hoàn tất cài đặt

3. **Kiểm tra cài đặt Composer**
   ```cmd
   composer -V
   ```

### Hướng dẫn cài đặt NVM và Node.js 22

1. **Tải NVM cho Windows**
   - Truy cập [https://github.com/coreybutler/nvm-windows/releases](https://github.com/coreybutler/nvm-windows/releases)
   - Tải file **nvm-setup.exe** từ phiên bản mới nhất

2. **Cài đặt NVM**
   - Chạy file **nvm-setup.exe**
   - Làm theo hướng dẫn để hoàn tất cài đặt
   - Khởi động lại Terminal/Command Prompt

3. **Kiểm tra cài đặt NVM**
   ```cmd
   nvm version
   ```

4. **Cài đặt Node.js phiên bản 22**
   ```cmd
   nvm install 22
   ```

5. **Sử dụng Node.js phiên bản 22**
   ```cmd
   nvm use 22
   ```

6. **Kiểm tra phiên bản Node.js**
   ```cmd
   node -v
   npm -v
   ```

> **Lưu ý:** Nếu gặp lỗi phân quyền, hãy chạy Terminal/Command Prompt với quyền Administrator.


---

## KHỞI CHẠY DỰ ÁN
> ⚠️ **Phải bật Docker Desktop trước khi chạy lệnh**
>
> 👉 **Hướng dẫn chạy dự án ở môi trường local (development environment)**

### Bước 1: Chuẩn bị file môi trường (.env)

Cả thư mục `backend/` và `frontend/` đều có sẵn file `.env.example`.

**1.1. Tạo file `.env` từ `.env.example`:**

```powershell
# Tại thư mục backend/
Copy-Item -Path ".env.example" -Destination ".env" -Force

# Tại thư mục frontend/
Copy-Item -Path ".env.example" -Destination ".env" -Force
```

**1.2. Cấu hình `APP_KEY` cho backend:**

Tại file `backend/.env`, biến `APP_KEY` phải được nhập key. Có 2 cách:
- Sử dụng key nội bộ của nhóm (liên hệ team để lấy key)
- Hoặc tự generate key mới:

```powershell
# Tại thư mục backend/
php artisan key:generate
```

### Bước 2: Cài đặt dependencies

**2.1. Cài đặt dependencies cho frontend:**

```powershell
# Tại thư mục frontend/
npm install
```

> 📌 **Lưu ý:** Chỉ cần chạy 1 lần duy nhất sau khi clone. Sau này không cần chạy lại trừ khi xóa `node_modules/`.

### Bước 3: Khởi chạy database MySQL bằng Docker

```powershell
# Tại thư mục gốc IS207.Q13/
docker compose up -d
```

> 📌 **Lưu ý:** Chạy mỗi khi muốn bật web để có database MySQL ở local.

### Bước 4: Migrate database và khởi chạy backend

**4.1. Migrate database (chỉ chạy khi cần):**

```powershell
# Tại thư mục backend/
php artisan migrate:refresh --seed
```

> 📌 **Khi nào cần chạy:**
> - Lần đầu tiên clone repo về
> - Khi có thay đổi cấu trúc database

**4.2. Khởi chạy server backend:**

```powershell
# Tại thư mục backend/
php artisan serve
```

> 📌 **Lưu ý:** Chạy mỗi khi muốn bật web.
> 
> 🔗 **Backend API:** http://localhost:8000

### Bước 5: Khởi chạy frontend

```powershell
# Tại thư mục frontend/
npm run dev
```

> 📌 **Lưu ý:** Chạy mỗi khi muốn bật web.
> 
> 🔗 **Frontend:** http://localhost:5173

### Bước 6: Dừng và dọn dẹp

**6.1. Tắt Docker containers:**

```powershell
# Tại thư mục gốc IS207.Q13/
docker compose down
```

**6.2. Tắt container và xóa volume (dữ liệu trong DB):**

```powershell
# Tại thư mục gốc IS207.Q13/
docker compose down -v
```

> ⚠️ **Cảnh báo:** Lệnh trên sẽ xóa toàn bộ dữ liệu trong database.

---

## CÔNG NGHỆ SỬ DỤNG

### Backend

1. **Ngôn ngữ:** [PHP](https://www.php.net/)
2. **Framework:** [Laravel](https://laravel.com/)
3. **Authentication:** [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)

### Frontend

1. **Ngôn ngữ:** [HTML](https://developer.mozilla.org/en-US/docs/Web/HTML), [CSS](https://developer.mozilla.org/en-US/docs/Web/CSS), [JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
2. **Framework và công cụ:**
    * [Vite](https://vitejs.dev/) - Build tool
    * [Vue 3](https://vuejs.org/) - Progressive JavaScript framework

### Database

**Hệ quản trị cơ sở dữ liệu:** [MySQL](https://www.mysql.com/)

### Deployment - Nền tảng triển khai

1. **Frontend:** Ứng dụng web phía người dùng (client) được triển khai trên [Vercel](https://vercel.com/), sử dụng tên miền tùy chỉnh `vietmarket.helios.id.vn`

2. **Backend API:** Dịch vụ Laravel API được triển khai lên [Google Cloud Run](https://cloud.google.com/run) thông qua [Cloud Build trigger](https://docs.cloud.google.com/build/docs) (nhánh `main` trigger build khi push)

3. **Cơ sở dữ liệu:** Sử dụng [Google Cloud SQL](https://cloud.google.com/sql) (managed database) với MySQL 8.4.7 instance để lưu trữ dữ liệu người dùng, sản phẩm, giao dịch, v.v.

### CI/CD & DevOps

1. **Containerization:** [Docker](https://www.docker.com/)
2. **Version Control & CI/CD:** [GitHub Actions](https://docs.github.com/en/actions)
