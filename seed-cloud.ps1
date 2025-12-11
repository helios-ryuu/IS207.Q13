# Cloud SQL Seed Script

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Cloud SQL Seed Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Change to backend directory
Set-Location -Path "$PSScriptRoot\backend"

# Check if .env.cloud exists
if (-not (Test-Path ".env.cloud")) {
    Write-Host "[ERROR] File .env.cloud khong ton tai!" -ForegroundColor Red
    Set-Location -Path $PSScriptRoot
    exit 1
}

# Backup current .env
Write-Host "[1/4] Backing up current .env..." -ForegroundColor Yellow
Copy-Item -Path .env -Destination .env.backup -Force

# Use .env.cloud
Write-Host "[2/4] Using .env.cloud configuration..." -ForegroundColor Yellow
Copy-Item -Path .env.cloud -Destination .env -Force

# Clear config cache
Write-Host "[3/4] Clearing config cache..." -ForegroundColor Yellow
php artisan config:clear

# Run database seeder
Write-Host "[4/4] Seeding database..." -ForegroundColor Yellow
php artisan db:seed --force

# Restore original .env
Write-Host "[5/5] Restoring original .env..." -ForegroundColor Yellow
Copy-Item -Path .env.backup -Destination .env -Force

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Seeding completed!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green

# Return to original directory
Set-Location -Path $PSScriptRoot
