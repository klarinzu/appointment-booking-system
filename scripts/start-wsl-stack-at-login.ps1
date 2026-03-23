param(
    [string]$Distro = "Ubuntu",
    [string]$TargetDir = "~/projects/appointment-booking-system",
    [int]$DockerTimeoutSeconds = 300
)

$ErrorActionPreference = "Stop"

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$launcher = Join-Path $PSScriptRoot "run-in-wsl.ps1"
$logDir = Join-Path $repoRoot "storage\logs"
$logPath = Join-Path $logDir "windows-wsl-autostart.log"

New-Item -ItemType Directory -Force -Path $logDir | Out-Null

function Write-Log([string]$Message) {
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Add-Content -Path $logPath -Value "[$timestamp] $Message" -Encoding UTF8
}

function Get-DockerDesktopPath {
    $candidates = @(
        (Join-Path $env:ProgramFiles "Docker\Docker\Docker Desktop.exe"),
        (Join-Path $env:LocalAppData "Programs\Docker\Docker\Docker Desktop.exe")
    )

    foreach ($candidate in $candidates) {
        if (Test-Path $candidate) {
            return $candidate
        }
    }

    return $null
}

function Wait-ForDocker([int]$TimeoutSeconds) {
    $deadline = (Get-Date).AddSeconds($TimeoutSeconds)

    while ((Get-Date) -lt $deadline) {
        & docker version --format "{{.Server.Version}}" 1>$null 2>$null

        if ($LASTEXITCODE -eq 0) {
            return $true
        }

        Start-Sleep -Seconds 5
    }

    return $false
}

Write-Log "Autostart task triggered."

try {
    $dockerDesktop = Get-DockerDesktopPath

    if (-not (Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue) -and $dockerDesktop) {
        Write-Log "Starting Docker Desktop."
        Start-Process -FilePath $dockerDesktop | Out-Null
    }

    Write-Log "Waiting for Docker to become ready."

    if (-not (Wait-ForDocker -TimeoutSeconds $DockerTimeoutSeconds)) {
        throw "Docker did not become ready within $DockerTimeoutSeconds seconds."
    }

    Write-Log "Docker is ready. Launching WSL sync/start."

    $launcherOutput = & $launcher -Distro $Distro -TargetDir $TargetDir 2>&1

    foreach ($line in $launcherOutput) {
        Write-Log "$line"
    }

    if ($LASTEXITCODE -ne 0) {
        throw "WSL launcher exited with code $LASTEXITCODE."
    }

    Write-Log "WSL stack startup completed."
} catch {
    Write-Log "Autostart failed: $($_.Exception.Message)"
    throw
}
