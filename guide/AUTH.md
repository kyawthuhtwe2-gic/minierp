# Authentication & Email Service Setup လမ်းညွှန် (Myanmar)

**Technology Stack**
- Backend: Laravel 13
- Authentication: Laravel Passport
- Database: MySQL 8.4
- Frontend: React 19 + Vite 8
- Email Services: Brevo SMTP / Amazon Simple Email Service (AWS SES)
- Deployment: Docker Compose

---

## 1. System Overview

MiniERP မှာ User Authentication နဲ့ Email Notification ကို သီးခြားတာဝန်ခွဲပြီး အသုံးပြုမယ်။

### 1.1 Authentication Method

MiniERP က အသုံးပြုမယ့် Authentication Method က—

**Email + Password Authentication with Token-Based API Authentication (Laravel Passport)**

အလုပ်လုပ်ပုံက—

1. User က Email နဲ့ Password ထည့်ပြီး Login ဝင်မယ်။
2. Laravel က Database ထဲက User ကို ရှာမယ်။
3. Password ကို Hash နဲ့ စစ်ဆေးမယ်။
4. မှန်ကန်ရင် Laravel Passport က Access Token ထုတ်ပေးမယ်။
5. React Frontend က API Request တိုင်းမှာ Bearer Token ထည့်ပေးမယ်။
6. Laravel က Token ကို Validate လုပ်ပြီး Request ကို ခွင့်ပြုမယ်။
7. Role Middleware က Admin, Manager, Staff စတဲ့ Permission တွေကို စစ်ဆေးမယ်။

### 1.2 Email Service ရဲ့ တာဝန်

Email Service က Authentication Method မဟုတ်ပါဘူး။ Email ပို့ဖို့အတွက် အသုံးပြုတဲ့ Service ဖြစ်ပါတယ်။

| Feature | အသုံးပြုမယ့် Laravel Component |
|---|---|
| Login | Email + Password |
| API Authentication | Laravel Passport |
| Welcome Email | Laravel Notification / Mail |
| Password Reset Email | Laravel Password Broker |
| Email Verification | Laravel Email Verification |
| Background Email | Laravel Queue |
| Email Delivery | Brevo SMTP / AWS SES |

### 1.3 Architecture

```text
React Frontend
      |
      | Email + Password
      v
Laravel API
      |
      v
User Authentication
      |
      +---- Password Verification
      |
      +---- Laravel Passport
      |          |
      |          +---- Access Token
      |
      +---- Role Authorization
      |
      +---- Email Notification
                  |
                  v
             Laravel Mail
                  |
             SMTP / SES
                  |
                  v
             User Email Inbox
```

---

## 2. Free SMTP Service vs AWS SES

MiniERP အတွက် Email Provider နှစ်မျိုးကို အသုံးပြုနိုင်တယ်။

### 2.1 Brevo SMTP (Free Plan)

Brevo က Free Plan မှာ တစ်ရက်ကို Email 300 စောင်အထိ ပို့နိုင်ပါတယ်။ Transactional Email လည်း Support လုပ်ပါတယ်။

အသုံးပြုနိုင်တဲ့ Feature များ—

- Welcome Email
- Password Reset Email
- Email Verification
- Account Notification
- Transactional Email

**SMTP Configuration**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=YOUR_BREVO_SMTP_LOGIN
MAIL_PASSWORD=YOUR_BREVO_SMTP_KEY
MAIL_SCHEME=tls

MAIL_FROM_ADDRESS=no-reply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

မှတ်ချက်:
- Brevo Account တစ်ခု ဖွင့်ပါ။
- Sender Email သို့မဟုတ် Domain ကို Verify လုပ်ပါ။
- SMTP Credentials ကို ရယူပါ။
- SMTP Login နဲ့ SMTP Key ကို `.env` ထဲမှာ ထည့်ပါ။
- Free Plan မှာ Brevo Branding ပါဝင်နိုင်ပါတယ်။

Official Website: https://www.brevo.com/

### 2.2 Amazon Simple Email Service (AWS SES)

AWS SES ဆိုတာ Amazon Web Services ရဲ့ Cloud Email Sending Service ဖြစ်ပါတယ်။

အသုံးပြုနိုင်တဲ့ Feature များ—

- Transactional Email
- Password Reset Email
- Welcome Email
- Email Verification
- Large-Scale Email Delivery
- Bounce and Complaint Monitoring
- Email Sending Statistics

