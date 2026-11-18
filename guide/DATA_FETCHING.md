# Data Fetching နှင့် Freeze Prevention လမ်းညွှန် (Myanmar)

## မိတ်ဆက်

ဒီစာတမ်းသည် MiniERP repository အတွက် frontend မှ backend API သို့ data ရယူ/ပြင်ဆင်ခြင်းနှင့် application freezing မဖြစ်အောင်ကာကွယ်ရေး architect-level guideline ဖြစ်သည်။

### ရည်ရွယ်ချက်

- `Page -> Component -> Service -> Backend API` data flow ကိုတည်ငြိမ်စေရန်။
- Loading, error, cancel, cache, pagination နှင့် mutation state များကိုစနစ်တကျထိန်းချုပ်ရန်။
- Slow request, heavy data, unbounded loop နှင့် resource leak များကိုကာကွယ်ရန်။

### Project Context

- Frontend: React 19 + Vite 8
- Backend: Laravel 13 + PHP
- Database: MySQL 8.4
- Container runtime: Docker Compose

## 1. Architecture

အကြံပြု data flow:

```text
Page/Feature
  -> Component
  -> React hook
  -> frontend/src/services/
  -> HTTP API
  -> Laravel Route
  -> Controller/Request/Policy
  -> Service/Model
  -> MySQL
```

- Page/component သည် UI ကိုတစ်ဦးတည်းတည်ဆောက်ပါ။ HTTP URL, header နှင့် response parsing ကို UI အတွင်းမထည့်ပါနှင့်။
- `frontend/src/services/` တွင် API endpoint ၏ request/response contract ကိုစုထားပါ။
- External request logic သည် reusable service/hook boundary တစ်ခုထဲတွင်ရှိသင့်ပါတယ်။
- Laravel controller တွင် HTTP concern ကိုထားပြီး business rule ရှုပ်ထွေးပါက service/domain class သို့ခွဲထုတ်ပါ။
- Model သည် Eloquent data relation/persistence ကိုတာဝန်ယူပြီး untrusted request ကိုတိုက်ရိုက်မယုံပါနှင့်။

## 2. API Base Configuration

Frontend API base ကို `VITE_API_URL` မှာသာထားပါ။

```text
VITE_API_URL=http://localhost:8000/api
```

- `VITE_*` value များသည် browser code ထဲရောက်သောကြောင့် secret မထည့်ပါနှင့်။
- API URL fallback ကို silent production secret/credential အဖြစ်မထားပါနှင့်။
- Environment များအလိုက် base URL ကို configure/lint/test လုပ်ပါ။
- URL trailing slash က `/api` နှင့် endpoint concatenation ကိုသေချာသိမ်းပါ။

## 3. HTTP Client Boundary

နောက်ပိုင်းတွင် shared fetch client ဖန်တီးမည်ဆိုလျှင် အောက်ပါတာဝန်များပေးသင့်ပါတယ်-

- `baseUrl`
- Authorization header injection
- JSON request/response parsing
- Timeout/abort support
- Consistent API error mapping
- Optional request ID/correlation header
- No hard-coded secret

`Page -> Component -> Service -> API` ပုံစံကို လိုက်နာပါ။

```js
// frontend/src/services/exampleService.js
export async function getExamples({ signal } = {}) {
  const response = await fetch(`${import.meta.env.VITE_API_URL}/examples`, {
    method: 'GET',
    headers: {
      Accept: 'application/json',
    },
    signal,
  })

  if (!response.ok) {
    throw new Error(`Failed to fetch examples: ${response.status}`)
  }

  return response.json()
}
```

အထက်ပါ service code သည် architectural example သက်သက်ဖြစ်ပြီး အသုံးမပြုမီ project error contract တစ်ခုကိုပြည့်စုံစွာဖန်တီးပါ။

## 4. Loading, Success, Error State

Page data fetching state ကို ထိန်းသင့်သည့်အနည်းဆုံး state များ-

```js
const [data, setData] = useState(null)
const [isLoading, setIsLoading] = useState(false)
const [error, setError] = useState(null)
```

Rules:

- Initial load/loading state ကို UI တွင်ရှင်းလင်းပြပါ။
- Network error/timeout/validation/authorization ကိုပြားပြားဆိုင်းဆုံး error contract အဖြစ်ခွဲပါ။
- React component unmounted ဖြစ်ပါက state update မလုပ်သင့်။
- Mutation success လည်း data က stale ဖြစ်နေနိုင်သည်။ List cache သို့မဟုတ် server response ပြန်သုံးပါ။
- Empty array နှင့် error state ကိုအတူတူမယူဆပါနှင့်။
- Retry ကို user action သို့မဟုတ် intentional policy အဖြစ်သာလုပ်ပါ။ Flaky test တစ်ခုကို mask မလုပ်ပါနှင့်။

