# MiniERP

MiniERP သည် React၊ Vite၊ Laravel နှင့် MySQL ဖြင့်တည်ဆောက်ထားသည့် local development project ဖြစ်ပါတယ်။ Laravel Passport JWT login နှင့် role-based authorization ပါဝင်ပါတယ်။

## လက်ရှိအခြေအနေ

- React/Vite frontend သည် `http://localhost:5173` တွင် run ပြီး login page ကိုပြသပါတယ်။
- Laravel backend သည် `http://localhost:8000` တွင် run ပါမယ်။
- MySQL 8.4 သည် host port `3307` တွင်ရရှိနိုင်ပါတယ်။
- Login-only authentication (Passport access token၊ ၆၀ မိနစ်သက်တမ်း)။ Registration မပါပါ။
- Role ကို `users.role` column တွင်သိမ်းပြီး request တိုင်းတွင် database မှဖတ်ပါတယ်။ Role အမည်အသစ်ထည့်ရန် schema မပြောင်းရပါ။
- Backend startup တွင် migrations ကိုအလိုအလျောက် run ပါမယ်။

## အမြန်စတင်ရန် (Quick start)

Repository root မှ အဆင့်အတိုင်း run ပါ။

1. Services စတင်ရန် (terminal တစ်ခုတွင် ဆက်လက် run နေမည်):

   ```bat
   .\window.bat
   ```

   macOS/Linux: `sh ./mac.sh`

2. Backend စတင်ပြီးနောက် terminal အသစ်တစ်ခုတွင် auth setup (တစ်ကြိမ်သာလိုပြီး ထပ်run လည်းလုံခြုံသည်):

   ```bat
   .\setup-auth.bat
   ```

   macOS/Linux: `sh ./setup-auth.sh`

3. Backend ကို restart လုပ်ပါ:

   ```powershell
   docker compose restart backend
   ```

4. http://localhost:5173 ကိုဖွင့်ပြီး sign in လုပ်ပါ။

| Email | Password | Role |
| :--- | :--- | :--- |
| `admin@example.com` | `Admin@123456` | `admin` |
| `staff@example.com` | `Staff@123456` | `staff` |

ပထမဆုံး login ပြီးနောက် password ကိုပြောင်းပါ။ `backend/.env` တွင် `ADMIN_EMAIL` / `ADMIN_PASSWORD` သတ်မှတ်ပါက seeder က ၎င်းတို့ကိုသုံးပါတယ်။ Production တွင် default password ဖြင့် seed လုပ်ခွင့်မပြုပါ။

## Technology stack

| အပိုင်း | Technology |
| :--- | :--- |
| Frontend | React 19၊ Vite 8 |
| Backend | Laravel 13၊ PHP 8.5 Docker runtime |
| Database | MySQL 8.4 |
| Local orchestration | Docker Compose |
| Frontend lint | Oxlint |
| Backend tests | PHPUnit |

## Runtime နှင့် package versions

| အပိုင်း | Requirement | Resolved version |
| :--- | :--- | :--- |
| PHP | `^8.3` Composer requirement | PHP 8.5.12 Docker image |
| Laravel Framework | `^13.17` | 13.42.0 |
| Laravel Tinker | `^3.0` | 3.1.0 |
| PHPUnit | `^12.5.12` | 12.5.43 |
| Node.js | Docker image | 24 |
| MySQL | Docker image | 8.4 |
| React / React DOM | `^19.3.1` | 19.3.1 |
| Vite | `^8.3.6` | 8.3.8 |
| `@vitejs/plugin-react` | `^6.1.2` | 6.3.0 |
| Oxlint | `^1.90.0` | 1.93.0 |

Manifest မှ `^` သည် compatible version range ဖြစ်ပြီး lockfile က install မည့် package ကိုတိတိကျကျထိန်းထားပါတယ်။ Dependency သို့မဟုတ် version ပြောင်းပါက Composer/PHP နှင့် npm files ကိုတစ်ပြိုင်နက် update လုပ်ပါ-

- PHP: `backend/composer.json`, `backend/composer.lock`
- Frontend: `frontend/package.json`, `frontend/package-lock.json`