AWS SES က Pay-as-you-go Service ဖြစ်ပါတယ်။ AWS Free Tier အကျုံးဝင်မှုက Account အမျိုးအစားနဲ့ စတင်အသုံးပြုတဲ့အချိန်ပေါ် မူတည်ပါတယ်။ အမြဲတမ်း အခမဲ့ဖြစ်မယ်လို့ မယူဆသင့်ပါဘူး။

Official Website: https://aws.amazon.com/ses/

---

## 3. AWS SES Setup – Step by Step

### Step 1: AWS Account ဖွင့်ခြင်း

1. https://aws.amazon.com/ ကို ဝင်ပါ။
2. AWS Account တစ်ခု Create လုပ်ပါ။
3. Billing Information ထည့်ပါ။
4. AWS Management Console ကို Login ဝင်ပါ။
5. Amazon SES Service ကို ရှာဖွေဖွင့်ပါ။

သတိပြုရန်: AWS Account မှာ Billing Information ထည့်ထားရနိုင်ပြီး Free Tier ကျော်လွန်သုံးစွဲမှုအတွက် Charges ဖြစ်နိုင်ပါတယ်။

### Step 2: AWS Region ရွေးချယ်ခြင်း

SES က Region အလိုက် Configuration နဲ့ SMTP Credentials ကို သီးခြားစီ စီမံပါတယ်။

ဥပမာ—

```text
Region: us-east-1
Region Name: US East (N. Virginia)
```

အသုံးပြုမယ့် Region ကို မှတ်ထားပါ။ နောက်ပိုင်း SMTP Host နဲ့ Credentials တွေမှာ ဒီ Region ကို အသုံးပြုရပါမယ်။

### Step 3: Domain Verification

AWS SES မှာ ကိုယ်ပိုင် Domain ကို Verify လုပ်ဖို့ အကြံပြုပါတယ်။

ဥပမာ—

```text
yourdomain.com
```

လုပ်ဆောင်ရန်—

1. Amazon SES ကို ဖွင့်ပါ။
2. Configuration ထဲက Verified Identities ကို သွားပါ။
3. Create Identity ကို နှိပ်ပါ။
4. Domain ကို ရွေးပါ။
5. ကိုယ့် Domain Name ထည့်ပါ။
6. DKIM Verification ကို Enable လုပ်ပါ။
7. AWS က ပေးထားတဲ့ DNS Records တွေကို Domain DNS Provider မှာ ထည့်ပါ။
8. Verification Complete ဖြစ်တဲ့အထိ စောင့်ပါ။

**DNS Records**

AWS က ပေးတဲ့ Records တွေကို တိတိကျကျ ထည့်ပါ။

- Domain Verification TXT Record
- DKIM CNAME Records
- လိုအပ်ပါက Custom MAIL FROM DNS Records

မှတ်ချက်: DNS Records တွေကို ကိုယ့် Domain DNS Provider ရဲ့ Dashboard မှာ ထည့်ရပါမယ်။

### Step 4: SES Sandbox မှ Production Access ပြောင်းခြင်း

AWS SES Account အသစ်တွေဟာ ပုံမှန်အားဖြင့် Sandbox Mode နဲ့ စတင်ပါတယ်။

Sandbox Mode မှာ—
- Verified Email Address တွေကိုသာ ပို့နိုင်ပါတယ်။
- Sending Quota ကန့်သတ်ချက်ရှိပါတယ်။
- အခြား User တွေဆီ Production Email ပို့ဖို့ အဆင်မပြေပါဘူး။

Production Access ရရှိဖို့—

1. Amazon SES Console ကို ဝင်ပါ။
2. Account Dashboard ကို ဖွင့်ပါ။
3. Sandbox Banner ထဲက Request Production Access ကို ရွေးပါ။
4. Mail Type ကို `Transactional` ရွေးပါ။
5. Website URL ထည့်ပါ။
6. Contact Email ထည့်ပါ။
7. Email ပို့မယ့် ရည်ရွယ်ချက်ကို ရှင်းပြပါ။
8. Request ကို Submit လုပ်ပါ။

ဥပမာ ရည်ရွယ်ချက်—

```text
We use Amazon SES to send transactional emails
for our MiniERP application.

Our emails include:
- Welcome emails for new users
- Password reset links
- Email verification notifications
- Account-related notifications

We do not use this service for unsolicited
bulk marketing emails.
```

AWS ရဲ့ Approval ရရှိပြီးမှ Unverified Recipient တွေဆီ Production Email ပို့နိုင်ပါမယ်။

### Step 5: SMTP Credentials ရယူခြင်း