## 5. React Component Pattern

Feature တစ်ခုစာ:

- Page/component တစ်ခုချင်းစီတွင် `useEffect` fetch ကိုတိုက်ရိုက်မရေးပါနှင့်။
- Reusable fetch logic ကို custom hook သို့ပြောင်းပါ။
- AbortController ကို effect cleanup ထဲတွင်သုံးပါ။
- Feature-specific cache/server state library လိုအပ်ပါက ပြဿနာအတိုင်းသာရွေးပါ။
- Existing project တွင် state library မရှိသေးပါက React state အခြေခံဖြင့်စတင်ပြီး smallest implementation ကိုသုံးပါ။

အကြံပြု custom hook boundary:

```text
useExamples()
  -> examplesService.list()
  -> /api/examples
```

Hook သည် loading/error/data/cancel/refetch API ကိုပြန်ပေးသင့်သည်။

## 6. Request Cancellation and Duplicate Requests

- Search/filter ပေါ်ပြောင်းလျှင် ရှေး request ကို cancel လုပ်ပါ။
- Page unmount ဖြစ်လျှင် in-flight request ကို cancel လုပ်ပါ။
- Strict Mode လိုဖြစ်နေသော development double effect ခေါ်ဆိုမှုကို UI duplication မဖြစ်အောင် service/hook cache သို့မဟုတ် ref cleanup ဖြင့်ကာကွယ်ပါ။
- Submit button များကို request ပြီးဆုံးမချိန်မခြား disable သို့မဟုတ် idempotency protection လုပ်ပါ။
- Expensive list/load endpoint များအတွက် request deduplication ကိုကြည့်ပါ။

## 7. Pagination, Sorting, Filtering

Server data တင်သွင်းသည်:

- `page`
- `per_page`
- `sort`
- `direction`
- Search/filter parameters

Pagination response ၏ contract တစ်ခုတည်းကို server နှင့် frontend နှစ်ဖက်သတ်မှတ်ပါ။ Example:

```json
{
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 0
  }
}
```

- Client filter/sort/slice သို့မဟုတ် server contract ကိုထဲဝင်မဖြစ်မချင်းရွေးပါ။
- Large dataset များတွင် server-side pagination/search/sort လုပ်ပါ။
- Filter/query state ကို URL query string သို့သိမ်းပါ။ Refresh/deep link မပျက်မယွင်းဖြစ်စေပါ။
- Page နောက်ကျသွားသော filter ပြောင်းမှုတွင် page ကို `1` ပြန်သတ်မှတ်ပါ။
- Input search သည် debounce သုံးရလောက် high frequency endpoint ဖြစ်လျှင် သင့်တော်သလိုသုံးပါ။

## 8. Mutation and Refresh

Create/update/delete အတွက်:

- Laravel Form Request သို့မဟုတ် validated DTO ဖြင့် request validate လုပ်ပါ။
- Resource-level authorization ကို server-side လုပ်ပါ။
- UI state ကိုသာအခြေခံပြီး mutation အောင်မြင်သည်ဟုမယူဆပါ။
- Mutation success ပြီးလျှင်:
  1. Query cache ထည့်သွင်းမှု invalidate လုပ်ပါ။
  2. သက်ဆိုင်ရာ detail/list ကို refetch လုပ်ပါ။
  3. Server မှပြန်လာသော deterministic response ကို cache/update လုပ်ပါ။
- Mutation failure တွင် optimistic update ကို rollback လုပ်ပါ၊ မလုပ်နိုင်လျှင် မသုံးပါ။
- Double submit, replay, stale update ကိုကာကွယ်ပါ။

## 9. Laravel API Contract

Laravel route/controller အတွက်:

- `routes/api.php` တွင် route ကိုသတ်မှတ်ပါ။
- `FormRequest` သို့မဟုတ် standard validator သုံးပြီး input validation လုပ်ပါ။
- `Policy`/middleware ဖြင့် authorization လုပ်ပါ။
- Controller response ကို `200`, `201`, `204`, `422`, `403`, `404`, `500` ကဲ့သို့ HTTP semantics တိုင်းကျွမ်းဝင်စွာထည့်ပါ။
- Pagination, relation include, field shape ကို API Resource သို့မဟုတ် consistent serializer ထဲစုပါ။
- Error response format ကို stable ဖြစ်အောင်သတ်မှတ်ပါ။

