# Testing လမ်းညွှန် (Myanmar)

**Base URL:** `http://localhost:8000/api`  
**Authentication:** Laravel Passport  
**Database:** MySQL 8.4  
**Testing Tools:** cURL, Postman, PHPUnit

---

## 1. API Request Requirements

API Request တိုင်းတွင် အောက်ပါ Header ကို ထည့်သွင်းရမည်။

```http
Accept: application/json
```

Protected Endpoints များအတွက် Bearer Token လိုအပ်သည်။

```http
Authorization: Bearer <access_token>
```

---

## 2. Prerequisites

### 2.1 Start Services

Windows Command Prompt တွင် Run ပါ။

```bat
.\window\.bat
```

Services များ Running ဖြစ်နေစေရန် Terminal ကို ဖွင့်ထားပါ။

### 2.2 Setup Authentication

Terminal အသစ်တစ်ခုဖွင့်ပြီး Run ပါ။

```bat
.\setup-auth.bat
```

ဤ Script သည် အောက်ပါလုပ်ငန်းများကို လုပ်ဆောင်ရန် ရည်ရွယ်ထားသည်။

- Database Migrations
- Passport Keys Setup
- Passport Client Creation
- Admin Account Seeding
- Staff Account Seeding

### 2.3 Restart Backend

```bat
docker compose restart backend
```

### 2.4 Default Test Accounts

| Email | Password | Role |
|---|---|---|
| `admin@example.com` | `Admin@123456` | `admin` |
| `staff@example.com` | `Staff@123456` | `staff` |

Test Account Password များကို သက်ဆိုင်ရာ Seeder Configuration နှင့် `backend/.env` တွင် သတ်မှတ်ထားသည့်အတိုင်း စစ်ဆေးပါ။

---

## 3. Automated API Testing

### Windows

```bat
.\test-api.bat
```

### macOS / Linux

```bash
sh ./test-api.sh
```

### Database Safety Warning

**သတိပြုရန်:** Automated Tests များတွင် `RefreshDatabase` ကို အသုံးပြုထားသည်။

- Test Database ကို Refresh လုပ်နိုင်သည်။
- Test Scripts များသည် In-memory SQLite ကို အသုံးပြုရန် ရည်ရွယ်ထားသည်။
- MySQL Docker Database ကို စတင်အသုံးပြုခြင်း မရှိပါ။
- Docker MySQL Database ပေါ်တွင် `php artisan test` ကို တိုက်ရိုက်မလုပ်ပါနှင့်။

Test Summary ကို စစ်ဆေးပါ။

```text
Tests: 22 passed
```

အမှန်တကယ် Passed အရေအတွက်သည် Test Suite အပေါ် မူတည်သည်။

`.env` File မရှိကြောင်း Warning များသည် Test Environment တွင် ဖြစ်ပေါ်နိုင်သည်။ Test Failure နှင့် မရောထွေးစေရန် စစ်ဆေးပါ။

---

## 4. API Endpoints

### 4.1 Authentication Endpoints

| Method | Endpoint | Access | Description |
|---|---|---|---|
| POST | `/login` | Public | Login နှင့် Token ရယူခြင်း |
| GET | `/me` | Authenticated | လက်ရှိ User အချက်အလက်ရယူခြင်း |
| POST | `/logout` | Authenticated | Token Revoke လုပ်ခြင်း |

Login Rate Limit: Email နှင့် IP အပေါ်မူတည်ပြီး တစ်မိနစ်လျှင် 5 ကြိမ်။

### 4.2 Role-Protected Endpoints

| Method | Endpoint | Allowed Roles |
|---|---|---|
| GET | `/admin/dashboard` | `admin` |
| GET | `/management` | `admin`, `manager` |
| GET | `/staff/dashboard` | `staff` |

---

## 5. Manual API Testing (cURL)

### 5.1 Login

**Request**

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"Admin@123456"}'
```

**Expected Response: HTTP 200**

```json
{
  "success": true,
  "message": "Login successful",
  "token_type": "Bearer",
  "access_token": "<JWT>",
  "expires_at": "2026-11-26T12:00:00+00:00",
  "user": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@example.com",
    "role": "admin"
  }
}
```

Response ထဲမှ `access_token` ကို Copy လုပ်ပြီး Protected API များတွင် အသုံးပြုပါ။

### 5.2 Current User

```bash
curl http://localhost:8000/api/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <access_token>"
```

Expected: HTTP 200 နှင့် လက်ရှိ User အချက်အလက်။

### 5.3 Admin Dashboard

```bash
curl http://localhost:8000/api/admin/dashboard \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <access_token>"
```

Expected: Admin Token ဖြင့် HTTP 200။

### 5.4 Management

```bash
curl http://localhost:8000/api/management \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <access_token>"
```

Expected: Admin သို့မဟုတ် Manager Token ဖြင့် HTTP 200။

### 5.5 Staff Dashboard

```bash
curl http://localhost:8000/api/staff/dashboard \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <access_token>"
```

Expected: Staff Token ဖြင့် HTTP 200။

### 5.6 Logout

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <access_token>"
```