SES SMTP အသုံးပြုရန် SMTP Credentials သီးခြားလိုအပ်ပါတယ်။

လုပ်ဆောင်ရန်—

1. Amazon SES Console ကို ဝင်ပါ။
2. SMTP Settings ကို ရွေးပါ။
3. Create SMTP Credentials ကို နှိပ်ပါ။
4. IAM SMTP Credential နည်းလမ်းကို အသုံးပြုမယ်ဆိုရင် AWS က ပေးတဲ့ Setup ကို လိုက်နာပါ။
5. SMTP Username နဲ့ SMTP Password ကို သိမ်းထားပါ။

**အရေးကြီးချက်**

AWS Access Key ID နဲ့ AWS Secret Access Key ကို SMTP Username/Password အဖြစ် တိုက်ရိုက် မသုံးရပါဘူး။ SES SMTP Credentials သီးခြားအသုံးပြုရပါတယ်။

### Step 6: SMTP Endpoint

ဥပမာ Region က `us-east-1` ဖြစ်ရင်—

```text
SMTP Host:
email-smtp.us-east-1.amazonaws.com

Port:
587

Encryption:
STARTTLS

Username:
YOUR_SES_SMTP_USERNAME

Password:
YOUR_SES_SMTP_PASSWORD
```

အခြား Region အသုံးပြုရင် SMTP Endpoint ကိုလည်း သက်ဆိုင်ရာ Region အလိုက် ပြောင်းရပါမယ်။

---

## 4. Laravel 13 – AWS SES SMTP Configuration

MiniERP Backend ရဲ့ `.env` ဖိုင်မှာ အောက်ပါ Configuration ထည့်ပါ။

```env
MAIL_MAILER=smtp

MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587

MAIL_USERNAME=YOUR_SES_SMTP_USERNAME
MAIL_PASSWORD=YOUR_SES_SMTP_PASSWORD

MAIL_SCHEME=tls

MAIL_FROM_ADDRESS=no-reply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

အစားထိုးရမယ့် တန်ဖိုးများ—

| Environment Variable | ထည့်ရမယ့်တန်ဖိုး |
|---|---|
| MAIL_HOST | SES SMTP Endpoint |
| MAIL_PORT | 587 |
| MAIL_USERNAME | SES SMTP Username |
| MAIL_PASSWORD | SES SMTP Password |
| MAIL_SCHEME | tls |
| MAIL_FROM_ADDRESS | Verified Sender Email |
| MAIL_FROM_NAME | MiniERP |

**Docker အသုံးပြုထားရင်**

Backend Container က `.env` ပြောင်းလဲမှုကို ပြန်ဖတ်စေရန်—

```bash
docker compose restart backend
```

Laravel Configuration Cache ကို ရှင်းရန်—

```bash
docker compose exec backend php artisan config:clear
```

Production မှာ Configuration Cache အသုံးပြုထားပါက လိုအပ်သလို ပြန်တည်ဆောက်ပါ—

```bash
docker compose exec backend php artisan config:cache
```

သတိပြုရန်: `.env` ထဲက SMTP Password ကို GitHub သို့ မတင်ပါနှင့်။

---

## 5. Laravel Welcome Email Setup

User အသစ် Register လုပ်ပြီးတဲ့အခါ Welcome Email ပို့နိုင်ပါတယ်။

လုပ်ဆောင်ပုံ—

```text
User Registration
       |
       v
Validate User Data
       |
       v
Create User
       |
       v
Hash Password
       |
       v
Send Welcome Notification
       |
       v
AWS SES / Brevo SMTP
       |
       v
User Inbox
```

Welcome Email မှာ ပါဝင်သင့်တာတွေ—

- User Name
- Account Email
- MiniERP Application Name
- Login URL
- Support Contact

**Security**

User ရဲ့ Plain Text Password ကို Welcome Email ထဲမှာ လုံးဝ မပို့ပါနဲ့။

User အသစ်အတွက် Password သတ်မှတ်ရန် လိုအပ်ပါက Secure Set-Password Link သို့မဟုတ် Invitation Link ကို အသုံးပြုပါ။

---

## 6. Laravel Password Reset Email Setup

Laravel မှာ Password Reset အတွက် Built-in Password Broker ပါဝင်ပါတယ်။

အသုံးပြုမယ့် Component များ—

- Laravel Password Broker
- Password Reset Token
- Laravel Notification
- Laravel Mail
- AWS SES / Brevo SMTP

လုပ်ဆောင်ပုံ—

```text
User Clicks Forgot Password
          |
          v