အကြံပြု validation error shape:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

Production response တွင် stack trace, SQL, secret, internal path မထည့်ပါနှင့်။

## 10. Cache Strategy

Feature မေးရမည့်မေးခွန်း:

- Data ကို ဘယ်နေရာ cache လုပ်မလဲ။
- Stale ဖြစ်ပြီး ဘယ်အချိန်မှာ refresh လုပ်မလဲ။
- Authorization အရ user တစ်ယောက် cache ကိုတစ်ယောက်ခွဲထားရမလား။
- Sensitive PII ကို browser cache/local storage ထဲမထားရပါ။

Rules:

- Static/config/reference data အတွက်သာ cache များသုံးပါ။
- User-specific ERP data ကို shared browser cache ပေါ်မထားသင့်ပါ။
- Cached value များကို mutation နောက် invalidate/refetch လုပ်ပါ။
- Cache TTL သည် backend volatility နှင့်သက်ဆိုင်ရမည်။
- localStorage မှာ auth token/PII မသိမ်းပါနှင့်။

## 11. Error Handling

Frontend တွင် အကျုံးဝင် error category:

- 400/422: Invalid input
- 401: Session expired/login required
- 403: Forbidden/permission missing
- 404: Resource missing
- 409: Conflict/stale mutation
- 429: Throttled/rate limited
- 5xx/network: Temporary server/network failure

UI-level:

- Validation field error ကို inline ပြပါ။
- Authorization/missing resource ကို appropriate page/state ပြပါ။
- 401 ၏ redirect policy ကို app-wide centralized place မှလုပ်ပါ။
- 5xx/ network ကို retry action/generic message ဖြင့်ဖြေရှင်းပါ။
- Raw backend exception ကို end user ကိုမပြပါနှင့်။

## 12. Security

- Untrusted API input ကို frontend လက်ခံထားသော်လည်း server-side validation များဖြစ်မဖြစ် လိုက်နာပါ။
- Object-level authorization ကို UI hide လုပ်ခြင်းတစ်ခုတည်းမထားပါနှင့်။
- API response ကို HTML အဖြစ်ထည့်မသွင်းပါနှင့်။ Rich text များလည်း approved sanitization လုပ်ပါ။
- Upload များသည် content type, size, extension, filename ကို backend တွင်စစ်ပါ။
- PII/secret များကို URL/log/localStorage/service cache တွင်မသိုလှောင်ပါနှင့်။
- CORS ကို allowed frontend origin များသာလျှင် restrict လုပ်ပါ။

## 13. Testing

Laravel backend:

- HTTP feature test ဖြင့် status code, JSON shape, validation, authorization ကိုစစ်ပါ။
- Wrong user/role ကနေ resource access ထွက်လာသည်ကို negative test လုပ်ပါ။
- Pagination/filter/sort boundary တို့ကို test လုပ်ပါ။
- Database migration/persistence behavior ကို existing SQLite test setup များအတိုင်းစစ်ပါ။

React frontend:

- Loading, success, empty, error state များကို component test လုပ်ပါ။
- Service-level test မှ request URL/method/response mapping ကိုစစ်ပါ။
- Search/filter/query param များကို behavior test လုပ်ပါ။
- Unmount cancellation, stale response, retry, mutation rollback တို့ကိုလိုအပ်သလိုစစ်ပါ။
- Network/process boundaries ကိုသာ mock လုပ်ပြီး implementation detail မစစ်ပါနှင့်။

## 14. Do နှင့် Don't

| Do | Don't |
| :--- | :--- |
| Page, service, backend API contract ကိုခွဲထားပါ။ | Component တစ်ခုထဲတွင် URL, validation, parsing, cache logic အားလုံးထားမထားပါနှင့်။ |
| React state ကို loading/error/success ကိုခွဲထားပါ။ | Fetch promise ကို UI state မရှိဘဲမပြီးဆုံးပါနှင့်ထားပါနှင့်။ |
| Query cancellation ကိုသုံးပါ။ | Unmounted component state update ဖြင့် memory leak warning မဖြစ်စေပါနှင့်။ |
| Server-side pagination/filter/sort ကိုသုံးပါ။ | Large ERP dataset ကို client ထဲတစ်ခါတည်းမခိုင်းပါနှင့်။ |
| Mutation အပြီး cache/refetch ကိုinvalidate လုပ်ပါ။ | UI ကိုသာ refresh လုပ်ပြီး stale server data ကိုမဖယ်ရှားပါနှင့်။ |
| API contract ကို test လုပ်ပါ။ | Status 200 သာပဲအောင်မြင်သည်ဟုယူဆပြီး JSON shape မစစ်ပါနှင့်။ |
| Secret မထည့်ဘဲ environment configure လုပ်ပါ။ | `VITE_*` သို့မဟုတ် frontend bundle ထဲ secret ထည့်မထားပါနှင့်။ |

