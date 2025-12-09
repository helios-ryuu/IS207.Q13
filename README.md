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

### Hướng dẫn cài đặt PHP

1. **Tải PHP phiên bản phù hợp**
   - Truy cập [https://windows.php.net/download/](https://windows.php.net/download/)
   - Tải phiên bản **PHP 8.4.14** (Non Thread Safe) - Zip file

2. **Giải nén và di chuyển**
   - Giải nén file zip vừa tải
   - Đổi tên thành `php-8.4.14`
   - Di chuyển thư mục PHP vào ổ C: `C:\php-8.4.14`

3. **Cấu hình biến môi trường**
   - Mở **View advance system setting** → **Environment Variables...**
   - Chọn Path trên khung User variables và nhấn **Edit...**
   - Click **New**
   - Thêm `C:\php-8.4.14`
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
   - Chọn đúng đường dẫn PHP (`C:\php-8.4.14\php.exe`)
   - Hoàn tất cài đặt

3. **Kiểm tra cài đặt Composer**
   ```cmd
   composer -V
   ```

---

## KHỞI CHẠY DỰ ÁN
> ⚠️ **Phải bật Docker Desktop trước khi chạy lệnh**
>
> 👉 **Chỉ dùng cho mục đích kiểm thử (testing environment)**

### Bước 1: Chuẩn bị file môi trường (.env)
Khi clone repo lần đầu, thư mục `backend` **chưa có file `.env`** vì lý do bảo mật.  
Các dev cần tạo file `.env` dựa trên `.env.example`:

```powershell
Copy-Item -Path "./backend/.env.example" -Destination "./backend/.env" -Force
```

### Bước 2: Chạy dự án bằng Docker Compose
- Tại thư mục gốc của dự án:
```powershell
docker compose up -d

# Để chạy trực tiếp image testing từ Docker Hub (Phải xóa các image frontend và backend đang tồn tại)
# docker compose -f docker-compose.test.yml up -d

# Tắt toàn bộ container testing
# docker compose -f docker-compose.test.yml down
```
- Sau khi container khởi chạy thành công, truy cập:
> - 🔗 Frontend: http://localhost:5173
> - 🔗 Backend API (Laravel): http://localhost:8000

### Bước 3: Dừng và dọn dẹp container
- Tắt toàn bộ container:
```powershell
docker compose down
```
- Tắt container và xóa volume (dữ liệu trong DB,...):
```powershell
docker compose down -v
```

> - File .env trong backend chỉ cần tạo một lần duy nhất khi clone repo.
> - Các biến môi trường trong docker-compose.test.yml (như DB_HOST, DB_PORT, ...) sẽ ghi đè lên giá trị trong .env, nên không cần tự sửa thủ công.

---

## CÔNG NGHỆ SỬ DỤNG

### Backend

1. **Ngôn ngữ:** [PHP](https://www.php.net/)
2. **Framework và công cụ:** [Laravel](https://laravel.com/)

    * Tùy chọn thêm: [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum), [Laravel Octane](https://laravel.com/docs/10.x/octane)

### Frontend

1. **Ngôn ngữ:** [HTML](https://developer.mozilla.org/en-US/docs/Web/HTML), [CSS](https://developer.mozilla.org/en-US/docs/Web/CSS), [JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
2. **Framework và công cụ:**

    * [Vite](https://vitejs.dev/)
    * [Vue](https://vuejs.org/)

### Database & Cache

**Hệ quản trị cơ sở dữ liệu:** [MySQL](https://www.mysql.com/)

### Deployment - Nền tảng triển khai

1. **Triển khai Backend:** [Render](https://render.com/)
2.  **Triển khai Frontend:** [Vercel](https://vercel.com/)
3. **Triển khai Database (Dự kiến):** [ScaleGrid](https://scalegrid.io/)

### CI/CD & DevOps

1. **Công cụ chính:** [Docker](https://www.docker.com/)
2. **Công cụ mở rộng (tùy chọn):**
    * [GitHub Actions](https://docs.github.com/en/actions)
    * [Kubernetes](https://kubernetes.io/)
    * [Terraform](https://www.terraform.io/)
    * [Prometheus](https://prometheus.io/)
    * [Grafana](https://grafana.com/)

### Testing

1. **Backend testing:** [PHPUnit](https://phpunit.de/)
2. **Frontend testing:** [Vitest](https://vitest.dev/)