Expected: HTTP 200 နှင့် Logout Success Response။

Logout ပြီးနောက် ထို Token ကို ပြန်သုံးပါက HTTP 401 ရရှိရမည်။

---

## 6. Expected API Test Results

| Test Case | Expected Status | Expected Result |
|---|---:|---|
| Valid Login | 200 | Token နှင့် User Data |
| Unknown Email | 401 | Invalid Credentials |
| Wrong Password | 401 | Invalid Credentials |
| Missing Fields | 422 | Validation Errors |
| 6th Failed Login | 429 | Too Many Attempts |
| Missing Token | 401 | Unauthenticated |
| Invalid Token | 401 | Unauthenticated |
| Expired Token | 401 | Unauthenticated |
| Revoked Token | 401 | Unauthenticated |
| Admin → Admin Dashboard | 200 | Access Granted |
| Admin → Management | 200 | Access Granted |
| Staff → Admin Dashboard | 403 | Permission Denied |
| Staff → Management | 403 | Permission Denied |
| Staff → Staff Dashboard | 200 | Access Granted |
| Manager → Management | 200 | Access Granted |
| User with NULL Role | 403 | Permission Denied |

### 6.1 Common Error Responses

**401 Unauthorized**

```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

**403 Forbidden**

```json
{
  "success": false,
  "message": "You do not have permission."
}
```

**401 Invalid Credentials**

```json
{
  "success": false,
  "message": "Invalid email or password."
}
```

**422 Validation Error**

Email နှင့် Password မထည့်ထားပါက Validation Errors ပြန်ရမည်။

**429 Too Many Requests**

```text
Too Many Attempts.
```

---

## 7. Dynamic Role Verification

Role ကို Request တစ်ကြိမ်စီတွင် Database မှ ပြန်လည်စစ်ဆေးသောကြောင့် Token အသစ်မထုတ်ဘဲ Role ပြောင်းလဲမှုကို အသုံးပြုနိုင်သည်။

### Testing Procedure

**Step 1:** Staff Account ဖြင့် Login ဝင်ပါ။

```text
Email: staff@example.com
Password: Staff@123456
```

Token ကို သိမ်းထားပါ။

**Step 2:** Admin Dashboard ကို ခေါ်ပါ။

```http
GET /api/admin/dashboard
```

Expected: `403 Forbidden`

**Step 3:** Database ထဲတွင် Role ပြောင်းပါ။

```sql
UPDATE users
SET role = 'admin'
WHERE email = 'staff@example.com';
```

**Step 4:** ယခင် Token ကိုပင် အသုံးပြုပြီး Admin Dashboard ကို ပြန်ခေါ်ပါ။

Expected: `200 OK`

**Step 5:** မူလ Role ကို ပြန်ထားပါ။

```sql
UPDATE users
SET role = 'staff'
WHERE email = 'staff@example.com';
```

### MySQL Access

```bash
docker compose exec mysql mysql -uminierp -proot minierp
```

Production Database တွင် စမ်းသပ်ရန် ဤ SQL ကို မသုံးပါနှင့်။

---

## 8. Postman Testing

### 8.1 Create Environment

Postman Environment အသစ်တစ်ခု ဖန်တီးပါ။

| Variable | Value |
|---|---|
| `base_url` | `http://localhost:8000/api` |
| `token` | Empty |

### 8.2 Login Request

- Method: `POST`
- URL: `{{base_url}}/login`
- Header: `Accept: application/json`
- Header: `Content-Type: application/json`
- Body: Raw → JSON

```json
{
  "email": "admin@example.com",
  "password": "Admin@123456"
}
```

**Tests Tab**

```javascript
if (pm.response.code === 200) {
    pm.environment.set(
        "token",
        pm.response.json().access_token
    );
}
```

### 8.3 Protected Requests

Protected Request တိုင်းတွင် -

- Authorization Type: Bearer Token
- Token: `{{token}}`
- Header: `Accept: application/json`

### 8.4 Test Endpoints

```text
GET  {{base_url}}/me
GET  {{base_url}}/admin/dashboard
GET  {{base_url}}/management
GET  {{base_url}}/staff/dashboard
POST {{base_url}}/logout
```

User Role အလိုက် Expected Status Code များကို စစ်ဆေးပါ။

---

## 9. Troubleshooting

| Problem | Possible Cause | Solution |
|---|---|---|
| Personal access client not found for `users` user provider | Passport Client မရှိခြင်း | `setup-auth.bat` ပြန် Run ပါ |
| Login မှာ HTTP 500 | Passport Keys မရှိခြင်း သို့မဟုတ် Permission ပြဿနာ | Setup ပြန်လုပ်ပြီး Backend Restart လုပ်ပါ |
| Valid Token