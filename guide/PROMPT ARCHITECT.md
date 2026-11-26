# Prompt Architect လမ်းညွှန် (Myanmar)

## မိတ်ဆက်

ဒီစာတမ်းသည် MiniERP repository အတွက် architect/technical lead များနှင့် coding agent များကို အသုံးပြုမည့် prompt များရေးဆွဲရန် ညွှန်ကြားထားသည့် လမ်းညွှန်ဖြစ်သည်။

### ရည်ရွယ်ချက်

- Architecture တူညီစေရန် reusable prompt များဖန်တီးပါ။
- Small incremental change များကိုလွယ်ကူစွာအကောင်အထည်ဖော်ပါ။
- `skills/` folder ရှိ workflow များကို လိုက်နာစေပါ။

### Project Context

- Frontend: React 19 + Vite 8
- Backend: Laravel 13 + PHP
- Database: MySQL 8.4
- Container runtime: Docker Compose

## 1. မည်သည့်အခါ Architect Prompt ကိုအသုံးပြုမလဲ

အောက်ပါကိစ္စများအတွက် architect prompt ကိုအသုံးပြုပါ-

- System-wide architecture ကို review လုပ်ခြင်း
- Module, layer, သို့မဟုတ် feature boundary များကိုသတ်မှတ်ခြင်း
- Duplication, coupling, circular dependency, သို့မဟုတ် technical debt ကိုစစ်ဆေးခြင်း
- Refactor plan တင်ပြခြင်း
- Security-sensitive feature တစ်ခုအတွက် architecture ကိုထိန်းသိမ်းခြင်း
- Feature implementation မတိုင်မီ data flow, API contract, migration, test strategy ကိုချထားခြင်း

Single-file/diff review အတွက် `architecture-review` မသုံးပါနှင့်။ အဆင့်အလိုက် အောက်ပါ skill များကိုအသုံးပြုပါ-

| လုပ်ငန်း | အသုံးပြုရန် skill |
| :--- | :--- |
| အကြောင်းရင်းမသိသေးသော bug/debugging | `skills/debugging/SKILL.md` |
| အကြောင်းရင်းသိပြီးသား defect ပြင်ခြင်း | `skills/bug-fix/SKILL.md` |
| Test strategy နှင့် automated test | `skills/testing/SKILL.md` |
| Diff၊ branch၊ သို့မဟုတ် PR-like change review | `skills/code-quality-review/SKILL.md` |
| Modules/layers/system-wide structure review | `skills/architecture-review/SKILL.md` |
| Auth၊ authorization၊ secret၊ input နှင့် trust boundary | `skills/security-audit/SKILL.md` |

## 2. MiniERP Project Context

Prompt များတွင် အောက်ပါလက်ရှိ architecture ကိုအခြေခံပါ-

- **Frontend:** React 19၊ Vite 8၊ `frontend/src/`
- **Backend:** Laravel 13၊ PHP 8.3+၊ `backend/app/`၊ `backend/routes/`၊ `backend/database/migrations/`
- **Database:** Docker Compose ဖြင့် MySQL 8.4
- **Frontend data flow:** `Page -> Component -> Service -> Backend API`
- **Backend request flow:** `Route -> Controller -> Model/Database -> Response`
- **Local URLs:** Frontend `http://localhost:5173`၊ Backend `http://localhost:8000`
- **Local API base:** `frontend/.env` တွင် `VITE_API_URL=http://localhost:8000/api`

ပြောင်းလဲမှုများကို project ၏ လက်ရှိ style ကိုထိန်းသိမ်းပြီး small incremental steps ဖြင့်လျာထားပါ။

## 3. Architect Prompt မူလပုံစံ

အောက်ပါ template ကို feature-specific အချက်အလက်များဖြင့်ဖြည့်ပြီးအသုံးပြုပါ။

