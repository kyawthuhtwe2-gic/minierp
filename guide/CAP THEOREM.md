# CAP Theorem လမ်းညွှန် (Myanmar)

## မိတ်ဆက်

ဒီစာတမ်းသည် MiniERP အတွက် distributed system များတွင် CAP Theorem ၏ အဓိပ္ပာယ်၊ trade-off များနှင့် database architecture ရွေးချယ်ရာတွင် ထည့်သွင်းစဉ်းစားရမည့်အချက်များကို ရှင်းပြထားသည်။

### ရည်ရွယ်ချက်

- Consistency, Availability နှင့် Partition Tolerance တို့၏ အဓိပ္ပာယ်ကိုရှင်းလင်းစွာခွဲခြားရန်။
- Network partition ဖြစ်သည့်အခါ system ၏ behavior ကိုကြိုတင်သတ်မှတ်ရန်။
- CAP Theorem ကို database design နှင့် replication ဆုံးဖြတ်ချက်များတွင် မှန်ကန်စွာအသုံးချရန်။

### Project Context

- Frontend: React 19 + Vite 8
- Backend: Laravel 13 + PHP
- Database: MySQL 8.4
- Container runtime: Docker Compose

## 1. CAP Theorem ဆိုသည်မှာ

CAP သည် distributed data system တစ်ခုတွင် အောက်ပါဂုဏ်သတ္တိသုံးခုကို ရည်ညွှန်းသည်—

| ဂုဏ်သတ္တိ | အဓိပ္ပာယ် |
| :--- | :--- |
| **Consistency (C)** | ဖတ်ရှုသည့် request များသည် နောက်ဆုံးအောင်မြင်ပြီးသား write ကိုမြင်ရပြီး၊ system တစ်ခုလုံးတွင် တူညီသော data ကိုမြင်စေရန် ကြိုးပမ်းခြင်း။ |
| **Availability (A)** | အလုပ်လုပ်နေသော node သို့ရောက်လာသည့် request တိုင်းသည် အောင်မြင်သော response သို့မဟုတ် သတ်မှတ်ထားသော failure response တစ်ခုကို အချိန်မီပြန်ရခြင်း။ |
| **Partition Tolerance (P)** | Node များအကြား network ဆက်သွယ်မှု ပြတ်တောက်ခြင်း သို့မဟုတ် နှောင့်နှေးခြင်းရှိသော်လည်း system က ဆက်လက်လုပ်ဆောင်နိုင်ရန် ဒီဇိုင်းလုပ်ထားခြင်း။ |

CAP Theorem ၏ အဓိကသဘောတရားမှာ network partition ဖြစ်နေချိန်တွင် distributed system သည် **Consistency နှင့် Availability နှစ်ခုလုံးကို တစ်ပြိုင်နက် အပြည့်အဝအာမမခံနိုင်ခြင်း** ဖြစ်သည်။ Partition Tolerance ကို လက်တွေ့ network ပေါ်ရှိ distributed system များတွင် လျစ်လျူရှု၍မရသောကြောင့် partition ဖြစ်ချိန်တွင် C သို့မဟုတ် A ဘက်ကို ရွေးချယ်ရသည်။

## 2. Partition ဖြစ်ချိန်တွင် ရွေးချယ်ရမည့် behavior

| ချဉ်းကပ်ပုံ | Partition ဖြစ်ချိန် behavior | အကျိုးဆက် |
| :--- | :--- | :--- |
| **CP (Consistency + Partition Tolerance)** | Data မတူညီမှုကိုရှောင်ရန် အချို့ request များကိုစောင့်ဆိုင်းစေခြင်း သို့မဟုတ် reject လုပ်ခြင်း။ | Data consistency ကိုဦးစားပေးသော်လည်း partition ရှိသည့်ဘက်တွင် availability လျော့နိုင်သည်။ |
| **AP (Availability + Partition Tolerance)** | Partition ဖြစ်နေသော်လည်း request များကိုဆက်လက်လက်ခံပြီး လုပ်ဆောင်ခြင်း။ | Availability ကိုဦးစားပေးသော်လည်း node များတွင် ယာယီ data မတူညီမှုဖြစ်နိုင်ပြီး နောက်ပိုင်း reconcile လုပ်ရနိုင်သည်။ |

- **CP ဥပမာ:** ငွေလက်ကျန်ကို တိကျစွာထိန်းသိမ်းရန် replica များအကြား အတည်ပြုချက်မရသေးပါက ငွေလွှဲမှုကို ရပ်ဆိုင်းထားခြင်း။
- **AP ဥပမာ:** Network ပြတ်နေချိန်တွင်လည်း order တင်နိုင်စေပြီး connection ပြန်ရချိန်တွင် data များကို synchronize လုပ်ခြင်း။
- ဥပမာများသည် architecture ပုံစံကို ရှင်းပြရန်သာဖြစ်ပြီး database ထုတ်ကုန်တစ်ခုချင်းစီ၏ အပြုအမူကို အစားမထိုးပါ။

## 3. အဓိပ္ပာယ်ဖွင့်ဆိုရာတွင် သတိပြုရန်

