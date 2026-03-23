param(
    [string]$LauncherName = "LNU-DOCUMATE-WSL-Autostart.vbs"
)

$ErrorActionPreference = "Stop"

$startupDir = [Environment]::GetFolderPath('Startup')
$launcherPath = Join-Path $startupDir $LauncherName

if (Test-Path $launcherPath) {
    Remove-Item $launcherPath -Force
}

Write-Host "Startup launcher removed: $launcherPath"