### Package တစ်ခုချင်း၏ ရည်ရွယ်ချက်

#### Frontend packages

| Package | အမျိုးအစား | မည်သည်အတွက်သုံးသည် | MiniERP တွင်အခန်းကဏ္ဍ |
| :--- | :--- | :--- | :--- |
| `react` | Runtime dependency | Component-based UI နှင့် state management | ERP page၊ reusable component နှင့် interactive starter UI ကိုတည်ဆောက်ရန် |
| `react-dom` | Runtime dependency | React component ကို browser DOM ထဲတွင် render လုပ်ခြင်း | `main.jsx` မှ app ကို `#root` အတွင်း mount လုပ်ရန် |
| `vite` | Development tool | Fast development server၊ HMR နှင့် production bundling | Frontend ကို port `5173` တွင် run ပြီး browser asset များကို build/publish လုပ်ရန် |
| `@vitejs/plugin-react` | Development plugin | React JSX နှင့် Fast Refresh ကို Vite တွင်ပံ့ပိုးခြင်း | `.jsx` source များကို browser အတွက် compile ပြီး code ပြောင်းလျှင်အမြန် reload ရှိရန် |
| `@types/react` | Development types | React API အတွက် TypeScript declaration | React component/editor type checking ကိုကူညီရန်။ လက်ရှိ JavaScript project တွင် optional build dependency အဖြစ်ရှိသည် |
| `@types/react-dom` | Development types | React DOM API အတွက် TypeScript declaration | DOM mounting/rendering API အသုံးပြုမှုကို tooling ကနားလည်ရန် |
| `oxlint` | Development tool | JavaScript/JSX code ကို linting စစ်ခြင်း | Syntax၊ bug-prone code နှင့် maintainability ပြဿနာကို `npm run lint` ကနေစစ်ရန် |

Frontend app ကို production build လုပ်သည်နှင့် `react` နှင့် `react-dom` ၏ UI code နှင့် package assets များသည် output bundle အတွင်းပါလာမည်။ `vite`၊ plugin၊ types နှင့် `oxlint` များသည် development/build tooling ဖြစ်ပြီး browser runtime ထဲတွင် tool ၏ source code အားလုံးကိုတိုက်ရိုက်မထည့်သွင်းပါ။

#### Backend packages

| Package | အမျိုးအစား | မည်သည်အတွက်သုံးသည် | MiniERP တွင်အခန်းကဏ္ဍ |
| :--- | :--- | :--- | :--- |
| `laravel/framework` | Runtime dependency | Routing၊ controller၊ ORM၊ validation၊ config၊ view နှင့် HTTP services | Backend MVC architecture၊ migration၊ database access၊ API/Blade response တို့ကိုပေးရန် |
| `laravel/tinker` | Development tool | Laravel application context တွင် PHP REPL run ခြင်း | Model/route/config တို့ကို local shell မှခဏစမ်းသပ်ရန် |
| `phpunit/phpunit` | Development tool | PHP automated unit/feature test runner | `php artisan test` က backend behavior နှင့် HTTP response များကိုစစ်ရန် |
| `laravel/pint` | Development tool | Laravel/PHP code style formatter | PHP ဖိုင်များကို PSR-style consistent formatting အဖြစ်ပြောင်းရန် |
| `laravel/pail` | Development tool | Laravel log stream/viewer | Development log များကို terminal ထဲတွင်တိုက်ရိုက်စောင့်ရန် |
| `nunomaduro/collision` | Development tool | PHPUnit/artisan failure display | PHP exception၊ test failure များကို readable terminal output အဖြစ်ပြသရန် |
| `mockery/mockery` | Development tool | PHP mock/spy object | External service/ORM dependency များကို unit test တွင်အစားထိုးရန် |
| `fakerphp/faker` | Development tool | Random realistic test data | Test factory/seeder/data များဖန်တီးရန် |

