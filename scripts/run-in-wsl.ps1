param(
    [string]$Distro = "Ubuntu",
    [string]$TargetDir = "~/projects/appointment-booking-system",
    [switch]$SkipInstall,
    [switch]$SkipMigrate,
    [switch]$InstallNode
)

function Convert-ToWslPath([string]$WindowsPath) {
    $resolved = (Resolve-Path $WindowsPath).Path
    $drive = $resolved.Substring(0, 1).ToLowerInvariant()
    $rest = $resolved.Substring(2).Replace('\', '/')

    return "/mnt/$drive$rest"
}

function Format-CmdArgument([string]$Value) {
    if ($Value -notmatch '[\s"]') {
        return $Value
    }

    return '"' + $Value.Replace('"', '""') + '"'
}

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$scriptPath = Join-Path $PSScriptRoot "wsl-sync-and-start.sh"

$sourceWsl = Convert-ToWslPath $repoRoot
$scriptWsl = Convert-ToWslPath $scriptPath

if (-not $sourceWsl) {
    throw "Unable to resolve the project path inside WSL."
}

if (-not $scriptWsl) {
    throw "Unable to resolve the WSL launcher script path."
}

$args = @()

if ($SkipInstall) {
    $args += "--skip-install"
}

if ($SkipMigrate) {
    $args += "--skip-migrate"
}

if ($InstallNode) {
    $args += "--install-node"
}

Write-Host "Running the project from Ubuntu WSL at $TargetDir..."
$commandLine = (@("wsl", "-d", $Distro, "bash", $scriptWsl, $sourceWsl, $TargetDir) + $args | ForEach-Object {
    Format-CmdArgument $_
}) -join " "

$previousErrorActionPreference = $ErrorActionPreference

try {
    $ErrorActionPreference = "Continue"
    & cmd.exe /d /c $commandLine
    $exitCode = $LASTEXITCODE
} finally {
    $ErrorActionPreference = $previousErrorActionPreference
}

if ($exitCode -ne 0) {
    exit $exitCode
}
