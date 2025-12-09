# Cloud SQL Migration Script

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Cloud SQL Migration Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Change to backend directory
Set-Location -Path "$PSScriptRoot\backend"

# Backup current .env
Write-Host "[1/4] Backing up current .env..." -ForegroundColor Yellow
Copy-Item -Path .env -Destination .env.backup -Force

# Use .env.cloud
Write-Host "[2/4] Using .env.cloud configuration..." -ForegroundColor Yellow
Copy-Item -Path .env.cloud -Destination .env -Force

# Drop all tables and re-migrate
Write-Host "[3/4] Dropping all tables (fresh migration)..." -ForegroundColor Yellow
php artisan migrate:fresh --force

# Restore original .env
Write-Host "[4/4] Restoring original .env..." -ForegroundColor Yellow
Copy-Item -Path .env.backup -Destination .env -Force

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Migration completed!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green

# Return to original directory
Set-Location -Path $PSScriptRoot