Laravel framework သည် application server အတွက် core package ဖြစ်သည်။ အခြား package များက framework မှအပိုဆောင်း development၊ testing၊ formatting capability များကိုပေးသည်။ ERP feature အသစ်များ၌ routing၊ controller၊ Eloquent ORM၊ validation များကို Laravel framework ပေါ်အခြေခံပါ။

#### Docker/runtime packages

| Component | အမျိုးအစား | မည်သည်အတွက်သုံးသည် | MiniERP configuration |
| :--- | :--- | :--- | :--- |
| PHP 8.5.12 | Runtime | Laravel နှင့် Composer packages run ခြင်း | `backend/Dockerfile` |
| Composer 2 | PHP dependency tool | Composer package install/autoload generation | PHP dependencies ကို Docker build time တွင် install လုပ်ရန် |
| Node.js 24 | Runtime/toolchain | Vite dev server နှင့် npm scripts run ခြင်း | `frontend/Dockerfile` |
| MySQL 8.4 | Database runtime | ERP relational data အတွက် SQL database | Compose `mysql` service; host port `3307` |
| Docker Compose | Orchestration | Frontend၊ backend၊ database services နှင့် volume ကိုတစ်စုတည်းစီမံခြင်း | `compose.yaml` |

PHP 8.5.12 Docker image ကို Composer `php ^8.3` requirement အပြင် project lockfile compatibility ကိုထပ်မံသေချာပေးထားပါတယ်။ Node.js 24 သည် Vite/Oxlint/React package tooling ကိုချိတ်ဆက် run ရန်သုံးပြီး frontend development server၊ lint နှင့် production build တို့ကိုလုပ်ပေးပါတယ်။

### Runtime dependency နှင့် Development dependency ခွဲခြားပုံ

- **Runtime dependency** ကို application run လုပ်စဉ်လိုအပ်သည်။
- **Development dependency** ကို local development၊ test၊ lint၊ formatting အတွက်လိုအပ်ပြီး production behavior ကိုချိန်ဆမည်မဟုတ်ပါ။
- `frontend/package.json` တွင် `dependencies` က runtime/UI packages ဖြစ်ပြီး `devDependencies` က build/lint/type tooling ဖြစ်သည်။
- `backend/composer.json` တွင် `require` က application runtime ဖြစ်ပြီး `require-dev` က test/format/debug packages ဖြစ်သည်။

Package အသစ်တိုးလာပါက လိုအပ်ချက်ကိုနားလည်စေရန်အတွက် အောက်ပါအချက်ကိုဆုံးဖြတ်ပါ-

1. Browser/HTTP run time လိုအပ်ပါက runtime dependency။
2. Test၊ lint၊ format သာလိုအပ်ပါက development dependency။
3. Existing Laravel/React capability တစ်ခုကို package မလိုဘဲ implement လုပ်နိုင်ပါက မထည့်သင့်ပါ။
4. လိုအပ်မှသာ package တစ်ခုထည့်ပြီး lockfile နှစ်ဖိုင်စလုံးကို update လုပ်ပါ။

## Project ဖွဲ့စည်းပုံ

```text
MiniERP/
├── backend/                  # Laravel 13 backend
│   ├── app/
│   │   ├── Http/Controllers/ # HTTP controllers
│   │   ├── Models/           # Eloquent models
│   │   └── Providers/        # Application service providers
│   ├── database/
│   │   ├── factories/        # Test data factories
│   │   ├── migrations/       # Database schema
│   │   └── seeders/          # Database seeders
│   ├── resources/views/      # Laravel Blade views
│   ├── routes/               # Web/API routes
│   ├── tests/                # PHPUnit tests
│   ├── composer.json
│   └── .env.example
├── frontend/                 # React 19 single-page application
│   ├── src/
│   │   ├── assets/           # Images and static assets
│   │   ├── components/       # Shared UI
│   │   │   ├── AppShell.jsx      # Fixed header + content area
│   │   │   ├── UserMenu.jsx      # Header name/email + profile popup (Logout)
│   │   │   └── AccessGrid.jsx    # Server-checked access cards
│   │   ├── pages/
│   │   │   ├── auth/         # LoginPage
│   │   │   ├── admin/        # AdminHome (role: admin)
│   │   │   └── user/         # UserHome, StaffDashboard (other roles)
│   │   ├── services/         # api.js (API client)
│   │   ├── App.jsx           # Login or role-based home
│   │   └── main.jsx
│   ├── public/
│   ├── package.json
│   └── .env.example
├── compose.yaml
├── window.bat / mac.sh       # Services စတင်ရန်
├── setup-auth.bat / .sh      # Auth setup (migrate, keys, client, admin)
├── test-api.bat / .sh        # Backend tests (in-memory SQLite)
├── README.md
└── developer.guide.md
```