## 15. Implementation Checklist

Feature တစ်ခုအတွက် data fetching ထည့်သည့်အခါ:

- [ ] API route, HTTP method, authorization, validation ကိုသတ်မှတ်ပြီးပြီ။
- [ ] Request/response JSON contract ကို documentation/test တွင်ထည့်ထားသည်။
- [ ] Frontend service/hook boundary ကိုအသုံးပြုသည်။
- [ ] Loading, success, empty, error, retry states များပြည့်စုံသည်။
- [ ] Abort/cancel/deduplication လိုအပ်သလောက်ထည့်ထားသည်။
- [ ] Pagination/filter/sort/search contract တစ်ခုတည်းဖြစ်သည်။
- [ ] Mutation cache/refetch/rollback လိုအပ်ချက်များအရဖြစ်သည်။
- [ ] Sensitive data/authorization ကို server-side စစ်ထားသည်။
- [ ] Laravel feature test နှင့် React focused test များ pass ဖြစ်သည်။
- [ ] `npm run lint`/build နှင့် `php artisan test` ကို run သင့်သည်။

## 16. Data Fetching အကြောင့် Application Freeze ကာကွယ်ရေး

Data fetching ကိုထိန်းချုပ်ပြီး API စောလျော့သွားပါက application freeze/hang ဖြစ်နိုင်သည်။ React, Laravel, MySQL နှင့် Docker boundary တိုင်းတွင် အောက်ပါအချက်များကိုသတ်မှတ်ပါ။

### 16.1 Frontend Request Safety

- `AbortController` ကို unmount, route/page/filter/page ပြောင်းလဲမှုအတွက် cleanup လုပ်ပါ။
- API timeout ကို finite value ထားပြီး indefinitely pending state မဖြစ်စေပါနှင့်။
- React effect dependency ကိုလိုအပ်သလောက်သာထားပြီး object/array ကိုတိုက်ရိုက် dependency မလုပ်ပါနှင့်။
- Effect cleanup မှ timer, listener, subscription, WebSocket/SSE ကိုလည်း မမေ့ပဲဖြေထုတ်ပါ။
- Strict Mode/effect re-run အတွက် request retry/deduplication/idempotent mutation ကိုသုံးပါ။
- Rapid submit/search/filter များအတွက် stale result, duplicate request, overlapping request မဖြစ်အောင်ကာကွယ်ပါ။

### 16.2 Loading and Async State

- Loading, success, empty, error, timeout, retry state များကိုစာရင်းအရတိတိပြားပြားခွဲပါ။
- Component unmounted ပြီးနောက် async result ဖြင့် `setState` မလုပ်ပါနှင့်။
- Retry count၊ retry delay၊ backoff/jitter၊ timeout ကို upper bound ပေးပါ။
- Retry storm မဖြစ်စေရန် overlapping retry မလုပ်ပါ။
- Empty data နှင့် fetch error ကို UI အတွင်းအတူတူမသုံးပါနှင့်။

### 16.3 Large Dataset

- ERP list/detail/report တို့အတွက် server-side pagination ကို default လုပ်ပါ။
- Page size ကို reasonable maximum ထားပါ။
- Search/filter/sort ကို server/database သို့ပို့ပြီး client တစ်ခုတည်းဖြင့် full dataset filter မလုပ်ပါနှင့်။
- 10,000+ record နှင့်အထက်ကို virtualization သို့မဟုတ် pagination အသုံးပြုပါ။
- CSV/PDF/export/import လုပ်ငန်းကြီးများကို synchronous browser loop အစား queued job သို့ပြောင်းပါ။
- Polling ကို hidden tab/unmounted component/page အတွင်း pause လုပ်ပါ။

### 16.4 Laravel and MySQL

