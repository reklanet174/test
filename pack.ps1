# Build archive for transfer. Run: powershell -File .\pack.ps1
$ErrorActionPreference = "Stop"
$env:Path = "C:\Program Files\Docker\Docker\resources\bin;" + $env:Path

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$out = Join-Path $root "bitrix-notes-docker.zip"
$dumpDir = Join-Path $root "bitrix-notes\docker\db"
$dump = Join-Path $dumpDir "bitrix.sql"

New-Item -ItemType Directory -Force -Path $dumpDir | Out-Null

Write-Host "==> MySQL dump"
$ErrorActionPreference = "Continue"
cmd /c "docker exec bitrix-notes-db-1 mysqldump -ubitrix -p123 --single-transaction --quick bitrix > `"$dump`" 2>nul"
$ErrorActionPreference = "Stop"

if ((Test-Path $dump) -and ((Get-Item $dump).Length -gt 1000)) {
    Write-Host ("dump ok: {0:N1} MB" -f ((Get-Item $dump).Length / 1MB))
} else {
    Write-Host "dump missing or empty - continue with current files"
}

if (Test-Path -LiteralPath $out) {
    Remove-Item -LiteralPath $out -Force
}

Write-Host "==> tar zip (fast)"
Push-Location $root
try {
    $ErrorActionPreference = "Continue"
    tar.exe -a -c -f $out `
        --exclude=bitrix-bus-demo/bitrixdock/logs `
        --exclude=bitrix-bus-demo/bitrixdock/www/bitrix/cache `
        --exclude=bitrix-bus-demo/bitrixdock/www/bitrix/managed_cache `
        --exclude=bitrix-bus-demo/bitrixdock/www/bitrix/stack_cache `
        --exclude=*/node_modules `
        --exclude=*/.git `
        start.cmd `
        start.sh `
        pack.ps1 `
        bitrix-notes `
        bitrix-bus-demo
    $tarCode = $LASTEXITCODE
    $ErrorActionPreference = "Stop"
    if ($tarCode -ne 0 -and -not (Test-Path -LiteralPath $out)) {
        throw "tar failed with code $tarCode"
    }
} finally {
    Pop-Location
}

if (-not (Test-Path -LiteralPath $out)) {
    throw "zip was not created"
}

$size = [math]::Round((Get-Item -LiteralPath $out).Length / 1MB)
Write-Host ""
Write-Host "OK: $out"
Write-Host "Size: $size MB"
Write-Host "Windows: start.cmd"
Write-Host "Linux:   bash start.sh"
