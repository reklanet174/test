$ErrorActionPreference = "Stop"
$env:Path = "C:\Program Files\Docker\Docker\resources\bin;" + $env:Path
$root = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $root
docker compose down
Write-Host "bitrix-notes stopped"
