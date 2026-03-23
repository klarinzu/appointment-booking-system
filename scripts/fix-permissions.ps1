param(
    [string]$Service = "student-dashboard"
)

$ErrorActionPreference = "Stop"

Write-Host "Repairing Laravel writable permissions in the $Service container..."

& docker compose exec -T $Service sh /var/www/html/scripts/docker-fix-permissions.sh

if ($LASTEXITCODE -ne 0) {
    throw "Permission repair failed with exit code $LASTEXITCODE."
}

Write-Host "Clearing compiled views as www-data..."

& docker compose exec -T --user www-data $Service php artisan view:clear

if ($LASTEXITCODE -ne 0) {
    throw "View cache clear failed with exit code $LASTEXITCODE."
}

Write-Host "Permissions repaired successfully."