```text
သင်သည် MiniERP အတွက် Software Architect ဖြစ်သည်။

အောက်ပါ repository instructions နှင့် skills ကိုဖတ်ပါ—
- CLAUDE.md
- README.md
- skills/architecture-review/SKILL.md
- လိုအပ်သည့် skill(s): [skill ဖိုင်အမည်များ]

Task:
[ဖြည့်စွက်မည့် architecture သို့မဟုတ် implementation လုပ်ငန်း]

Project context:
- Frontend: React/Vite
- Backend: Laravel
- Database: MySQL
- Current limitation or desired behavior: [ရေးပါ]

Scope:
- Included: [ပါဝင်သည့် modules/files]
- Excluded: [မပါဝင်သည့် modules/files]
- Constraints: [compatibility, deadline, migration, security စသည်]

လုပ်ဆောင်ရန်—
1. လက်ရှိ modules/layers နှင့် dependency direction ကို map လုပ်ပါ။
2. အထောက်အထားဖြစ်သော files/modules ကိုဖော်ပြပါ။
3. architecture concern တစ်ခုချင်းစီအတွက် concrete evidence ထည့်ပါ။
4. Change များကို smallest working increments ဖြင့်ခွဲပါ။
5. API contract, database migration, backward compatibility, security boundary, test coverage တို့ကိုထည့်သွင်းပါ။
6. မလိုအပ်သော rewrite သို့မဟုတ် speculative optimization ကိုရှောင်ပါ။
7. မိမိတင်ပြချက်အားလုံးကိုလက်ရှိ project conventions ဖြင့်အတည်ပြုပါ။

Do not change code unless the prompt explicitly asks for implementation.
No assumptions, hidden requirements, or invented APIs.
When a trade-off exists, recommend the most practical minimal option and explain why.

ရလဒ် format:
1. ရှင်းလင်းချက်
2. လက်ရှိ architecture
3. အဓိက findings
4. အကြံပြုချက်များ
5. implementation steps
6. test plan
7. risks and left-alone items
```

## 4. Skill Routing Prompt များ

### Architecture Review

```text
Use skills/architecture-review/SKILL.md.
MiniERP ၏ system-wide structure ကို review လုပ်ပါ။
Diff/file quality review မပြုပါနှင့်။
Module boundaries၊ dependency direction၊ duplicated business rules၊ single responsibility၊ layering၊ coupling/cohesion ကိုစစ်ဆေးပါ။

Finding တစ်ခုစီမှာ—
- Evidence: ဖိုင်/ module အမည်
- Problem
- Smallest safe fix
- What it unblocks
- Effort and risk

Architecture ကအဆင်ပြေပါကအဆင်ပြေသည်ဟုပြောပါ။ Finding မရှိလျှင် finding ပြုလုပ်မတင်ပါနှင့်။
Big-bang rewrite မတင်ပါနှင့်။
```

### Feature Architecture / Implementation Design

```text
Use README.md and the relevant skills in skills/.

Feature:
[feature အမည်နှင့် expected behavior]

လက်ရှိ pattern ကိုထိန်းသိမ်းပြီး ဒီ feature အတွက်
frontend page/component/service flow,
backend route/controller/service/model flow,
database migration,
API contract,
validation,
error handling,
permission/security check,
regression test,
manual verification တို့ကိုဖော်ပြပါ။

Open-ended requirement မရှိစေရန် missing decisions များကိုရှင်းပါ။
If clarification is unavoidable, ask only the one most important question.
Default to the smallest implementation consistent with current architecture.
```

### Debugging နှင့် Bug Fix

```text
Bug symptoms:
- Actual: [actual behavior]
- Expected: [expected behavior]
- Trigger: [input/request/environment]
- Evidence: [log/stack trace/test output]

Cause မသိသေးပါက skills/debugging/SKILL.md ကိုအသုံးပြုပါ။
Cause အတည်ပြုပြီးပါက skills/bug-fix/SKILL.md ကိုတင်ဆက်ပါ။
First reproduce, then state the root cause in one sentence, then plan a minimal fix.
Before fixing, define a regression test that fails for the same reason.
Search for sibling occurrences.
```

### Test Strategy

```text
Use skills/testing/SKILL.md.

Feature/change:
[ရေးပါ]

Risk-first test plan ဆွဲပါ—
1. Core observable behavior
2. Boundary conditions
3. Error paths
4. Integration seams
5. Authorization/security negative cases

Project's existing Laravel/PHPUnit and React test conventions ကိုလိုက်နာပါ။
No retries to mask flaky tests. No implementation-detail assertions.
Run the smallest relevant test during development, then the surrounding suite.
```

### Security Review

