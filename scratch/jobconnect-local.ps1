param(
    [switch]$Stop,
    [switch]$NoBrowser,
    [switch]$Test
)

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$runtimeDir = Join-Path $projectRoot 'storage\app\jobconnect-local'
$statePath = Join-Path $runtimeDir 'server.json'

function Test-LocalPort([int]$Port) {
    $client = New-Object System.Net.Sockets.TcpClient
    try {
        $connection = $client.ConnectAsync('127.0.0.1', $Port)
        return ($connection.Wait(500) -and $client.Connected)
    } catch {
        return $false
    } finally {
        $client.Dispose()
    }
}

function Get-SavedServer {
    if (-not (Test-Path -LiteralPath $statePath)) { return $null }
    try {
        $state = Get-Content -LiteralPath $statePath -Raw | ConvertFrom-Json
        $server = Get-Process -Id $state.pid -ErrorAction Stop
        if ($server.Path -eq $state.php -and
            $server.StartTime.ToUniversalTime().Ticks.ToString() -eq $state.started -and
            $state.project -eq $projectRoot) {
            return @{ Process = $server; State = $state }
        }
    } catch { }
    return $null
}

function Invoke-Php([string[]]$Arguments) {
    & $script:php @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw ('PHP gap loi khi chay: ' + ($Arguments -join ' '))
    }
}

function Read-LocalEnv([string]$Name, [string]$Default) {
    $pattern = '(?m)^' + [regex]::Escape($Name) + '=(.*)$'
    $match = [regex]::Match([IO.File]::ReadAllText($script:envPath), $pattern)
    if ($match.Success) { return $match.Groups[1].Value.Trim().Trim('"').Trim("'") }
    return $Default
}

function Test-WebReady([string]$Url) {
    try {
        $response = Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec 3
        return ($response.StatusCode -eq 200 -and $response.Content -match 'JobConnect')
    } catch { return $false }
}

