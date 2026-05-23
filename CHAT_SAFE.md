# CHAT_SAFE — Best Sport Byrds

**Последно обновяване:** 2026-05-23  
**Версия:** 3.3.0  
**Git:** `main` @ https://github.com/filipovrz/sport_birds · последен commit `fbe8d65`

---

## Текущ фокус (готово в кода)

✅ **Футър** — колона „Начини на плащане“ → `/payment-methods`  
✅ **Плащания 3.0** — infrastructure готова; **без live API ключове** (работи **банков превод**)  
✅ **Фактури 3.1–3.2** — проформа при банка + оригинал след плащане  
✅ **3.3.0** — EN switch, analytics dashboard, админ фактури, map fix, backfill проформи  
✅ **Документация** — `docs/PAYMENTS.md`, `docs/PAYMENT_PROVIDERS_CHECKLIST.md`

### Остава (извън код / по избор)
- API ключове: Stripe, ePay.bg, PayPal, Revolut → `docs/PAYMENT_PROVIDERS_CHECKLIST.md`
- Production HTTPS + webhook URL-и при доставчиците
- Пълен EN превод на всички views (v1 = header, меню, dashboard)

---

## Фаза 3.3.0 — EN + analytics + админ фактури

### EN език
- `app/Services/LocaleService.php` — `bg` / `en`, session
- `app/Helpers/i18n.php` — `__()`, `locale()`
- `resources/lang/bg.php`, `resources/lang/en.php`
- `GET /locale/{lang}?return=/path` — `LocaleController@switch`
- Преключвател **BG / EN** в header (`resources/views/layouts/_lang_switcher.php`)
- Преведени: app/guest layout, sidebar nav, потребителско табло (`dashboard/index.php`)
- Admin, legal, print views — **все още BG**

### Analytics dashboard
- `app/Services/AnalyticsService.php`
- **Потребител** `/dashboard`: просрочени прегледи, тренировки 30д, състезания YTD, развъдни двойки, последни резултати, CSV блок (ако `ExportService::canExport()`)
- **Админ** `/admin`: чакащи плащания (състезания/събития), платени 30д, приходи EUR 30д, брой фактури/проформи

### Админ фактури
- `app/Controllers/Admin/InvoiceController.php`
- `/admin/invoices` — списък; `/admin/invoices/{id}` — преглед; `/print` — PDF
- Sidebar: **Абонаменти** → **Фактури** (permission `subscriptions`)
- Backfill: `InvoiceService::backfillMissingBankProformas()` — веднъж при bootstrap (flag `invoice_proforma_backfill_done`)

### Map fix
- `MapController` — заявка към **`event_announcements`** (не `events`; таблицата не съществува)
- Commit в `fbe8d65`; Docker понякога спира → `docker compose up -d`

---

## Фактури (3.1.0 / 3.2.0)

### Поведение
| Метод | Проформа | Оригинална фактура |
|--------|----------|-------------------|
| Банков превод | Веднага при създаване на плащане | След `markPaid` (админ / потвърждение) |
| Онлайн (Stripe, ePay, …) | — | След успешно плащане |

### Номерация
- Проформа: `PRO-2026-000001` (`proforma_prefix`, default PRO)
- Фактура: `BSB-2026-000001` (`invoice_prefix`, default BSB)
- Референция поръчка: `BSB-00000001` (payment ID)

### Файлове
- `database/phase4_2_invoices.sql` — `invoices`, billing в `users`
- `database/phase4_3_proforma.sql` — `document_type`, `source_proforma_number`
- `app/Services/InvoiceService.php`
- `app/Controllers/InvoiceController.php` (потребител)
- `resources/views/invoices/*`
- `PaymentService::markPaid()` → invoice; `create()` + bank page → proforma

### UI (потребител)
- **Профил → Фактури** — `/dashboard/invoices`
- Профил → **Данни за фактуриране** (EIK, ДДС, адрес)
- `/payment/bank/{token}` — линк проформа PDF
- `/payment/status/{token}` — проформа (pending) / фактура (paid)
- Доставчик: админ → футър → фирмени данни