```text
Use skills/security-audit/SKILL.md.

Component/feature:
[ရေးပါ]

Trust boundaries:
- [user input, API, DB, files, headers, webhooks, secrets စသည်]

Authentication, resource-level authorization, least privilege,
input validation, output encoding, secrets, logging, sensitive data,
parameterized database access, and security regression tests ကိုစစ်ပါ။

Finding တစ်ခုစီတွင် severity၊ impact/exploitability၊ evidence၊ root cause၊ minimal fix၊ and regression test ကိုဖော်ပြပါ။
Checked areas နှင့် explicitly out-of-scope areas ကိုခွဲခြားပါ။
```

## 5. Finding Severity

Code/architecture review အတွက် severity ကိုအောက်ပါအတိုင်းသုံးပါ-

- **Blocker:** Bug၊ data loss၊ breaking change၊ အချက်အလက်ပျက်စီးမှု၊ သို့မဟုတ် security exploit ဖြစ်နိုင်သည်။
- **Should-fix:** မကြာခင် maintainability၊ correctness၊ သို့မဟုတ် change risk ကိုသိသာစွာတိုးစေသည်။
- **Nit:** Optional style/polish။ Project convention မဟုတ်လျှင် preference အဖြစ်မဖော်ပြပါနှင့်။

System-wide architecture အတွက် `Blocker` ကို အများအားဖြင့် data loss၊ security၊ သို့မဟုတ် များစွာသော subsystem များကို အသုံးမပြုနိုင်အောင်ဖြစ်စေသည့် ပြဿနာများအတွက် reserve လုပ်နိုင်ပြီး၊ ကျန် finding များကို evidence-based pain/effort အတိုင်း အရင်ဆုံးမှနောက်ဆုံး အစဉ်တင်ပါ။

## 6. Architecture Findings Format

```markdown
## Finding 1: [အမည်]

- **Severity:** Blocker / Should-fix / Nit
- **Evidence:** [ဖိုင်၊ module၊ dependency, သို့မဟုတ် behavior]
- **Problem:** [အဘယ်ကြောင့်ပြဿနာဖြစ်သည်]
- **Smallest fix:** [အနည်းဆုံး working change]
- **Risk:** [change ၏သက်ရောက်မှု]
- **Test:** [မည်သို့verifyမည်]
```

Architecture အတည်ပြုချက်တစ်ခုစီအတွက် concrete file/module နှင့် change ၏အကျိုးကိုဖော်ပြပါ။ "Clean code" သို့မဟုတ် "best practice" တစ်ခုတည်းဖြင့် finding မဖန်တီးပါနှင့်။

## 7. Implementation Handoff Format

Architecture review ပြီး implementation လုပ်ရန် handoff ဖြစ်ပါက:

~~~markdown
## Goal
[လူတစ်ယောက်တည်းနားလည်နိုင်သည့် target behavior]

## In scope
- [ ]

## Out of scope
- [ ]

## Changes
1. [smallest first step]
2. [second step]
3. [verification step]

## Data/API changes
- Request:
- Response:
- Migration:
- Backward compatibility:

## Tests
- [focused regression/behavior test]
- [surrounding suite]

## Verification commands
~~~powershell
Set-Location .\backend
php artisan test

Set-Location ..\frontend
npm run lint
npm run build
~~~

## Risks / Rollback
- [risk]
- [safe rollback or incremental fallback]
~~~

## 8. Prompt ရေးရာတွင်မူတည်ရမည့်စည်းမျဉ်း

- Project instruction ကို skill instruction ထက်အာဏာအမြင့်ထားပါ။
- Scope၊ expected behavior၊ constraints တို့ကိုမေးခွန်းတင်ခင်ရှင်းပါ။
- `/` ထဲက skill များကိုထပ်ခါတလဲလဲမညွှန်ပါနှင့်—တစ်ခုချင်းစီ၏ ownership ကိုလိုက်ပါ။
- Test run မပြုမချင်း validation မပြီးဆုံးပါ။
- Reproduction evidence မရှိသေးမီ bug ကိုပြင်မည်ဆိုသော prompt မရေးပါနှင့်။
- Architecture ကိုbig-bang rewrite မပြုဘဲ incremental migration အဖြစ်သတ်မှတ်ပါ။
- Unrelated rename/refactor/style change ကိုအဓိက task ထဲမထည့်ပါနှင့်။
- `.env`၊ credentials၊ secrets နှင့် production infrastructure ကို prompt တွင်ထည့်သွင်းမထုတ်မဖော်ပါ။
- အတိအကျဖော်ပြထားသော file/endpoint မရှိဘဲ API၊ table၊ service တစ်ခုကိုတီထွင်မထားပါနှင့်။
- ယူဆချက်ကိုသက်သေမဖြစ်မချင်း assumption ဟုဖော်ပြပါ။