`frontend/src/pages/` ကို role အလိုက်ခွဲထားသည်: `auth/` (LoginPage)၊ `admin/` (AdminHome)၊ `user/` (UserHome၊ StaffDashboard)။ Header ကို `components/AppShell` တွင်တစ်နေရာတည်းထားပြီး user name/email နှင့် Logout popup ကို `components/UserMenu` ဖြင့်ပြသည်။ `frontend/src/services/api.js` တွင် API client ရှိပြီး `backend/routes/api.php` တွင် auth routes ရှိပါတယ်။

## Frontend architecture

Frontend သည် React 19 နှင့် Vite 8 ကိုအသုံးပြုပြီး `frontend/src/main.jsx` မှ `App` component ကို `#root` element ထဲတွင် mount လုပ်ပါတယ်။

- `App.jsx` သည် session (`sessionStorage` token) ကိုစစ်ပြီး `LoginPage` သို့မဟုတ် role အလိုက် `AdminHome` (admin) / `UserHome` (အခြား role) ကိုပြသပါတယ်။
- `pages/` တွင် ERP page components ကိုထည့်ရန်သင့်ပါတယ်။
- `components/` တွင် ပြန်သုံးနိုင်သည့် UI components ကိုထည့်ပါ။
- `services/` တွင် API request နှင့် server data handling ကိုထားပါ။
- `assets/` နှင့် `public/` တွင် static assets များကိုထားနိုင်ပါတယ်။

Feature အသစ်များအတွက် အကြံပြု data flow က `Page -> Component -> Service -> Backend API` ဖြစ်ပါတယ်။

## Backend architecture

Backend သည် Laravel MVC pattern ကိုအခြေခံပါတယ်။

- `Route` သည် URL request ကို Controller သို့လမ်းပြပါမယ်။
- `Controller` သည် HTTP request ကိုလက်ခံပြီး validation၊ service call သို့မဟုတ် response ကိုစီမံပါမယ်။
- `Model` သည် Eloquent ORM ဖြင့် database entity ကိုဖော်ပြပါမယ်။
- `Migration` သည် database schema ကို version-controlled ပုံစံဖြင့်ပြောင်းလဲပါမယ်။
- Blade view များသည် `backend/resources/views/` တွင်ရှိပြီး React frontend သည် API-based SPA အဖြစ်ထပ်ဖြည့်နိုင်ပါတယ်။

Request flow က `Route -> Controller -> Model/Database -> Response` ဖြစ်ပါတယ်။ Business rule များရှုပ်ထွေးလာပါက Controller တစ်ခုတည်းတွင်အလွန်မထားဘဲ service layer သို့ခွဲထုတ်ပါ။ လက်ရှိ backend တွင် `User` model နှင့် Laravel base controller ရှိပြီး custom ERP controller များမရှိသေးပါ။

Feature အသစ်တစ်ခုချိန်တွင် Laravel route၊ controller၊ model/migration နှင့် React service/page ကိုတစ်ပြိုင်နက်ထည့်သင့်ပါတယ်။

## လိုအပ်ချက်များ

- Docker Desktop
- Docker Compose
- `window.bat` အတွက် Windows environment
- `mac.sh` အတွက် macOS သို့မဟုတ် Linux shell

Local PHP/Node ဖြင့် run လိုပါက PHP 8.3+၊ Composer၊ Node.js၊ npm နှင့် MySQL လိုအပ်ပါတယ်။

## Docker ဖြင့် စတင်ရန်

Repository root မှ:

Windows မှာ:

