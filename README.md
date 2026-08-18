# نظام إدارة الوساطة التأمينية — عبدالله الشباسي للتأمين

نظام CRM مخصص بالكامل لإدارة دورة عمل مكتب وساطة تأمينية: من دخول العميل كـ Lead، مرورًا بعرض السعر والتفاوض، إصدار الوثيقة، متابعة التجديد، وحتى تسجيل المطالبات وحساب العمولات والتحصيلات.

مبني كـ Custom CRM بالكامل (مش قالب معدّل)، بلوحة إدارة عربية RTL بالكامل.

---

## المحتويات

- [نظرة عامة على التقنيات](#نظرة-عامة-على-التقنيات)
- [الوحدات المبنية](#الوحدات-المبنية)
- [التشغيل محليًا](#التشغيل-محليًا)
- [بيانات الدخول الافتراضية](#بيانات-الدخول-الافتراضية)
- [الأدوار والصلاحيات](#الأدوار-والصلاحيات)
- [هيكل المشروع](#هيكل-المشروع)
- [المهام المجدولة (Scheduler)](#المهام-المجدولة-scheduler)
- [الاختبارات الآلية](#الاختبارات-الآلية)
- [النسخ الاحتياطي](#النسخ-الاحتياطي)
- [رفع المستندات (Storage)](#رفع-المستندات-storage)
- [حدود النظام الحالية](#حدود-النظام-الحالية)
- [خطوات النشر على سيرفر حقيقي](#خطوات-النشر-على-سيرفر-حقيقي)
- [سجل Git](#سجل-git)

---

## نظرة عامة على التقنيات

| الطبقة | التقنية |
|---|---|
| Framework | Laravel 13 (PHP 8.5) |
| لوحة الإدارة | [Filament 3.3](https://filamentphp.com/) (بدون واجهة React/SPA منفصلة — عمدًا، لتقليل الوقت والتكلفة) |
| قاعدة البيانات | MySQL 8 |
| الصلاحيات | [spatie/laravel-permission](https://spatie.be/docs/laravel-permission) + [bezhansalleh/filament-shield](https://github.com/bezhanSalleh/filament-shield) |
| سجل النشاط | [spatie/laravel-activitylog](https://spatie.be/docs/laravel-activitylog) |
| النسخ الاحتياطي | [spatie/laravel-backup](https://spatie.be/docs/laravel-backup) |
| تصدير PDF | [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) |
| استيراد/تصدير Excel | Filament Actions (native `ImportAction` / `ExportAction`) |
| الإشعارات | Filament Database Notifications |
| الاختبارات | PHPUnit (قاعدة بيانات MySQL مستقلة للاختبارات) |

---

## الوحدات المبنية

كل وحدة من الـ16 وحدة المتفق عليها في العرض الفني مبنية وشغالة، ماعدا وحدة واحدة محتاجة حساب خارجي:

| # | الوحدة | الحالة |
|---|---|---|
| 1 | إدارة العملاء (فرد/شركة، مستندات، سجل متابعات) | ✅ |
| 2 | إدارة وثائق التأمين + محرك عمولة تلقائي | ✅ |
| 3 | نظام التجديدات والتنبيهات (90/60/30/15/7 يوم) + إشعارات فعلية | ✅ |
| 4 | CRM ومسار المتابعة — لوحة Kanban تفاعلية | ✅ |
| 5 | عروض الأسعار (مع مقارنة عروض لنفس العميل) | ✅ |
| 6 | إدارة المطالبات | ✅ |
| 7 | العمولات والتحصيلات | ✅ |
| 8 | شركات التأمين وتقاريرها (شاملة تقرير أداء لكل شركة) | ✅ |
| 9 | المستخدمون والصلاحيات (5 أدوار، صلاحيات فعلية مختلفة لكل دور) | ✅ |
| 10 | لوحة تحكم الإدارة (إحصائيات + شارتس + اتجاهات) | ✅ |
| 11 | تكامل واتساب والبريد | ❌ **محتاج حساب WhatsApp Business API** |
| 12 | استيراد/تصدير Excel + نسخ احتياطي تلقائي يومي | ✅ |
| 13 | مركز التقارير (روابط سريعة + أداء شركات/موظفين + تصدير PDF) | ✅ |
| 14 | تصميم الواجهات (عربي RTL بالكامل، متجاوب) | ✅ |
| 15 | الأمان وسجل النشاط | ✅ |
| 16 | التدريب — [دليل الاستخدام](docs/دليل-الاستخدام.md) | ✅ |

---

## التشغيل محليًا

### المتطلبات
- PHP 8.2+ (تم التطوير والاختبار على 8.5.5)
- Composer
- MySQL 8
- بيئة محلية زي [Laragon](https://laragon.org/) (Windows) أو أي بيئة PHP+MySQL مشابهة

### الخطوات

```bash
composer install
cp .env.example .env
php artisan key:generate
```

عدّل `.env` وحط بيانات قاعدة البيانات بتاعتك (شوف `.env.example` للتفاصيل، فيه ملاحظة عن `DB_DUMP_BINARY_PATH` لو شغّال على Windows/Laragon).

```bash
php artisan migrate
php artisan storage:link
php artisan make:filament-user   # لو عايز تعمل مستخدم Admin جديد
php artisan serve
```

افتح `http://127.0.0.1:8000/admin` (رابط "/" بيحوّلك تلقائيًا للـ admin panel).

---

## بيانات الدخول الافتراضية

> ⚠️ ده حساب تجريبي اتعمل وقت التطوير — غيّر الباسورد أو امسحه قبل التسليم النهائي للعميل.

- **الإيميل:** `admin@insurance-crm.test`
- **الباسورد:** `Password123!`

---

## الأدوار والصلاحيات

النظام فيه 5 أدوار، كل واحد بصلاحيات مختلفة فعليًا (مش أسماء فاضية) — معرّفة في `database/seeders/RolePermissionSeeder.php`:

| الدور | نطاق الصلاحيات |
|---|---|
| **Admin** (`super_admin`) | كل شيء بدون استثناء — بيتجاوز كل الفحوصات |
| **Manager** | قراءة/إضافة/تعديل على كل الشاشات التشغيلية + التقارير + سجل النشاط + كل شارتس الداشبورد |
| **Sales** | العملاء، الوثائق، عروض الأسعار، المطالبات، لوحة Pipeline — بدون سجل النشاط أو مركز التقارير |
| **Customer Service** | العملاء والمطالبات بشكل أساسي، عرض فقط للوثائق والعروض |
| **Accounting** | رؤية مالية (وثائق، شركات تأمين، تقارير، سجل النشاط) بدون تعديل بيانات المبيعات اليومية |

لتحديث الصلاحيات بعد إضافة شاشة جديدة:
```bash
php artisan shield:generate --all --panel=admin
php artisan db:seed --class=RolePermissionSeeder
```

---

## هيكل المشروع

أهم الأماكن اللي هتحتاجها للتعديل مستقبلًا:

```
app/
├── Enums/                          # ClientType, PipelineStage, PolicyType, PolicyStatus, QuotationStatus, ClaimStatus
├── Models/                         # Client, Policy, Quotation, Claim, InsuranceCompany, Collection, FollowUp
├── Console/Commands/NotifyRenewals.php   # إشعارات التجديد اليومية
├── Filament/
│   ├── Resources/                  # شاشات CRUD: Client, Policy, Quotation, Claim, InsuranceCompany, ActivityLog
│   ├── Pages/                      # Dashboard (مخصص), Pipeline (Kanban), Reports
│   ├── Widgets/                    # كروت الإحصائيات + الشارتس
│   ├── Exports/ & Imports/         # منطق تصدير/استيراد Excel
│   └── Providers/Filament/AdminPanelProvider.php   # إعدادات لوحة الإدارة (البراند، الألوان، الويدجتس)
resources/views/
├── filament/pages/                 # واجهات Pipeline و Reports (Blade)
└── pdf/                            # قوالب تقارير PDF
database/
├── migrations/
├── factories/                      # لعمل بيانات تجريبية/اختبار
└── seeders/RolePermissionSeeder.php
docs/دليل-الاستخدام.md              # دليل استخدام الفريق
tests/                              # 23 اختبار آلي
```

---

## المهام المجدولة (Scheduler)

معرّفة في `routes/console.php`:

| الأمر | التوقيت | الغرض |
|---|---|---|
| `backup:run` | يوميًا 02:00 | نسخة احتياطية من قاعدة البيانات والمستندات |
| `backup:clean` | يوميًا 02:30 | حذف النسخ القديمة حسب سياسة `config/backup.php` |
| `backup:monitor` | شهريًا يوم 1، 03:00 | تنبيه لو النسخ الاحتياطي توقف |
| `renewals:notify` | يوميًا 08:00 | إشعارات التجديد للموظف المسؤول والإدارة |

> ⚠️ **مهم:** تعريف الجدول في `routes/console.php` وحده مش كفاية — لازم تشغّل Laravel Scheduler فعليًا على السيرفر عن طريق Cron Job واحد كل دقيقة:
> ```
> * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
> ```
> على استضافة cPanel: من قسم "Cron Jobs" ضيف السطر ده بنفس الطريقة.

يمكن تشغيل أي أمر يدويًا للتجربة: `php artisan backup:run` أو `php artisan renewals:notify`.

---

## الاختبارات الآلية

23 اختبار (33 Assertion) بتغطي: حدود تصنيفات التجديد الزمنية، حساب العمولة (شامل حالة القسط صفر والتحديث)، حساب التحصيل والمتبقي، سلوك الحذف الناعم/النهائي والـ Cascade، ومنع تكرار إشعارات التجديد.

الاختبارات بتشتغل على قاعدة بيانات MySQL منفصلة (`insurance_crm_test`) بدل SQLite، لأن نسخة PHP المستخدمة (8.5.5) معندهاش `pdo_sqlite` مفعّل، وتفعيله كان محتاج تعديل `php.ini` على مستوى الجهاز كله بدل المشروع، فاتجنّب الحل ده.

```bash
# لأول مرة، لازم تعمل قاعدة بيانات الاختبار:
mysql -u root -e "CREATE DATABASE insurance_crm_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan test
```

---

## النسخ الاحتياطي

يشتغل تلقائيًا حسب [الجدول أعلاه](#المهام-المجدولة-scheduler)، ومُعدّ فعليًا وجرّب بنجاح (نسخة كاملة تقريبًا 2MB لقاعدة البيانات + المستندات).

على Windows/Laragon، `mysqldump` مش موجود في الـ PATH الافتراضي، فمحتاج `DB_DUMP_BINARY_PATH` في `.env` يشاور على فولدر `mysql.exe`/`mysqldump.exe` (موضّح في `.env.example`). على سيرفر Linux عادي غالبًا مش هتحتاجها خالص.

تشغيل يدوي: `php artisan backup:run`

---

## رفع المستندات (Storage)

كل شاشات رفع المستندات (عميل، وثيقة، عرض سعر، مطالبة) بتستخدم الـ`public` disk بشكل افتراضي من Filament، بغض النظر عن `FILESYSTEM_DISK` في `.env`. عشان الملفات المرفوعة تبقى قابلة للعرض من المتصفح، لازم الأمر ده يتنفّذ **مرة واحدة بعد كل نشر جديد على سيرفر جديد**:

```bash
php artisan storage:link
```

(اتعمل بالفعل في بيئة التطوير الحالية.)

---

## حدود النظام الحالية

- **تكامل واتساب والبريد (وحدة 11):** الكود جاهز للربط، بس محتاج حساب WhatsApp Business Cloud API فعلي من Meta + بيانات اعتماد SMTP حقيقية.
- **النشر:** النظام شغال محليًا (Laragon) فقط لحد دلوقتي — محتاج استضافة (VPS) ودومين للنشر الفعلي.
- **بيانات تجريبية:** فيه عميل ووثيقة وشركتين تأمين تجريبيين اتعملوا وقت الاختبار — احذفهم من الواجهة قبل إدخال بيانات حقيقية.
- **حساب الـ Admin الافتراضي:** غيّر الباسورد بعد أول دخول فعلي.

---

## خطوات النشر على سيرفر حقيقي

قائمة سريعة، مش شرح مفصّل:

1. `composer install --no-dev --optimize-autoloader`
2. اعمل `.env` حقيقي (قاعدة بيانات production، `APP_ENV=production`, `APP_DEBUG=false`, `QUEUE_CONNECTION` مناسب)
3. `php artisan migrate --force`
4. `php artisan storage:link`
5. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
6. عيّن Cron Job واحد لـ `php artisan schedule:run` (شوف [المهام المجدولة](#المهام-المجدولة-scheduler))
7. فعّل SSL (AutoSSL على cPanel مجاني)
8. اعمل مستخدم Admin حقيقي بدل التجريبي، واحذف بيانات الاختبار

---

## سجل Git

المشروع متتبّع بالكامل بـ Git من أول commit، بتاريخ commits واضح لكل مرحلة تطوير (الأساسيات، الوحدات التجارية، الداشبورد، الاختبارات...). راجع `git log` للتفاصيل الكاملة.
