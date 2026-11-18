# Cybersecurity လမ်းညွှန် (Myanmar)

## မိတ်ဆက်

ဒီစာတမ်းသည် MiniERP repository အတွက် developer၊ reviewer၊ coding agent များနှင့် အသုံးပြုသူများ လိုက်နာရမည့် cybersecurity လုပ်ဆောင်မှုစည်းကမ်းများကိုဖော်ပြသည်။

### ရည်ရွယ်ချက်

- Authentication, authorization နှင့် trust boundary များကိုကာကွယ်ပါ။
- Secrets, sensitive data နှင့် external input များကိုလုံခြုံစွာထိန်းချုပ်ပါ။
- Vulnerability၊ security regression test နှင့် incident response ကိုတိတိပြားပြားလုပ်ဆောင်ပါ။

### Project Context

- Frontend: React 19 + Vite 8
- Backend: Laravel 13 + PHP
- Database: MySQL 8.4
- Container runtime: Docker Compose

## 1. အခြေခံ မလုပ်ရ (Don't) နှင့် လုပ်ရ (Do)

| မလုပ်ရ (Don't) | လုပ်ရ (Do) |
| :--- | :--- |
| အချက်အလက်၏ sensitivity ကိုမခွဲခြားဘဲ ထိန်းချုပ်မှုမလုပ်ပါနှင့်။ | Data sensitivity အဆင့်ကိုသတ်မှတ်ပြီး အနည်းဆုံး privilege principle ကိုအသုံးပြုပါ။ |
| Password၊ token၊ API key ကို source code သို့မဟုတ် Git history တွင်ထည့်မထားပါနှင့်။ | Environment variable သို့မဟုတ် approved secret store အသုံးပြုပါ။ |
| User input ကိုယုံကြည်လို့မရပါ။ | အဝင်တစ်ခုချင်းစီကို server-side validation လုပ်ပြီး safe primitive များဖြင့်ထိန်းချုပ်ပါ။ |
| UI hide/disable ချင်းကို authorization အဖြစ်ယူဆမထားပါနှင့်။ | Endpoint တိုင်းတွင် server-side authenticationနှင့် resource-level authorization ပြုလုပ်ပါ။ |
| SQL string ကို user input ဖြင့်ပေါင်းမစပ်ပါနှင့်။ | Laravel ORM/query builder သို့မဟုတ် parameterized query အသုံးပြုပါ။ |
| ချို့ယွင်းချက်ကို symptom ပေါ်တွင်ပဲ patch မလုပ်ပါနှင့်။ | Trust failure ရဲ့ root cause ကိုရှာပြီး exploit test ဖြင့်အတည်ပြုပါ။ |
| Debug log တွင် password၊ token သို့မဟုတ် PII ထည့်မထားပါနှင့်။ | Sensitive fields ကို redact လုပ်ပြီး structured log format အသုံးပြုပါ။ |
| Dependency ကို version မစစ်ဘဲမပေါင်းမပါနှင့်။ | Maintainable၊ actively-supported package များကိုသာထည့်ပြီး known vulnerability စစ်ဆေးပါ။ |
| Production/server infrastructure ကိုခွင့်ပြုချက်မရှိဘဲအပြောင်းအလဲမလုပ်ပါနှင့်။ | Local/staging environment နှင့် reversible၊ reviewed change ပဲအသုံးပြုပါ။ |
| Security issue ကို public issue/pr/commit တွင်အပြည့်အစုံမဖော်ပြပါနှင့်။ | Responsible disclosure procedure အတိုင်းဖြေရှင်းပြီး private detail များကိုကန့်သတ်ပါ။ |

## 2. Authentication

### လုပ်ရ

- Public မဟုတ်သော route/endpoint အားလုံးတွင် server-side identity verification လုပ်ပါ။
- Password ကို bcrypt, Argon2 သို့မဟုတ် project-approved modern KDF ဖြင့် hash လုပ်ပါ။
- Session/token ရဲ့ lifetime၊ rotation၊ logout invalidation၊ privilege change invalidation ကိုသတ်မှတ်ပါ။
- Login, reset password, MFA, API token တို့အတွက် rate limiting၊ lockout/throttling အစီအမံထားပါ။
- Authentication failure များတွင် user enumeration ဖြစ်အောင်အသေးစိတ်မပြပါနှင့်။
- Session identifier များကို login, privilege change အပြီးတွင် rotate ပါ။

### မလုပ်ရ

- Password ကို plaintext, reversible encryption သို့မဟုတ် MD5/SHA-1-only ဖြင့်မသိုလှောင်ပါနှင့်။
- Credential များကို URL query string သို့မဟုတ် log တွင်မထားပါနှင့်။
- Client-side route hide ချင်းကို login protection အဖြစ်မယူဆပါနှင့်။
- Token များကို browser local storage တွင်မသိုလှောင်ပါနှင့်။
- Authentication bypass/debug account ကို production တွင်မထားပါနှင့်။

## 3. Authorization

### လုပ်ရ

- Endpoint တစ်ခုချင်းစီတွင် resource-level authorization စစ်ပါ။
- “ဤအသုံးပြုသူသည် record ဤအရာကို access လုပ်ရမလား” ဟုမေးပါ။
- Policy/middleware/gate များကို enforcement တစ်နေရာတွင်စုထားပြီး reuse လုပ်ပါ။
- Default deny principle ကိုသုံးပါ။ Route အသစ်တစ်ခုကို permission decision explicit မရှိဘဲမဖန်တီးပါနှင့်။
- List, detail, create, update, delete, export တစ်ခုချင်းစီကိုစစ်ပါ။
- အောက်ပါအခြေအနေလက်ခံခံထားထားပြီး 403 သို့မဟုတ် project-standard deny response ပြန်ပါ။

### မလုပ်ရ

- “Login လုပ်ပြီးသည့်အတွက်” သာဘဲအာဏာရှိသည်ဟုမယူဆပါနှင့်။
- Role check သာပြီး object ownership ကိုမစစ်ပါနှင့်။
- ID parameter ကိုယုံကြည်ပြီး ဝင်ရောက်ခွင့်မစစ်ပါနှင့်။
- User ကို permission မရှိသည့် button/field တို့ပဲ hide လုပ်ပြီး server-side မှားမစစ်ပါနှင့်။
- Authorization logic ကို controller တစ်ခုချင်းစီတွင် copy/paste မလုပ်ပါနှင့်။

## 4. Input and Output Handling

### လုပ်ရ

- User, API client, file, header, webhook, queue message တို့ကို untrusted data အဖြစ်သတ်မှတ်ပါ။
- Laravel validation rules၊ allow-list, length limit၊ content type၊ format၊ enum၊ numeric range တို့ကိုအသုံးပြုပါ။
- Database မှ values များကို context-aware HTML/JS/CSS/URL encode လုပ်ပါ။
- SQL parameterized query/ORM binding အသုံးပြုပါ။
- File upload သည် MIME content၊ extension၊ size၊ filename sanitization နှင့် storage location ကိုစစ်ပါ။
- File path access မတိုင်မီ canonicalization လုပ်ပြီး allow-list directory အတွင်းသာတားဆီးပါ။
- Deserialization, redirect, template, command execution တို့ကို safe API များဖြင့်သာလုပ်ပါ။

### မလုပ်ရ

- Payload ကိုခွင့်ပြုသည့် စကားလုံးတစ်လုံးဖြစ်မဖြစ်သာစစ်ပြီး reject မလုပ်ပါနှင့်။
- Raw HTML ကိုပဲ render မလုပ်ပါနှင့်။
- Shell command တွင် user input ကို string concatenation မလုပ်ပါနှင့်။
- File name သို့မဟုတ် client-provided path ကို server file access တွင်တိုက်ရိုက်မသုံးပါနှင့်।
- Uploaded file များကို public web root တွင် executable permission မပေးပါနှင့်။

## 5. Secrets and Configuration

### လုပ်ရ

- `.env.example` တွင် placeholder values ထည့်ပြီး actual secret မထည့်ပါနှင့်။
- Local secret များကို `.gitignore` ဖြင့်ကာကွယ်ပါ။
- Secret အတွက် approved secret store သို့မဟုတ် environment mechanism အသုံးပြုပါ။
- Production, staging, local အတွက်အနည်းဆုံး privilege ရှိသော credentials ခွဲသတ်မှတ်ပါ။
- Secret တစ်ခု leak ဖြစ်သွားပါက delete/cleanup ပြုလုပ်မီ rotate လုပ်ပါ။
- Token၊ API key၊ certificate၊ private key များကို expiration နှင့် rotation schedule ထားပါ။

### မလုပ်ရ

- Secret ကို source code၊ README၊ screenshots၊ issue၊ log၊ test fixture၊ commit history တွင်မထည့်ပါနှင့်।
- “Public repository မဟုတ်လို့ safe” ဟုမယူဆပါနှင့်။
- တစ်ခုတည်းသော shared master key/token ကို environment/microservice အားလုံးတွင်မသုံးပါနှင့်။
- Secret ကို rotation မလုပ်ဘဲ git history မှ delete/cleanup ကိုပြဿနာပြေသည်ဟုမယူဆပါနှင့်။

## 6. Logging and Sensitive Data

### လုပ်ရ

- Request ID, user ID, action, result, timestamp, source IP (လိုအပ်လျှင်) တို့ကို audit event အဖြစ်ထည့်ပါ။
- Password, session token, API key, credit card, national ID, personal data များကို log မတင်ပါနှင့်။
- Error များကို internal detail များဖြင့်ပိတ်၍ public response တွင် generic အဖြစ်ပြပါ။
- Sensitive field များကို structured redact လုပ်ပါ။
- Security log များကို access control၊ retention၊ integrity protection လုပ်ပါ။

### မလုပ်ရ

- Authorization failure/credential attempt မှာ secret များပါသော full payload ကိုမရေးပါနှင့်။
- Debug mode ကို production တွင်မဖွင့်ပါနှင့်။
- Stack trace ကို public API/client/browser တွင်မပြပါနှင့်။
- Logs များကို sensitive data warehouse အဖြစ်အသုံးမပြုပါနှင့်။

## 7. Backend Laravel Checklist

- [ ] Route group တိုင်းတွင် authentication middleware ကိုသတ်မှတ်ထားသည်။
- [ ] Resource/policy authorization ကို server-side စစ်ထားသည်။
- [ ] Request validation သည် allow-list-based ဖြစ်သည်။
- [ ] Database access သည် Eloquent/query binding သို့မဟုတ် prepared statement ဖြစ်သည်။
- [ ] CSRF protection ကို Laravel default ဖြင့်ထိန်းထားသည်။
- [ ] CORS allow-origin/allow-method/allow-header များကို project domain သို့ restrict လုပ်ထားသည်။
- [ ] Password reset/token/email verification/account lockout များကိုလုပ်ထားသည်။
- [ ] Error response များတွင် secret/stack trace မပါဘူး။
- [ ] Security header များကို server/proxy/web framework မှထည့်ထားသည်။
- [ ] Migration သည် rollback, backup နှင့် data exposure impact ကိုထည့်သွင်းစဉ်းစားထားသည်။

## 8. Frontend React/Vite Checklist

- [ ] Sensitive authorization decision ကို frontend ပေါ်တွင်သက်သက်မထားပါနှင့်။
- [ ] `dangerouslySetInnerHTML` ကို မသုံးရသလိုအခြေအနေတွင် sanitized content သာအသုံးပြုပါ။
- [ ] External URL များကို allow-list သို့မဟုတ် safe navigation လုပ်ပါ။
- [ ] Browser storage တွင် password၊ token၊ PII မသိုလှောင်ပါနှင့်။
- [ ] API error သည် information disclosure မဖြစ်ပါစေရန် generic message ပြပါ။
- [ ] Environment variable များကို source တွင်ပေါင်းထည့်ပြီး secret မထားပါနှင့်။
- [ ] Production build တွင် source map/debug detail မထည့်သွင်းပါနှင့်။
- [ ] Dependency/package ၏ vulnerability စစ်ဆေးထားသည်။

## 9. Database and Data Protection

### လုပ်ရ

- Database account ကို read/write/delete လိုအပ်သလောက် minimum privilege ပေးပါ။
- Backup ကို encryption၊ access control၊ retention နှင့် restore test တို့ဖြင့်ကာကွယ်ပါ။
- PII/financial/sensitive record များကို data minimization လုပ်ပါ။
- Export/download ၏ authorization၊ rate limit၊ audit trail ကိုစစ်ပါ။
- Soft delete/hard delete များ၏ recovery နှင့် privacy retention requirement ကိုသတ်မှတ်ပါ။
- Migration မတိုင်မီ backup နှင့် rollback plan ရှိပါ။

### မလုပ်ရ

- Database credential ကို application-wide master account ထည့်သုံးပြီး least privilege မလိုက်နာပါနှင့်။
- Production data ကို unrelated test/development environment တွင် anonymization မလုပ်ဘဲမကူးမယူပါနှင့်။
- Backup ကိုစစ်မထားဘဲ restore လုပ်နိုင်သည်ဟုယူဆပါနှင့်။
- Sensitive field ကို encryption မလိုအပ်သည့်အတွက်မထည့်သည့်အခါ privacy impact စဉ်းစားမထားပါနှင့်။

## 10. Dependencies and Supply Chain

### လုပ်ရ

- Package manifest/lockfile ကို Git တွင်ထိန်းထားပြီး reproducible install သုံးပါ။
- Known vulnerability/unsupported package ကို update သို့မဟုတ် replace လုပ်ပါ။
- Package များကို install မတိုင်မီ maintainer၊ license၊ version၊ download/source activity ကိုအနည်းဆုံးစစ်ပါ။
- Automated dependency scanning ကို CI တွင်ထည့်ပါ။
- Upgrade တစ်ခုချင်းစီတွင် lockfile diff၊ build/test၊ security impact ကိုစစ်ပါ။

### မလုပ်ရ

- Security advisory မရှိသည့် package အသစ်ကို blind copy/paste မလုပ်ပါနှင့်။
- `latest` သို့မဟုတ် remote script ကို runtime တွင်တိုက်ရိုက် download/execute မလုပ်ပါနှင့်။
- Lockfile ကိုမထိန်းဘဲ install/update မလုပ်ပါနှင့်။
- Dependency update ဖြင့်ကျော်လွှားရန် test failure/suppression မလုပ်ပါနှင့်။

## 11. Security Review Rules

Security review အတွက် အောက်ပါ trust boundary များကိုစစ်ပါ-

- Browser-to-API
- API-to-database
- API-to-file storage
- API-to-third-party service
- Queue/job/worker
- Webhook/integration
- Secret/config/CI-CD

Finding တစ်ခုစီတွင် ရှင်းလင်းစွာဖော်ပြပါ-

1. **Severity:** Blocker / High / Medium / Low
2. **Evidence:** source file၊ route၊ function၊ request/response သို့မဟုတ် behavior
3. **Trust failure:** unvalidated input၊ missing authorization၊ secret exposure စသည်
4. **Impact and exploitability**
5. **Root cause**
6. **Smallest safe fix**
7. **Security regression test**

Finding တွင် guessed exploit သို့မဟုတ် requirement မပါသည့်သတင်းမထည့်ပါနှင့်။ Checked areas နှင့် out-of-scope areas ကိုတိတိကျကျ ခွဲခေါ်ပါ။

## 12. Security Regression Tests

### လုပ်ရ

- Vulnerability ကိုဖယ်ရှားပြီးပြီးတိုင်း exploit path ကိုပိတ်ထားသည်ကို test လုပ်ပါ။
- Unauthorized wrong user/role ကို deny လုပ်သည်ကိုအတည်ပြုပါ။
- Invalid input၊ injection-like payload၊ cross-object access၊ expired/invalid token များကို test လုပ်ပါ။
- Test မှာ real secret၊ real customer PII၊ real production credential မထည့်ပါနှင့်။
- Test behavior/contract ကို assert လုပ်ပြီး implementation detail ကိုမဆွဲမဆွဲပါနှင့်။

### မလုပ်ရ

- Vulnerability မရှိသည်ဟုခံယူပြီး regression test မရေးပါနှင့်။
- Right user သာ allow ဖြစ်သည်ကိုစစ်ပြီး wrong user ကိုမစစ်ပါနှင့်။
- Security test ကို random sleep/retry ဖြင့် flaky masking မလုပ်ပါနှင့်။
- Test environment တွင် real production data သို့မဟုတ် live service ကိုမသုံးပါနှင့်။

## 13. Incident Response

Potential security incident တွေ့ရှိပါက-

1. အဆင့်အတိအကျပြတ်သားမည်နည်းကိုစမ်းမလှုပ်မီ evidence ကို preserve လုပ်ပါ။
2. အချိန်၊ endpoint၊ account، data source၊ impact scope ကိုမှတ်ထားပါ။
3. Unauthorized access ရှိလျှင် affected account/token/secret ကို revoke/rotate လုပ်ပါ။
4. Logs၊ database audit trail၊ deployment history ကိုအတွင်းရေးအဖြစ် preserve လုပ်ပါ။
5. Impacted users/data owner/system owner များကို approved communication process ဖြင့်အသိပေးပါ။
6. Root cause fix မတိုင်မီ symptom suppress မလုပ်ပါနှင့်။
7. Incident ပြီးစီးမှုအတွက် timeline၊ cause၊ fix၊ validation၊ retention/remediation plan ကိုရေးပါ။

Secrets သို့မဟုတ် active compromise ဖြစ်သွားသည်မှာအလွန်အရေးကြီးသည့်အရေး။ Document/developer instructions ဖြင့် deletion သက်သက်ဖြင့်ကာကွယ်မှုမပြုလုပ်ပါနှင့်။

## 14. Coding Agent များအတွက် လုပ်ဆောင်ချက်

Coding agent ကိုအသုံးပြုသောအခါ-

- Relevant repository instructions၊ existing patterns၊ security skill/policy ကိုဖတ်ပါ။
- Security-affecting change များအတွက် data flow နှင့် trust boundary ကိုဖော်ပြပါ။
- Task မလိုအပ်သေးပါက assumptions မထည့်ပါနှင့်။
- Secret/config/production infrastructure ကိုမပြောင်းပါနှင့်။
- Exploit class တစ်ခုကို fix လျှင် sibling occurrences ကိုရှာပါ။
- Security negative test မပါဘဲ vulnerability fix ကိုမပြီးဆုံးဟုမယူဆပါနှင့်။
- Finding မရှိသော်လည်း checked areas ကိုတင်ပြပါ။
- Out-of-scope vulnerability classes ကိုခွဲခြားဖော်ပြပါ။

## 15. Quick Release Security Gate

Release မတိုင်မီ အောက်ပါအချက်များကိုစစ်ပါ-

- [ ] Authentication မဖြစ်မနေရှိသော endpoint များကိုစစ်ပြီးသွားသည်။
- [ ] Resource-level authorization negative test များ pass ဖြစ်သည်။
- [ ] Validation/SQL/HTML/file path များအား secure primitive အသုံးပြုထားသည်။
- [ ] Secrets/config များ source/log/client တွင်မပါဘူး။
- [ ] Dependency scan နှင့် lockfile review ပြီးသည်။
- [ ] Backups/rollback/migration impact ပြင်ဆင်ထားသည်။
- [ ] Security logs များတွင် PII/credential မပါဘူး။
- [ ] Security regression tests pass ဖြစ်သည်။
- [ ] Incident contact/escalation process သိထားသည်။

Security finding တစ်ခု Blocker ဖြစ်ပါက data loss၊ unauthorized access၊ secret exposure သို့မဟုတ် system compromise ဖြစ်နိုင်သောအချက်ကိုမထင်မရှား release မလုပ်ပါနှင့်။