## 9. Prompt ဥပမာများ

အောက်ပါ prompt များသည် MiniERP ၏ လက်ရှိ architecture ကိုအခြေခံ၍ copy/paste အသုံးပြုနိုင်သောလက်တွေ့ဥပမာများဖြစ်ပါတယ်။

### ဥပမာ 1: Feature Architecture အတွက်

```text
သင်သည် MiniERP အတွက် Software Architect ဖြစ်သည်။

Repository instructions:
- README.md
- CLAUDE.md
- commands/**
- skills/**

Task:
"Products" feature အတွက် architecture နှင့် implementation plan ဆွဲပါ။

Context:
- MiniERP သည် React 19/Vite 8 frontend၊ Laravel 13 backend၊ MySQL 8.4 database ကိုအသုံးပြုသည်။
- လက်ရှိ frontend တွင် API integration မရှိသေးပါ။
- လက်ရှိ backend တွင် routes/api.php မရှိသေးပါ။
- Frontend flow: Page -> Component -> Service -> Backend API
- Backend flow: Route -> Controller -> Model/Database -> Response

In scope:
- Products list
- Create product
- Update product
- Delete product
- Product name, SKU, price, stock

Out of scope:
- Inventory purchase/sales workflow
- Supplier management
- Authentication system
- Frontend visual redesign

Constraints:
- Incremental change သာသုံးပါ။
- Existing project conventions ကိုထိန်းသိမ်းပါ။
- Database အပြောင်းအလဲကို migration ဖြင့်လုပ်ပါ။
- Public API response format ကို documentation မသုံးရေတွက်မချင်းတည်တံ့စေရပါ။
- Security-sensitive input handling ကိုလုံးလုံးလျားလျားစစ်ဆေးပါ။

လုပ်ဆောင်ရန်:
1. အနိမ့်ဆုံး API endpoints သတ်မှတ်ပါ။
2. Laravel route/controller/model/migration boundaries ကိုဖော်ပြပါ။
3. React page/component/service structure ကိုဖော်ပြပါ။
4. Validation rule နှင့် error response များကိုတိတိကျကျရေးပါ။
5. Backward compatibility နှင့် migration risk ကိုစစ်ပါ။
6. Laravel/PHPUnit နှင့် React test cases များကို risk အလိုက်ဖော်ပြပါ။
7. Verification command များကို README.md ပုံစံအတိုင်းထည့်ပါ။

Do not change code unless the prompt explicitly asks for implementation.
Do not invent existing APIs or database columns.
Ask only one clarification if a required decision is genuinely missing.

ရလဒ်:
1. Architecture summary
2. API contract
3. Database design
4. Frontend design
5. Backend design
6. Implementation steps
7. Test plan
8. Risks and left-alone items
```

### ဥပမာ 2: System-Wide Architecture Review

```text
Use skills/architecture-review/SKILL.md.

Review target:
- MiniERP repository-wide architecture
- Frontend: frontend/
- Backend: backend/
- Docker/database: compose.yaml

Scope:
Included:
- Module boundaries
- Frontend folder responsibilities
- Laravel controller/model/migration organization
- Frontend-to-backend API seams
- Coupling and duplication
- Migration and Docker setup risks

Excluded:
- Current starter UI visual style
- Third-party package micro-optimization
- Security implementation details unless they affect architecture

Method:
1. README.md နှင့် code အသုံးပြုသည့်ပုံစံကို map လုပ်ပါ။
2. Dependency direction ကိုအမှန်တကယ်ရှိသည့် code မှထုတ်ယူပါ။
3. Change hurt နေသည့် places များကိုဖော်ပြပါ။
4. Finding တစ်ခုစီတွင် concrete evidence ရှိရပါမယ်။
5. Smallest incremental fix များကို recommendation အရင်ဆုံးထားပါ။
6. Architecture က healthy ဖြစ်ပါက finding မတင်ပါနှင့်။

Finding format:
- Severity
- Evidence
- Problem
- Smallest fix
- Risk
- Verification
- What it unblocks

Do not propose a rewrite unless incremental improvement is impossible.
List what should explicitly be left alone.
```

