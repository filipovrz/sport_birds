# Автоматична инсталация за Docker (локален тест)
$base = 'http://localhost:8080'
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$page = Invoke-WebRequest -Uri "$base/install" -WebSession $session -UseBasicParsing
if ($page.Content -match 'name="_token"\s+value="([^"]+)"') { $token = $Matches[1] } else { throw 'CSRF token not found' }
$body = @{
    _token = $token
    db_host = 'db'
    db_port = '3306'
    db_name = 'sport_birds'
    db_user = 'sport_birds'
    db_pass = 'secret'
    admin_email = 'admin@test.local'
    admin_name = 'Super Admin'
    admin_password = 'TestPass123!'
    app_url = $base
    app_env = 'local'
    app_debug = '1'
}
Invoke-WebRequest -Uri "$base/install" -Method POST -WebSession $session -Body $body -UseBasicParsing | Out-Null
Write-Host 'Install completed. Login: admin@test.local / TestPass123!'
