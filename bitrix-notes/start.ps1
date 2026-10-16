$ErrorActionPreference = "Stop"
$env:Path = "C:\Program Files\Docker\Docker\resources\bin;" + $env:Path

function Require-Docker {
    $ErrorActionPreference = "Continue"
    docker info 1>$null 2>$null
    $code = $LASTEXITCODE
    $ErrorActionPreference = "Stop"
    if ($code -ne 0) {
        Write-Host "Docker is not running. Start Docker Desktop and wait for Engine running."
        exit 1
    }
}

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$dock = Join-Path $root "..\bitrix-bus-demo\bitrixdock" | Resolve-Path
$www = Join-Path $dock "www"
$modules = Join-Path $www "local\modules"
$dump = Join-Path $root "docker\db\bitrix.sql"

Require-Docker

Write-Host "==> prepare folders"
New-Item -ItemType Directory -Force -Path $modules | Out-Null
New-Item -ItemType Directory -Force -Path (Join-Path $www "local\api") | Out-Null

$setup = Join-Path $www "bitrixsetup.php"
if (-not (Test-Path (Join-Path $www "bitrix\modules\main")) -and -not (Test-Path $setup)) {
    Write-Host "Downloading bitrixsetup.php..."
    Invoke-WebRequest -Uri "https://www.1c-bitrix.ru/download/scripts/bitrixsetup.php" -OutFile $setup
}

Write-Host "==> docker compose up --build"
Set-Location $root
$ErrorActionPreference = "Continue"
docker compose up -d --build
$upCode = $LASTEXITCODE
$ErrorActionPreference = "Stop"
if ($upCode -ne 0) {
    Write-Host "docker compose failed"
    exit 1
}

Write-Host "==> wait db"
$ok = $false
for ($i = 0; $i -lt 60; $i++) {
    $ErrorActionPreference = "Continue"
    $status = (docker inspect -f "{{.State.Health.Status}}" bitrix-notes-db-1 2>$null)
    $ErrorActionPreference = "Stop"
    if ($status -eq "healthy") {
        $ok = $true
        break
    }
    Start-Sleep -Seconds 2
}
if (-not $ok) {
    Write-Host "DB is not ready. Check: docker compose logs db"
    exit 1
}

Write-Host "==> check tables"
$ErrorActionPreference = "Continue"
$tableOut = cmd /c "docker compose exec -T db mysql -ubitrix -p123 -N -e ""SHOW TABLES FROM bitrix"" 2>nul"
$ErrorActionPreference = "Stop"

if ((-not $tableOut) -and (Test-Path -LiteralPath $dump)) {
    Write-Host "==> import docker/db/bitrix.sql"
    $ErrorActionPreference = "Continue"
    cmd /c "docker compose exec -T db mysql -ubitrix -p123 bitrix < `"$dump`" 2>nul"
    $ErrorActionPreference = "Stop"
}

Write-Host ""
Write-Host "Done."
Write-Host "BUS:     http://localhost/"
Write-Host "UI:      http://localhost:8081"
Write-Host "Swagger: http://localhost:8081/docs"
Write-Host "API:     http://localhost/api/notes"
Write-Host "DB: host=db  db=bitrix  user=bitrix  pass=123"
