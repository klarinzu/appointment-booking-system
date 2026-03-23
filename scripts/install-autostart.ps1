param(
    [string]$Distro = "Ubuntu",
    [string]$TargetDir = "~/projects/appointment-booking-system"
)

$ErrorActionPreference = "Stop"

$scriptPath = (Resolve-Path (Join-Path $PSScriptRoot "start-wsl-stack-at-login.ps1")).Path
$startupDir = [Environment]::GetFolderPath('Startup')
$launcherPath = Join-Path $startupDir "LNU-DOCUMATE-WSL-Autostart.vbs"
$psCommand = "powershell.exe -NoProfile -WindowStyle Hidden -ExecutionPolicy Bypass -File ""$scriptPath"" -Distro ""$Distro"" -TargetDir ""$TargetDir"""
$launcherContent = @(
    'Set WshShell = CreateObject("WScript.Shell")',
    'WshShell.Run "' + $psCommand.Replace('"', '""') + '", 0, False',
    'Set WshShell = Nothing'
) -join "`r`n"

Set-Content -Path $launcherPath -Value $launcherContent -Encoding ASCII

Write-Host "Startup launcher installed: $launcherPath"
Write-Host "It will run automatically every time you sign in to Windows."