Enter Registered Email
          |
          v
Laravel Password Broker
          |
          v
Generate Reset Token
          |
          v
Send Reset Email
          |
          v
User Clicks Reset Link
          |
          v
Validate Reset Token
          |
          v
Set New Password
          |
          v
Hash New Password
          |
          v
Update Database
```

Password Reset Token မှာ Expiration နဲ့ One-Time Usage ကို သတ်မှတ်ထားရပါမယ်။

Laravel ရဲ့ `Password::sendResetLink()` နဲ့ `Password::reset()` ကို အသုံးပြုနိုင်ပါတယ်။

Password Reset ပြီးတဲ့အခါ ယခင် Passport Access Tokens တွေကို Revoke လုပ်ဖို့လည်း စဉ်းစားသင့်ပါတယ်။

---

## 7. Email Verification

MiniERP မှာ User Email မှန်ကန်ကြောင်း စစ်ဆေးဖို့ Laravel Email Verification ကို အသုံးပြုနိုင်ပါတယ်။

Flow—

1. User Register လုပ်မယ်။
2. Laravel က Verification Email ပို့မယ်။
3. User က Email ထဲက Verification Link ကို နှိပ်မယ်။
4. Laravel က Verification Signature နဲ့ Expiration ကို စစ်ဆေးမယ်။
5. အောင်မြင်ရင် Email Verified ဖြစ်မယ်။

Email Verification ကို မဖြစ်မနေ လိုအပ်တဲ့ Business Requirement ရှိမှ Login သို့မဟုတ် သက်ဆိုင်ရာ Feature တွေမှာ ကန့်သတ်သင့်ပါတယ်။

---

## 8. Laravel Queue – Background Email Sending

Email ပို့တာကို API Request အတွင်း တိုက်ရိုက်လုပ်ရင် Response ကြာနိုင်ပါတယ်။

အကြံပြုထားတဲ့ပုံစံက Queue ဖြစ်ပါတယ်။

```text
Laravel API
     |
     v
Create Notification Job
     |
     v
Queue Storage
     |
     v
Queue Worker
     |
     v
AWS SES / Brevo SMTP
     |
     v
Email Inbox
```

အသုံးပြုနိုင်တဲ့ Command များ—

```bash
php artisan queue:work
```

Production မှာ Queue Worker ကို သီးခြား Process အဖြစ် အမြဲအလုပ်လုပ်နေစေဖို့ စီမံရပါမယ်။

Queue အသုံးပြုမယ်ဆိုရင် Failed Jobs ကိုလည်း စောင့်ကြည့်ပြီး Retry Policy သတ်မှတ်ထားပါ။

---

## 9. Testing & Verification

### 9.1 SMTP Testing

စမ်းသပ်ရမယ့် အချက်များ—

- SMTP Credentials မှန်ကန်မှု
- Verified Sender Email
- SES Production Access
- SMTP Host နဲ့ Region မှန်ကန်မှု
- Port 587 နဲ့ TLS Connection
- Recipient Inbox ထဲ Email ရောက်ရှိမှု

### 9.2 Test Cases

| Test Case | Expected Result |
|---|---|
| New User Registration | Welcome Email ရောက်ရှိ |
| Forgot Password | Reset Email ရောက်ရှိ |
| Valid Reset Token | Password ပြောင်းနိုင် |
| Expired Reset Token | Password ပြောင်းခွင့်မရှိ |
| Invalid SMTP Credentials | Mail Failure ဖြစ် |
| Unverified Sender | SES က ပယ်ချနိုင် |
| SES Sandbox Recipient မ Verify လုပ်ထားခြင်း | Email ပို့မရ |
| Queue Worker Stopped | Email Job မပို့နိုင်သေး |

### 9.3 AWS SES Monitoring

AWS SES Console မှာ အောက်ပါတို့ကို စောင့်ကြည့်ပါ—

- Sending Statistics
- Bounce Rate
- Complaint Rate
- Sending Quota
- Reputation Metrics
- Suppression List

Invalid Email Address တွေဆီ အကြိမ်ကြိမ် ပို့တာကို ရှောင်ပါ။ Bounce နဲ့ Complaint တွေကို စနစ်တကျ Handle လုပ်ပါ။

---

## 10. Security Best Practices

1. SMTP Credentials ကို `.env` ထဲမှာသာ သိမ်းပါ။
2. `.env` ကို Git Repository ထဲ မတင်ပါနဲ့။
3. AWS IAM မှာ လိုအပ်တဲ့ Permission ကိုသာ ပေးပါ။
4. AWS Root Account Credentials ကို Application မှာ မသုံးပါနဲ့။
5. SES SMTP Credentials ကို အခြား Service Credentials တွေနဲ့ မရောထွေးပါနဲ့။
6. Password Reset Token ကို Expiration သတ်မှတ်ပါ။
7. Password Reset Request ကို Rate Limit လုပ်ပါ။
8. Welcome Email မှာ Plain Text Password မပို့ပါနဲ့။
9. Email ပို့တဲ့အခါ Queue အသုံးပြုပါ။
10. Bounce နဲ့ Complaint ကို Monitor လုပ်ပါ။
11. Production Domain မှာ SPF, DKIM နဲ့ DMARC ကို မှန်ကန်စွာ Configure လုပ်ပါ။
12. AWS Billing Alerts သတ်မှတ်ပါ။

---

## 11. Recommended Configuration for MiniERP

| Component | Recommended Service |
|---|---|
| User Login | Email + Password |
| API Authentication | Laravel Passport |
| Authorization | Role Middleware / Policies |
| Welcome Email | Laravel Notification |
| Password Reset | Laravel Password Broker |
| Email Verification | Laravel Email Verification |
| Email Transport | Brevo SMTP / AWS SES |
| Background Processing | Laravel Queue |
| Email Security | SPF, DKIM, DMARC |
| Monitoring | AWS SES Metrics / Provider Dashboard |

### Development Environment

Development အဆင့်မှာ—

```text
Laravel Mail
     |
     v