- CAP ၏ **Consistency** သည် အများအားဖြင့် linearizability ကဲ့သို့ operation များအကြား မြင်ရသည့်အချိန်အစီအစဉ်ကို ဆိုလိုသည်။ ACID ၏ consistency rule များနှင့် အမည်တူသော်လည်း အဓိပ္ပာယ်တူညီသည်ဟု မယူဆပါနှင့်။
- CAP သည် request အားလုံးကို အချိန်တိုင်း C သို့မဟုတ် A တစ်ခုတည်းအဖြစ် ရွေးချယ်ရမည်ဟု မဆိုလိုပါ။ အဓိက trade-off သည် **network partition ဖြစ်နေသည့်အချိန်** အတွက်ဖြစ်သည်။
- Availability သည် response ပြန်လာခြင်းကို ဆိုလိုပြီး response ထဲက data သည် နောက်ဆုံး write ဖြစ်သည်ဟု မအာမခံပါ။ Error response ပြန်ခြင်းကို business success အဖြစ်မယူဆပါနှင့်။
- Single-node database တွင် node များအကြား partition ဆိုသည့်အခြေအနေမရှိသဖြင့် CAP ကိုတိုက်ရိုက်အသုံးချ၍ C/A အမျိုးအစားတပ်ခြင်းထက် failure mode နှင့် recovery ကို သီးခြားစဉ်းစားပါ။

## 4. MiniERP အတွက် အသုံးချရန်

MiniERP ၏ လက်ရှိ project context သည် Laravel backend နှင့် MySQL ကို Docker Compose ဖြင့်အသုံးပြုထားခြင်းဖြစ်သည်။ လက်ရှိ setup ကို distributed database အဖြစ်သတ်မှတ်ရန် အထောက်အထားမရှိပါ။ Replica သို့မဟုတ် service များအကြား network dependency ထည့်သွင်းမည်ဆိုမှ CAP trade-off ကို တိတိကျကျဆုံးဖြတ်ပါ။

### Data အမျိုးအစားအလိုက် စဉ်းစားရန်

| Data/လုပ်ငန်း | ဦးစားပေးရန် | စဉ်းစားရမည့်အချက် |
| :--- | :--- | :--- |
| ငွေလွှဲမှု၊ invoice၊ stock လျှော့ချမှု | Consistency | တူညီသော order သို့မဟုတ် transaction ကို နှစ်ကြိမ်မလုပ်မိစေရန် transaction နှင့် idempotency ကိုစဉ်းစားပါ။ |
| Product list၊ report၊ dashboard | Availability သို့မဟုတ် stale-read လက်ခံနိုင်မှု | Replica မှ data အနည်းငယ်နောက်ကျပြီးပြခြင်းကို လုပ်ငန်းစည်းမျဉ်းက ခွင့်ပြုမပြု သတ်မှတ်ပါ။ |
| Order/stock ပြောင်းလဲမှုအပြီး ဖတ်ခြင်း | Read-after-write consistency | ပြောင်းလဲမှုအပြီး နောက်ဆက်တွဲဖတ်ရှုမှုကို authoritative database မှလုပ်ရန် သို့မဟုတ် replica နောက်ကျမှုကိုကိုင်တွယ်ရန်။ |

### Design checklist

- Partition သို့မဟုတ် database ချိတ်ဆက်မှုဆုံးရှုံးပါက အရေးကြီးသော write များကို fail လုပ်မည်၊ queue ထဲသိမ်းမည်၊ သို့မဟုတ် နောက်မှ retry လုပ်မည်ကိုသတ်မှတ်ပါ။
- Retry လုပ်မည့် mutation များကို idempotent ဖြစ်အောင်ဒီဇိုင်းလုပ်ပြီး duplicate transaction မဖြစ်စေရန် ကာကွယ်ပါ။
- Replica အသုံးပြုပါက read-after-write၊ replication lag၊ failover နှင့် data reconciliation behavior ကိုမှတ်တမ်းတင်ပြီးစမ်းသပ်ပါ။
- Timeout၊ retry limit၊ error response နှင့် user-facing message များကိုရှင်းလင်းစွာသတ်မှတ်ပါ။ Indefinite wait မလုပ်ပါနှင့်။
- Backup၊ restore နှင့် failover ကို စမ်းသပ်ပါ။ Replication တစ်ခုတည်းကို backup အဖြစ်မယူဆပါနှင့်။
- Business-critical operation များတွင် data integrity နှင့် availability တို့အနက် မည်သည့်အရာကို ဘယ်အခြေအနေတွင်ဦးစားပေးမည်ကို product/operations stakeholder နှင့် သဘောတူပါ။

## 5. အကြံပြုချက်

- CAP ကို database တစ်ခု၏ အမြဲတမ်းဂုဏ်သတ္တိအဖြစ်တံဆိပ်ကပ်ခြင်းထက် partition ဖြစ်ချိန်တွင် system က ဘာလုပ်မည်ကို ဆုံးဖြတ်သည့် framework အဖြစ်အသုံးပြုပါ။
- MiniERP ၏ transaction နှင့် stock/ငွေကြေးဆိုင်ရာလုပ်ငန်းများတွင် data integrity ကိုဦးစားပေးပြီး connection မရပါက မသေချာသော write ကိုအောင်မြင်သကဲ့သို့ မပြပါနှင့်။
- Eventual consistency ကိုရွေးချယ်သည့် feature များတွင် stale data ခွင့်ပြုနိုင်သည့်ကြာချိန်၊ user interface behavior နှင့် reconciliation နည်းလမ်းကိုတိတိကျကျဖော်ပြပါ။
- Architecture ပြောင်းလဲမှုမတိုင်မီ partition၊ timeout၊ node failure နှင့် recovery scenario များအတွက် test plan ရေးဆွဲပါ။