- External HTTP call အတွက် connect/read timeout ထားပါ။
- Queueable/heavy operation ကို Laravel controller/sync request အတွင်းမလုပ်ပါနှင့်။
- Laravel အတွက် `QUEUE_CONNECTION=sync` သည် local/development အတွက်သင့်တော်ပြီး heavy workload အတွက် dedicated queue worker လိုအပ်နိုင်သည်။
- Pagination/filter/sort columns တွင် index ထည့်ပါ။
- Stable sort key နှင့် server-side limit သတ်မှတ်ပါ။
- Eloquent `N+1` query ကို eager loading ဖြင့်ရှင်းပါ။
- `SELECT *` နှင့် unbounded relation include ကိုရှောင်ပါ။
- Transaction အတွင်း network call/export/email မလုပ်ပါနှင့်။
- Long-running query ကို slow query log/EXPLAIN ဖြင့်စစ်ပါ။
- Lock duration ကိုကန့်သတ်ပြီး optimistic locking သို့မဟုတ် atomic update ကိုသင့်လျော်စွာသုံးပါ။

### 16.5 Memory and Subscription Cleanup

- Timer, interval, event listener, observer, WebSocket/SSE, subscription များကို cleanup လုပ်ပါ။
- Cache ကို unbounded မထားပါနှင့်။ TTL/eviction policy နှင့် user/sensitivity boundary ထားပါ။
- Uploaded/temp file ကို processing ပြီး cleanup လုပ်ပါ။
- Shared cache/localStorage/worker memory မှာ PII/secret မသိုလှောင်ပါနှင့်။
- Queue/worker တစ်ခု၏ memory accumulation ကို finite lifecycle အတွက်ပြန်စစ်ပါ။

### 16.6 Docker and Runtime

- Frontend, backend, MySQL, queue worker များအတွက် healthcheck/resource/disk/log limit စစ်ပါ။
- Restart loop ဖြစ်လျှင် root cause မစစ်ဘဲ container ကိုတစ်ခါတည်း restart မလုပ်ပါနှင့်။
- Container shutdown/graceful termination ကို support လုပ်ပါ။
- Database disk/full volume မဖြစ်အောင် storage capacity monitor လုပ်ပါ။
- Backend startup မတိုင်မီ MySQL ready/healthy ဖြစ်မှုကို Compose dependency ကစစ်ထားသည်။

## 17. Freeze Incident ဖြစ်လျှင်

1. Symptom ကို “တိကျသော endpointသည် timeout” မျိုးဖြင့်ဖော်ပြပါ။
2. Frontend/backend/database/container boundary အလိုက် reproduce လုပ်ပါ။
3. Recent deploy/config/cache/schema/job များကိုကြည့်ပါ။
4. CPU၊ memory၊ disk၊ DB connection၊ active request၊ queue wait၊ slow query စသည်ကိုစစ်ပါ။
5. Evidence မရှိဘဲ probable cause ကိုတရားဝင် root cause မတင်ပါနှင့်။
6. Timeout/cancel/pause/disable/rollback တို့ကနေ minimal mitigation လုပ်ပါ။
7. Permanent fix၊ automated regression test၊ stress/load verification ထည့်ပါ။

Freeze-prevention regression test အတွက်:

- Unmount cleanup
- Abort/cancel stale response
- Rapid search/filter duplicate prevention
- Large dataset pagination boundary
- Timeout/controlled promise
- Double-submit
- External API slow/timeout
- Poll/reconnect backoff
- Queue retry/max attempt
- Lock conflict/concurrency

## 18. Freeze Prevention Release Checklist

- [ ] Request timeout, abort/cancel cleanup ရှိသည်။
- [ ] Loading/error/empty/timeout/retry state များပြည့်စုံသည်။
- [ ] Effect dependency/effect loop မရှိသည်။
- [ ] Server-side pagination/filter/sort/safe limit ရှိသည်။
- [ ] Heavy report/export/import ကို queued job ဖြင့်လုပ်သည်။
- [ ] Database index/N+1/slow query/lock risk ကိုစစ်ပြီးသည်။
- [ ] Timer/listener/subscription/cache cleanup ရှိသည်။
- [ ] Polling/reconnect/backoff finite ဖြစ်သည်။
- [ ] Docker health/resource/disk/log limit ရှိသည်။
- [ ] Freeze regression test များ pass ဖြစ်သည်။
- [ ] Rollback၊ restart နှင့် incident escalation procedure သိထားသည်။

Freeze ဖြစ်နိုင်သည့် Blocker finding ကို “application နည်းနည်း slow” ဟုအကြမ်းမတင်ပါနှင့်။ Actual failing boundary၊ reproducible symptom၊ measurable impact၊ smallest fix နှင့် regression test ကိုဖော်ပြပါ။