---

## Плащания 3.0.0

### Методи (код готов, ключове — не)
- **Банка** — референция `BSB-XXXXXXXX`, админ одобрение, проформа + фактура
- **Stripe, ePay, PayPal, Revolut** — gateways + webhooks; inactive без ключове

### Документация
| Файл | Съдържание |
|------|------------|
| `docs/PAYMENT_PROVIDERS_CHECKLIST.md` | **Какво да поискате** от всеки доставчик (ключове, webhook URL) |
| `docs/PAYMENTS.md` | Настройка в системата след получаване на ключове |
| `.env.example` | Имена на env променливи |

### Маршрути
- `/payment/bank|go|return|cancel|status/{token}`
- `/webhooks/stripe|epay|paypal|revolut`
- `/payment-methods`, `/payment-methods/{slug}`

---

## Миграции (Migrator)

| Версия | SQL |
|--------|-----|
| 3.0.0 | phase4_payments.sql |
| 3.0.1 | phase4_1_footer_cleanup.sql |
| 3.1.0 | phase4_2_invoices.sql |
| 3.2.0 | phase4_3_proforma.sql |
| 3.3.0 | phase4_4_finish.sql (version bump) |

`config/app.php` → **3.3.0**

---

## Docker / локално

```bash
docker compose up -d
```

| URL | Описание |
|-----|----------|
| http://localhost:8080/ | Начало |
| http://localhost:8080/dashboard | Табло + analytics |
| http://localhost:8080/dashboard/map | Карта (изисква платен план) |
| http://localhost:8080/dashboard/invoices | Фактури (вход) |
| http://localhost:8080/locale/en?return=/dashboard | English shell |
| http://localhost:8080/admin/invoices | Фактури (админ) |
| http://localhost:8080/payment-methods | Начини на плащане |

**Забележка:** `ERR_CONNECTION_REFUSED` = Docker спрял → `docker compose up -d`.

`.gitignore` — локална папка `Проби/` (тестови PDF) не е в git.

---

## Git история (важни commits)

| Commit | Описание |
|--------|----------|
| `77019eb` | v3.0.0 payments |
| `e7709ef` | v3.2.0 invoices + proforma |
| `28ab110` | PAYMENT_PROVIDERS_CHECKLIST.md |
| `fbe8d65` | v3.3.0 EN + analytics + admin invoices + map fix |

---

## История (чат)

| Дата | Заявка | Резултат |
|------|--------|----------|
| 2026-05-19 | CHAT_SAFE + футър плащания | `/payment-methods`, PaymentMethodsService |
| 2026-05-19 | Всички методи + auto + bank + GitHub | v3.0.0 |
| 2026-05-19 | Футър Imperia, реални checkout | gateways, footer column |
| 2026-05-19 | Фактури след плащане | v3.1.0, Профил → Фактури |
| 2026-05-19 | Проформа при банка, оригинал след плащане | v3.2.0 |
| 2026-05-19 | Сейф + GitHub | CHAT_SAFE, CHECKPOINT, VERSION |
| 2026-05-19 | Чеклист за доставчици | `docs/PAYMENT_PROVIDERS_CHECKLIST.md`, commit `28ab110` |
| 2026-05-23 | Map грешка `events` + Docker down | fix `event_announcements`, restart compose |
| 2026-05-23 | Довърши всичко без API ключове | v3.3.0: EN, analytics, admin invoices, backfill |
| 2026-05-23 | Пълен сейф последния чат | актуализация CHAT_SAFE (този файл) |

---

## Следваща стъпка (когато има ключове)

1. Попълни `docs/PAYMENT_PROVIDERS_CHECKLIST.md` → ключове в **Админ → Настройки** или `.env`
2. Регистрирай webhook URL-и при доставчиците
3. Тест: абонамент → метод → checkout / банка
4. Production: HTTPS домейн, фирмени данни във футъра за фактури