```bat
.\window.bat
```

macOS/Linux မှာ repository root မှ:

```sh
sh ./mac.sh
```

`window.bat` နှင့် `mac.sh` တို့က —

1. Docker Desktop ရှိမရှိစစ်ဆေးပါတယ်။
2. `backend/.env` နှင့် `frontend/.env` မရှိလျှင် `.env.example` မှဖန်တီးပါတယ်။
3. Laravel `APP_KEY` မရှိလျှင် generate လုပ်ပြီး backend env ထဲသို့သိမ်းပါတယ်။
4. `docker compose up --build` ဖြင့် services များစတင်ပါတယ်။

တစ်ဖန်တည်း Docker Compose ကိုလက်ဖြင့် run ရန်:

```powershell
docker compose up --build
```

Services ကိုရပ်ရန်:

```powershell
docker compose down
```

Local data ကိုထိမထားဘဲ container များကိုပိတ်ပြီးပြန်စရန်:

```powershell
docker compose down
docker compose up --build
```

## Auth setup အသေးစိတ်

`setup-auth.bat` / `setup-auth.sh` သည် အောက်ပါအဆင့်များကို Docker ထဲတွင် run ပါတယ်။ Data မဖျက်ပါ။

1. `docker compose build backend` - code ကို image ထဲသို့ပြန်ထည့်သည်။
2. `php artisan migrate --force` - `role` column နှင့် OAuth tables ကိုထပ်ထည့်သည်။
3. Passport keys ကိုမရှိမှသာ generate လုပ်ပြီး permission `600` ထားသည်။
4. `PersonalAccessClientSeeder` - personal access client မရှိမှသာဖန်တီးသည်။ ၎င်းမရှိလျှင် login တွင် `Personal access client not found` error ပေါ်သည်။
5. `AdminUserSeeder` - Admin account ကို `updateOrCreate` ဖြင့်ဖန်တီးသည်။ Password ကို ပထမဆုံးဖန်တီးချိန်တွင်သာသတ်မှတ်သည်။
6. `StaffUserSeeder` - Staff account ကို အလားတူဖန်တီးသည် (`STAFF_EMAIL` / `STAFF_PASSWORD` ကို `backend/.env` တွင်ပြောင်းနိုင်သည်)။

Passport keys သည် `backend-storage` volume ထဲ (`storage/oauth-*.key`) တွင်ရှိပြီး git ထဲမပါပါ။ Volume ကိုဖျက်လျှင် `setup-auth` ကိုပြန် run ပါ၊ ယခင် token များသုံးမရတော့ပါ။

## API endpoints

| Method | Endpoint | Access |
| :--- | :--- | :--- |
| POST | `/api/login` | Public (မိနစ်လျှင် ၅ ကြိမ် rate limit) |
| GET | `/api/me` | Login ဝင်ထားသူ |
| POST | `/api/logout` | Login ဝင်ထားသူ (token revoke) |
| GET | `/api/admin/dashboard` | `admin` |
| GET | `/api/management` | `admin`, `manager` |
| GET | `/api/staff/dashboard` | `staff` |

Response status: token မရှိ/မမှန်/သက်တမ်းကုန်/revoke ဖြစ်လျှင် `401`၊ role မလုံလောက်လျှင် `403`။ Route တွင် `role:admin,manager` ကဲ့သို့ role အမည်များကိုကော်မာဖြင့်ခွဲ၍ထည့်ပါ။

Postman / curl ဥပမာ:

```sh
curl -X POST http://localhost:8000/api/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"Admin@123456"}'

curl http://localhost:8000/api/me \
  -H "Accept: application/json" -H "Authorization: Bearer <access_token>"
```

User ၏ role ပြောင်းရန် database တွင်တိုက်ရိုက်ပြင်ပါ (မည်သည့် role အမည်မဆိုရသည်):

```sql
UPDATE users SET role = 'manager' WHERE email = 'user@example.com';
```

## Access URLs

- Frontend: http://localhost:5173
- Backend: http://localhost:8000
- MySQL host: `127.0.0.1:3307`