### ဥပမာ 3: Diff/Code Quality Review

```text
Use skills/code-quality-review/SKILL.md.

Review scope:
- Uncommitted changes
- Changed files only
- MiniERP React/Laravel project conventions

Before reporting:
- Change intent ကို README.md နှင့် diff မှသိပါ။
- Design correctness ကို detail readability မတိုင်မီစစ်ပါ။
- Existing project convention မဟုတ်ပါက personal preference အဖြစ် finding မလုပ်ပါ။

Check:
- Correctness and edge cases
- Laravel validation/error handling
- React state and render behavior
- API contract compatibility
- Resource cleanup
- DRY and single responsibility
- Performance risks only with evidence
- Changed behavior test coverage

Report severity:
- Blocker
- Should-fix
- Nit

Every finding must include:
- Exact file and symbol/location
- Concrete defect
- Impact
- Minimal suggested fix
- Relevant regression test

Do not review system-wide architecture.
Do not report speculative issues without evidence.
Do not use /security-review unless requested separately.
```

### ဥပမာ 4: Debugging ပြီး Bug Fix

```text
Use skills/debugging/SKILL.md first.

Bug:
Frontend မှ POST /api/products သို့ request ပို့သည်။

Observable:
- Actual: Laravel backend က 500 response ပြန်သည်။
- Expected: Valid product data အတွက် 201 response ပြန်သင့်သည်။
- Trigger: Name/SKU/price/stock များပါသည့် JSON request
- Environment: Docker Compose local environment
- Evidence: [paste actual response and relevant stack trace]

Unknown:
- Root cause
- Database/schema mismatch
- Validation
- Laravel route/controller wiring
- Docker environment

Debugging steps:
1. Minimum reproducible request နှင့် actual/expected ကိုသိမ်းပါ။
2. Route, controller, model, migration နှင့် database state ကိုတစ်ထပ်ချင်းစစ်ပါ။
3. One falsifiable hypothesis at a time.
4. Full stack trace နှင့် actual values ကို observe ပါ။
5. Root cause ကို evidence ဖြင့်တစ်ဝါကျပြောပါ။

Root cause confirmed ပြီးသည်နှင့်—
Use skills/bug-fix/SKILL.md.

Fix:
- Reproduce on current code
- Write failing regression test before fix
- Fix root cause only
- Search sibling occurrences
- Run focused test, then smallest relevant suite
- Remove temporary instrumentation

Do not guess-fix or retry implementation without a diagnosis.
```

### ဥပမာ 5: Security Review

```text
Use skills/security-audit/SKILL.md.

Review target:
[controller/route/service/file or feature name]

Trust boundaries:
- Browser JSON input
- HTTP headers and query parameters
- Laravel routes and middleware
- Database queries
- File uploads, if present
- Session/token handling, if present
- Environment and secrets

Check:
- Server-side authentication and authorization
- Resource-level object ownership
- Deny-by-default permission decisions
- Least privilege
- Validated input at entry
- Parameterized queries
- Context-aware output encoding
- Sensitive data excluded from logs/errors
- No secrets in source or client code
- Security regression and negative tests

Findings must include:
- Severity based on impact and exploitability
- Evidence and vulnerable code location
- Root trust failure
- Minimal fix
- Security regression test

Explicitly state:
- Which vulnerability classes were checked
- Which areas were out of scope

Do not suppress an error or filter a payload string as the fix.
Fix the trust-boundary root cause.
```

### ဥပမာ 6: Test Plan

```text
Use skills/testing/SKILL.md.

Change:
[feature or bug fix name]

Task:
Risk-first automated test plan နှင့် regression test outline ရေးပါ။

Priority:
1. Core observable behavior
2. Empty/invalid/boundary inputs
3. Database migration and persistence behavior
4. Authorization/negative cases
5. API integration
6. Error handling

Rules:
- Test behavior/contracts, not implementation details.
- One behavior per test.
- Arrange-act-assert structure.
- Deterministic setup and teardown.
- No wall-clock/network/random test dependency.
- Mock only at process/network boundaries.
- Do not use retries or sleeps to hide flaky tests.

Existing suite:
- Backend: backend/tests/
- Frontend: frontend package scripts

Validation:
- Run the smallest focused test/case first.
- Run php artisan test for backend.
- Run npm run lint and npm run build for frontend.
- Confirm a new regression test fails before the fix.
```