Brevo SMTP Free Plan
```

ကို အသုံးပြုနိုင်ပါတယ်။

### Production Environment

Production အဆင့်မှာ—

```text
Laravel Mail
     |
     v
AWS SES SMTP
     |
     v
Customer Email Inbox
```

ကို အသုံးပြုနိုင်ပါတယ်။

**အကြံပြုချက်:** MiniERP ရဲ့ ပထမအဆင့် Development မှာ Brevo Free Plan နဲ့ စမ်းသပ်ပြီး Production သို့ တင်တဲ့အခါ AWS SES ကို ပြောင်းသုံးနိုင်ပါတယ်။ Laravel Mail Configuration ကို Provider ပြောင်းလဲနိုင်အောင် သီးခြားထားပါ။

---

## 12. Official Documentation

### Laravel

- Laravel 13 Mail: https://laravel.com/docs/13.x/mail
- Laravel Notifications: https://laravel.com/docs/13.x/notifications
- Laravel Password Reset: https://laravel.com/docs/13.x/passwords
- Laravel Queues: https://laravel.com/docs/13.x/queues
- Laravel Authentication: https://laravel.com/docs/13.x/authentication

### AWS

- Amazon SES Getting Started: https://docs.aws.amazon.com/ses/latest/dg/getting-started-sending.html
- Amazon SES SMTP Setup: https://docs.aws.amazon.com/ses/latest/dg/send-email-smtp.html
- SES SMTP Credentials: https://docs.aws.amazon.com/ses/latest/dg/smtp-credentials.html
- SES Mail Manager SMTP Credentials: https://docs.aws.amazon.com/ses/latest/dg/smtp-credentials-mail-manager.html
- AWS SES Pricing: https://aws.amazon.com/ses/pricing/

### Brevo

- Brevo Website: https://www.brevo.com/
- Brevo Pricing: https://www.brevo.com/pricing/
- Brevo Free Plan Limits: https://help.brevo.com/hc/en-us/articles/208580669-FAQs-What-are-the-limits-of-the-Free-plan

---

## Final Summary

MiniERP မှာ Authentication နဲ့ Email Delivery ကို သီးခြားစီ စီမံသင့်ပါတယ်။

- **Authentication:** Email + Password
- **API Token:** Laravel Passport Personal Access Token
- **Authorization:** Role-Based Access Control
- **Password Reset:** Laravel Password Broker
- **Welcome Email:** Laravel Notification
- **Email Delivery:** Brevo SMTP သို့မဟုတ် AWS SES
- **Background Email:** Laravel Queue

ဒီ Architecture ကို အသုံးပြုခြင်းအားဖြင့် Email Provider ကို နောက်ပိုင်းမှာ Brevo ကနေ AWS SES သို့မဟုတ် အခြား SMTP Provider သို့ ပြောင်းလဲနိုင်ပြီး Authentication System ကို ပြန်လည်ရေးသားရန် မလိုအပ်ပါ။