Backend container မှ database host သည် `mysql` ဖြစ်ပြီး host machine မှ `127.0.0.1:3307` အသုံးပြုနိုင်ပါတယ်။

## Environment configuration

Local environment ဖိုင်များကို git မှမထည့်သွင်းပါနှင့်။

```text
backend/.env
frontend/.env
```

Backend env မှအဓိက database settings:

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=minierp
DB_USERNAME=minierp
DB_PASSWORD=root
```

Frontend env:

```dotenv
VITE_API_URL=http://localhost:8000/api
```

Compose ၏ database credential များသည် local development အတွက်သာဖြစ်ပြီး production အတွက်အသုံးမပြုပါနှင့်။ Production secret ကို committed source ထဲမထည့်သွင်းပါနှင့်။

## Database migration

Backend container စတင်သည်နှင့်:

```sh
php artisan migrate --force
```

ကိုအလိုအလျောက် run ပါမယ်။

Migration အသစ်ဖန်တီးရန်:

```powershell
Set-Location .\backend
php artisan make:migration create_products_table
```

Docker Compose တွင် `mysql-data` named volume သည် MySQL data ကို persistent အဖြစ်သိမ်းထားပါတယ်။ Container ကိုပိတ်ပြီးပြန်စလျှင် database data မပျောက်ပါ။ Database ကို reset လိုလျှင် named volume ကို explicit အဖြစ်ဖျက်ရန်လိုအပ်ပြီး `mysql-data` ကိုဖျက်ခြင်းသည် local data ကိုအပြီးအပိုင်ဖျက်သွားနိုင်သောကြောင့် confirmation မရှိဘဲမလုပ်ပါနှင့်။

## Docker မပါဘဲ run ရန်

### Backend

Docker MySQL ကိုအစပထမ run လုပ်ပါ:

```powershell
docker compose up -d mysql
```

Backend dependencies ကို install လုပ်ပြီး environment ဖန်တီးပါ:

```powershell
Set-Location .\backend
composer install

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    php artisan key:generate
}

php artisan migrate
php artisan serve
```

Local backend `.env` က database settings က Compose MySQL port နှင့်ပတ်သက်ပြီး `127.0.0.1:3307` ကိုပြသထားတာဖြစ်ပါတယ်။

### Frontend

Terminal အသစ်ထဲတွင်:

```powershell
Set-Location .\frontend

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
}

npm ci
npm run dev
```

## Tests နှင့် quality checks

### Backend

> သတိ: Tests များသည် `RefreshDatabase` သုံးသောကြောင့် ချိတ်ထားသော database ကို **ဖျက်ပစ်ပါတယ်**။ `docker compose run ... php artisan test` ကို တိုက်ရိုက်မ run ပါနှင့်၊ MySQL data ပျက်မည်။ အောက်ပါ script ကိုသာသုံးပါ။

Docker ဖြင့် (in-memory SQLite ကိုအတင်းသုံးပြီး MySQL ကိုမထိပါ):

```bat
.\test-api.bat
```

macOS/Linux: `sh ./test-api.sh`

Docker မပါဘဲ local PHP ဖြင့် run လျှင် `pdo_sqlite` ဖွင့်ထားပြီး `APP_KEY` ရှိရမည်:

```powershell
Set-Location .\backend
php artisan test
```

### Frontend

```powershell
Set-Location .\frontend
npm run lint
npm run build
```

## Architecture

- Frontend data flow: `Page -> Component -> Service -> Backend API`
- Backend request flow: `Route -> Controller -> Model/Database -> Response`
- Laravel တိုင်းတာတည်ဆောက်ပုံ: MVC
- Business logic တိုးလာပါက Controller ထဲတွင် အလွန်အကျွံမထည့်ဘဲ သင့်လျော်သလို service/component အဖြစ်ခွဲထားပါ။
- API endpoint အသစ်၊ migration နှင့် frontend service ကို တစ်ပြိုင်နက် update လုပ်ပါ။
- Architect-level prompt templates ကို [developer.guide.md](developer.guide.md) တွင်ရယူနိုင်ပါတယ်။
