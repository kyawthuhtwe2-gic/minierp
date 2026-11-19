#!/bin/sh
set -eu

# Script ကို ဘယ်နေရာမှ ခေါ်သည်ဖြစ်စေ project root မှ အလုပ်လုပ်ပါ။
cd "$(dirname "$0")"

# Docker command ကို အသုံးပြုနိုင်ခြင်း ရှိမရှိ စစ်ပါ။
if ! command -v docker >/dev/null 2>&1; then
    echo "Docker is not installed or is not available on PATH."
    exit 1
fi

# Docker engine ကို ချိတ်ဆက်အသုံးပြုနိုင်ခြင်း ရှိမရှိ စစ်ပါ။
if ! docker info >/dev/null 2>&1; then
    echo "Docker Desktop is not running."
    echo "Start Docker Desktop, wait until Docker is running, then run this script again."
    exit 1
fi

# Environment ဖိုင်များ မရှိသေးလျှင် ဥပမာဖိုင်များမှ ကူးယူဖန်တီးပါ။
if [ ! -f backend/.env ]; then
    cp backend/.env.example backend/.env
    echo "Created backend/.env"
fi

if [ ! -f frontend/.env ]; then
    cp frontend/.env.example frontend/.env
    echo "Created frontend/.env"
fi

# Laravel APP_KEY ရှိပြီးသားဆိုလျှင် key အသစ်မဖန်တီးပါနှင့်။
if ! grep -Eq '^APP_KEY=.+$' backend/.env; then
    echo "Generating Laravel application key..."

    # Key ကို terminal တွင်မပြဘဲ variable ထဲတွင် သိမ်းပါ။
    if ! app_key="$(docker compose run --rm --no-deps backend php artisan key:generate --show)"; then
        echo "Laravel application key could not be generated."
        exit 1
    fi

    if [ -z "$app_key" ]; then
        echo "Laravel application key could not be generated."
        exit 1
    fi

    # ယာယီဖိုင်ထဲတွင် APP_KEY တန်ဖိုးကိုသာ အစားထိုးပါ။
    tmp_env="$(mktemp "backend/.env.XXXXXX")"
    if ! awk -v key="$app_key" '
        /^APP_KEY=/ { print "APP_KEY=" key; updated=1; next }
        { print }
        END { if (!updated) exit 1 }
    ' backend/.env > "$tmp_env"; then
        rm -f "$tmp_env"
        echo "Laravel application key could not be saved: APP_KEY= is missing."
        exit 1
    fi

    if ! mv "$tmp_env" backend/.env; then
        rm -f "$tmp_env"
        echo "Laravel application key could not be saved."
        exit 1
    fi
fi

# Docker image များကို build လုပ်ပြီး MiniERP services များကို စတင်ပါ။
echo "Starting MiniERP..."
if ! docker compose up --build; then
    echo "MiniERP could not be started."
    exit 1
fi
