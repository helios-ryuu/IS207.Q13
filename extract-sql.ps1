# ================================================
# extract-sql.ps1
# Tạo file cloudsql_schema.sql từ Laravel migrations (--pretend)
# Dùng để chạy trực tiếp trên GCP Cloud SQL / PhpStorm
# Không phụ thuộc vào kí tự ⇂, xử lý cả prefix Γçé
# ================================================

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Generate Cloud SQL Schema from Migrations" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Đi tới thư mục backend (nơi có artisan)
$backendPath = Join-Path $PSScriptRoot "backend"
if (-not (Test-Path $backendPath)) {
    Write-Host "Không tìm thấy thư mục 'backend' tại: $backendPath" -ForegroundColor Red
    exit 1
}

Set-Location -Path $backendPath
Write-Host "Đang chạy trong thư mục: $backendPath" -ForegroundColor Yellow

# 2. Chạy artisan migrate --pretend --force để xuất SQL ra file log
$pretendLogFile = "migrations_pretend.log"
Write-Host "[1/3] Chạy 'php artisan migrate --pretend --force --no-ansi'..." -ForegroundColor Yellow

php artisan migrate --pretend --force --no-ansi | Out-File -FilePath $pretendLogFile -Encoding utf8

if (-not (Test-Path $pretendLogFile)) {
    Write-Host "Không tạo được file $pretendLogFile. Kiểm tra lại lệnh php artisan." -ForegroundColor Red
    exit 1
}

Write-Host "Đã ghi output vào $pretendLogFile" -ForegroundColor Green

# 3. Đọc file pretend log
Write-Host "[2/3] Đọc và xử lý file pretend log..." -ForegroundColor Yellow
$lines = Get-Content $pretendLogFile

# Lọc những dòng chứa lệnh SQL:
# - create table ...
# - alter table ...
# (bất kể phía trước có Γçé hay không)
$sqlLines = $lines | Where-Object {
    $_ -match '(?i)\bcreate table\b' -or $_ -match '(?i)\balter table\b'
}

if ($sqlLines.Count -eq 0) {
    Write-Host "Không tìm thấy dòng nào chứa 'create table' hoặc 'alter table'." -ForegroundColor Red
    Write-Host "Mở file $pretendLogFile để kiểm tra format log thực tế." -ForegroundColor Yellow
    exit 1
}

# Xử lý từng dòng:
# - Bỏ prefix 'Γçé' (nếu có)
# - Chuẩn hóa collate 'utf8mb4_unicode_ci' -> collate utf8mb4_unicode_ci
# - Thêm dấu ';' nếu chưa có
$statements = $sqlLines | ForEach-Object {
    $clean = $_

    # Bỏ prefix 'Γçé' + khoảng trắng đầu dòng nếu có (bị bể font từ mũi tên)
    $clean = $clean -replace '^\s*Γçé\s*', ''
    $clean = $clean.Trim()

    # Chuẩn hóa COLLATE cho Cloud SQL (bỏ dấu nháy)
    $clean = $clean -replace "(?i)collate\s*'utf8mb4_unicode_ci'", "collate utf8mb4_unicode_ci"

    # Thêm dấu ';' nếu chưa có
    if ($clean -notmatch ';$') {
        $clean += ';'
    }

    $clean
}

# 4. Tạo output hoàn chỉnh cho file .sql
Write-Host "[3/3] Tạo file cloudsql_schema.sql..." -ForegroundColor Yellow

$output = @()
$output += "-- ============================================="
$output += "-- Schema generated from Laravel migrations (--pretend --force)"
$output += "-- Generated at: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
$output += "-- Dùng cho GCP Cloud SQL / MySQL"
$output += "-- ============================================="
$output += ""
$output += "SET FOREIGN_KEY_CHECKS=0;"
$output += ""

foreach ($stmt in $statements) {
    # Nếu là CREATE TABLE, thêm DROP TABLE IF EXISTS phía trước cho an toàn
    if ($stmt -match '(?i)^create table\s+`?([a-zA-Z0-9_]+)`?') {
        $tableName = $matches[1]
        $output += ""
        $output += "-- -----------------------------------------"
        $output += "-- Table: $tableName"

        # Đơn giản: không dùng backtick, để MySQL hiểu tên bảng trực tiếp
        $output += "DROP TABLE IF EXISTS $tableName;"
    }

    $output += $stmt
}

$output += ""
$output += "SET FOREIGN_KEY_CHECKS=1;"
$output += ""

# Ghi ra file .sql
$sqlFile = "cloudsql_schema.sql"
$output | Set-Content -Path $sqlFile -Encoding utf8

Write-Host ""
Write-Host "Đã tạo file SQL: $backendPath\$sqlFile" -ForegroundColor Green
Write-Host "Mở file này trong PhpStorm (kết nối Cloud SQL) và Execute để tạo schema." -ForegroundColor Green
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Hoàn tất." -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# Quay lại thư mục ban đầu
Set-Location -Path $PSScriptRoot
