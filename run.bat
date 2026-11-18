@echo off
setlocal

REM REM သည် မှတ်ချက်ရေးရန်သုံးသော Batch command ဖြစ်ပြီး နောက်ကစာသားကို run လုပ်မည်မဟုတ်ပါ။
REM Script ကိုဘယ်နေရာမှခေါ်သည်ဖြစ်စေ project root မှအလုပ်လုပ်ရန် ပြောင်းပါ။
cd /d "%~dp0"

REM Docker ကို install လုပ်ထားပြီး PATH မှအသုံးပြုနိုင်ခြင်းရှိမရှိ စစ်ပါ။
where docker >nul 2>&1
if errorlevel 1 (
    echo Docker is not installed or is not available on PATH.
    pause
    exit /b 1
)

REM Docker Desktop အလုပ်လုပ်နေပြီး Docker engine ကိုဆက်သွယ်နိုင်ခြင်းရှိမရှိ စစ်ပါ။
docker info >nul 2>&1
if errorlevel 1 (
    echo Docker Desktop is not running.
    echo Start Docker Desktop, wait until it reports that Docker is running, then run run.bat again.
    pause
    exit /b 1
)

REM Backend နှင့် frontend အတွက် local environment ဖိုင်မရှိသေးလျှင် ဥပမာဖိုင်မှ ကူးယူဖန်တီးပါ။
if not exist "backend\.env" (
    copy "backend\.env.example" "backend\.env" >nul
    echo Created backend\.env
)

if not exist "frontend\.env" (
    copy "frontend\.env.example" "frontend\.env" >nul
    echo Created frontend\.env
)

REM Laravel APP_KEY ရှိပြီးသားဆိုလျှင် key အသစ်မဖန်တီးဘဲ service စတင်ရန်သွားပါ။
findstr /R /C:"^APP_KEY=.+$" "backend\.env" >nul
if not errorlevel 1 goto start_services

REM APP_KEY မရှိသေးလျှင် Docker ထဲတွင် Laravel key ကိုထုတ်ပါ။ --show ကြောင့် key ကို terminal တွင်မပြပါ။
echo Generating Laravel application key...
set "MINIERP_APP_KEY="
for /f "usebackq delims=" %%L in (`docker compose run --rm --no-deps backend php artisan key:generate --show`) do (
    set "MINIERP_APP_KEY=%%L"
)

if "%MINIERP_APP_KEY%"=="" (
    echo Laravel application key could not be generated.
    pause
    exit /b 1
)

REM ထုတ်ထားသော key ကို backend\.env ၏ APP_KEY တန်ဖိုးတွင် သိမ်းပါ။
powershell -NoProfile -ExecutionPolicy Bypass -Command "$p = Join-Path (Get-Location) 'backend\.env'; $key = $env:MINIERP_APP_KEY; $text = [IO.File]::ReadAllText($p); $text = [regex]::Replace($text, '^APP_KEY=.*$', 'APP_KEY=' + $key, [Text.RegularExpressions.RegexOptions]::Multiline); [IO.File]::WriteAllText($p, $text)"
if errorlevel 1 (
    echo Laravel application key could not be saved.
    pause
    exit /b 1
)

:start_services
REM Docker image များကို build လုပ်ပြီး MiniERP services အားလုံးကို စတင်ပါ။
echo Starting MiniERP...
docker compose up --build
if errorlevel 1 (
    echo MiniERP could not be started.
    pause
    exit /b 1
)

endlocal