$lockHash = [BitConverter]::ToString(
    [Security.Cryptography.SHA256]::Create().ComputeHash([Text.Encoding]::UTF8.GetBytes($projectRoot))
).Replace('-', '').Substring(0, 16)
$mutex = New-Object Threading.Mutex($false, ('Local\JobConnect_' + $lockHash))
$hasLock = $false
try {
    $hasLock = $mutex.WaitOne(0)
    if (-not $hasLock) { throw 'Mot cua so khac dang khoi dong JobConnect. Hay doi cua so do hoan tat.' }
    Set-Location -LiteralPath $projectRoot

    if ($Stop) {
        $saved = Get-SavedServer
        if ($null -ne $saved) {
            Stop-Process -Id $saved.Process.Id -ErrorAction Stop
            Write-Host 'Da dung web JobConnect.' -ForegroundColor Green
        } else {
            Write-Host 'Web JobConnect hien khong chay bang nut nay.'
        }
        if (Test-Path -LiteralPath $statePath) { Remove-Item -LiteralPath $statePath }
        Write-Host 'Laragon va MySQL van giu nguyen de cac bai lab khac su dung.'
        exit 0
    }

    $laragonRoot = $projectRoot
    while (-not (Test-Path -LiteralPath (Join-Path $laragonRoot 'laragon.exe'))) {
        $parent = Split-Path -Parent $laragonRoot
        if (-not $parent -or $parent -eq $laragonRoot) { throw 'Khong tim thay laragon.exe phia tren thu muc du an.' }
        $laragonRoot = $parent
    }

    $profilePath = Join-Path $laragonRoot 'usr\profile\default.ini'
    $profile = if (Test-Path -LiteralPath $profilePath) { [IO.File]::ReadAllText($profilePath) } else { '' }
    function Find-LaragonProgram([string]$Component, [string]$RelativePath) {
        $pattern = '(?ms)^\[' + [regex]::Escape($Component) + '\]\s*\r?\nVersion=([^\r\n]+)'
        $selected = [regex]::Match($profile, $pattern)
        if ($selected.Success) {
            $candidate = Join-Path (Join-Path $laragonRoot ('bin\' + $Component + '\' + $selected.Groups[1].Value.Trim())) $RelativePath
            if (Test-Path -LiteralPath $candidate) { return $candidate }
        }
        $folders = Get-ChildItem -LiteralPath (Join-Path $laragonRoot ('bin\' + $Component)) -Directory | Sort-Object Name -Descending
        foreach ($folder in $folders) {
            $candidate = Join-Path $folder.FullName $RelativePath
            if (Test-Path -LiteralPath $candidate) { return $candidate }
        }
        throw ('Khong tim thay ' + $Component + ' trong Laragon.')
    }

    $script:php = Find-LaragonProgram 'php' 'php.exe'
    $mysql = Find-LaragonProgram 'mysql' 'bin\mysqld.exe'
    $composer = Join-Path $laragonRoot 'bin\composer\composer.phar'
    $env:PATH = (Split-Path -Parent $script:php) + ';' + (Join-Path $laragonRoot 'bin\git\cmd') + ';' + $env:PATH
    if ($Test) {
        if (-not (Test-Path -LiteralPath 'vendor\autoload.php')) { throw 'Hay bam CHAY_JOBCONNECT.bat mot lan de cai thu vien truoc.' }
        New-Item -ItemType Directory -Path $runtimeDir -Force | Out-Null
        Write-Host 'KIEM THU JOBCONNECT - SQLite tam trong bo nho, khong dung MySQL that.' -ForegroundColor Cyan
        & $script:php (Join-Path $projectRoot 'vendor\bin\phpunit') --configuration (Join-Path $projectRoot 'phpunit.xml') --testdox --colors=always --log-junit (Join-Path $runtimeDir 'test-results.xml')
        exit $LASTEXITCODE
    }
    @('bootstrap\cache', 'storage\framework\cache\data', 'storage\framework\sessions',
        'storage\framework\views', 'storage\logs', 'storage\app\public', 'storage\app\jobconnect-local') |
        ForEach-Object { New-Item -ItemType Directory -Path (Join-Path $projectRoot $_) -Force | Out-Null }

    Write-Host '[1/5] Kiem tra PHP va cau hinh local...'
    Invoke-Php @('-r', 'exit(PHP_VERSION_ID < 80300 ? 1 : 0);')
    $script:envPath = Join-Path $projectRoot '.env'
    if (-not (Test-Path -LiteralPath $script:envPath)) {
        $envText = [IO.File]::ReadAllText((Join-Path $projectRoot '.env.example'))
        $defaults = [ordered]@{
            APP_NAME = 'JobConnect'; APP_URL = 'http://127.0.0.1:8000'; DB_CONNECTION = 'mysql'
            DB_HOST = '127.0.0.1'; DB_PORT = '3306'; DB_DATABASE = 'jobconnect'; DB_USERNAME = 'root'; DB_PASSWORD = ''
            SESSION_DRIVER = 'file'; CACHE_STORE = 'file'; FILESYSTEM_DISK = 'public'; QUEUE_CONNECTION = 'sync'
        }
        foreach ($entry in $defaults.GetEnumerator()) {
            $pattern = '(?m)^#?\s*' + [regex]::Escape($entry.Key) + '=.*$'
            $envText = [regex]::Replace($envText, $pattern, ($entry.Key + '=' + $entry.Value))
        }
        [IO.File]::WriteAllText($script:envPath, $envText, (New-Object Text.UTF8Encoding($false)))
    }
    if ((Read-LocalEnv 'DB_HOST' '127.0.0.1') -notin @('localhost', '127.0.0.1')) {
        throw 'Nut nay danh cho MySQL local. Kiem tra DB_HOST trong .env.'
    }

    Write-Host '[2/5] Kiem tra thu vien Composer...'
    if (-not (Test-Path -LiteralPath 'vendor\autoload.php')) {
        if (-not (Test-Path -LiteralPath $composer)) { throw 'Khong tim thay Composer trong Laragon.' }
        Write-Host 'Lan dau can Internet de tai thu vien. Qua trinh nay co the mat vai phut.'
        Invoke-Php @($composer, 'install', '--prefer-dist', '--no-interaction')
    }
    Invoke-Php @('artisan', 'config:clear', '--no-interaction')
    if (-not (Read-LocalEnv 'APP_KEY' '')) { Invoke-Php @('artisan', 'key:generate', '--no-interaction') }

    Write-Host '[3/5] Mo Laragon va khoi dong MySQL...'
    if (-not (Get-Process -Name laragon -ErrorAction SilentlyContinue)) {
        Start-Process -FilePath (Join-Path $laragonRoot 'laragon.exe') -WorkingDirectory $laragonRoot -WindowStyle Hidden | Out-Null
    }
    $dbPort = [int](Read-LocalEnv 'DB_PORT' '3306')
    if (-not (Test-LocalPort $dbPort)) {
        if (-not (Get-Process -Name mysqld -ErrorAction SilentlyContinue)) {
            $mysqlRoot = Split-Path -Parent (Split-Path -Parent $mysql)
            $mysqlIni = Join-Path $mysqlRoot 'my.ini'
            if (-not (Test-Path -LiteralPath $mysqlIni)) { throw 'Khong tim thay my.ini cua MySQL.' }
            Start-Process -FilePath $mysql -ArgumentList @('--defaults-file="' + $mysqlIni + '"', '--console') `
                -WorkingDirectory $mysqlRoot -WindowStyle Hidden `
                -RedirectStandardOutput (Join-Path $runtimeDir 'mysql-output.log') `
                -RedirectStandardError (Join-Path $runtimeDir 'mysql-error.log') | Out-Null
        }
        $databaseReady = $false
        for ($attempt = 0; $attempt -lt 60; $attempt++) {
            if (Test-LocalPort $dbPort) { $databaseReady = $true; break }
            Start-Sleep -Milliseconds 500
        }
        if (-not $databaseReady) { throw ('MySQL chua len cong ' + $dbPort + '. Xem mysql-error.log trong ' + $runtimeDir) }
    }

    Write-Host '[4/5] Tao database, cap nhat bang va du lieu mau neu la lan dau...'
    Invoke-Php @((Join-Path $PSScriptRoot 'setup_local.php'))
    if (-not (Test-Path -LiteralPath 'public\storage')) { Invoke-Php @('artisan', 'storage:link', '--no-interaction') }

    Write-Host '[5/5] Khoi dong va kiem tra web...'
    $saved = Get-SavedServer
    if ($null -ne $saved) {
        $url = $saved.State.url
    } else {
        $port = 8000
        while (Test-LocalPort $port) {
            $port++
            if ($port -gt 8020) { throw 'Cac cong 8000-8020 deu dang duoc su dung.' }
        }
        $url = 'http://127.0.0.1:' + $port
        $env:APP_URL = $url
        $router = Join-Path $projectRoot 'vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php'
        $server = Start-Process -FilePath $script:php -ArgumentList @('-S', ('127.0.0.1:' + $port), ('"' + $router + '"')) `
            -WorkingDirectory (Join-Path $projectRoot 'public') -WindowStyle Hidden -PassThru `
            -RedirectStandardOutput (Join-Path $runtimeDir 'web-output.log') `
            -RedirectStandardError (Join-Path $runtimeDir 'web-error.log')
        $state = @{ pid = $server.Id; started = $server.StartTime.ToUniversalTime().Ticks.ToString(); php = $script:php; project = $projectRoot; url = $url }
        [IO.File]::WriteAllText($statePath, ($state | ConvertTo-Json), (New-Object Text.UTF8Encoding($false)))
    }
    $webReady = $false
    for ($attempt = 0; $attempt -lt 20; $attempt++) {
        if (Test-WebReady $url) { $webReady = $true; break }
        Start-Sleep -Milliseconds 500
    }
    if (-not $webReady) { throw ('Web chua tra ve trang chu thanh cong. Xem storage\logs\laravel.log va ' + $runtimeDir + '\web-error.log') }
    Write-Host ('DA CHAY: ' + $url) -ForegroundColor Green
    Write-Host 'Tai khoan demo (mat khau: password):'
    Write-Host '  Admin: admin@jobconnect.vn'
    Write-Host '  Nha tuyen dung: fpt@jobconnect.vn'
    Write-Host '  Ung vien: ungvien@jobconnect.vn'
    Write-Host 'Bam DUNG_JOBCONNECT.bat de dung web. Huong dan test: HUONG_DAN_TEST.md'
    if (-not $NoBrowser) { Start-Process $url | Out-Null }
} catch {
    Write-Host ('LOI: ' + $_.Exception.Message) -ForegroundColor Red
    exit 1
} finally {
    if ($hasLock) { $mutex.ReleaseMutex() }
    $mutex.Dispose()
}
