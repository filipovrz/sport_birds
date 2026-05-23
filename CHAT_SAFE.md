# CHAT_SAFE — Best Sport Byrds

**Последно обновяване:** 2026-05-19  
**Версия:** 3.3.0  
**Git:** `main` @ sport_birds (filipovrz)

---

## Текущ фокус (готово)

✅ **Футър** — колона „Начини на плащане“ → `/payment-methods`  
✅ **Плащания 3.0** — без live ключове (bank работи)  
✅ **Фактури 3.1–3.2** — проформа + оригинал  
✅ **3.3** — EN switch (BG/EN), analytics dashboard, админ фактури, map fix

---

## Фактури (3.1.0 / 3.2.0)

### Поведение
| Метод | Проформа | Оригинална фактура |
|--------|----------|-------------------|
| Банков превод | Веднага при създаване на плащане | След `markPaid` (админ / потвърждение) |
| Онлайн (Stripe, ePay, …) | — | След успешно плащане |

### Номерация
- Проформа: `PRO-2026-000001` (`proforma_prefix` в settings, по подразбиране PRO)
- Фактура: `BSB-2026-000001` (`invoice_prefix`, по подразбиране BSB)
- Референция поръчка: `BSB-00000001` (ID на плащане)

### Файлове
- `database/phase4_2_invoices.sql` — таблица `invoices`, billing полета в `users`
- `database/phase4_3_proforma.sql` — `document_type` (proforma|invoice), `source_proforma_number`
- `app/Services/InvoiceService.php` — `issueProformaForPayment`, `issueForPayment`
- `app/Controllers/InvoiceController.php`
- `resources/views/invoices/*`
- `PaymentService::markPaid()` → издава само тип `invoice`
- `PaymentService::create()` + `PaymentController::bank()` → проформа при bank

### UI
- `/dashboard/invoices` — списък (тип, №, референция, описание)
- `/dashboard/invoices/{id}/print` — PDF
- Профил → **Фактури**; профил → **Данни за фактуриране**
- `/payment/bank/{token}` — линк проформа PDF
- Доставчик: админ → футър → фирмени данни

### Маршрути (dashboard)
- `GET /dashboard/invoices`
- `GET /dashboard/invoices/{id}`
- `GET /dashboard/invoices/{id}/print`

---

## Плащания 3.0.0

### Методи
- Банков превод — референция `BSB-XXXXXXXX`, админ одобрение
- Stripe, ePay.bg, PayPal, Revolut — автоматично fulfillment

### Ключови файлове
- `database/phase4_payments.sql`, `phase4_1_footer_cleanup.sql`
- `app/Services/PaymentService.php`, `PaymentFulfillmentService.php`, `Payment/Gateways/*`
- `docs/PAYMENTS.md` (настройка в системата), `docs/PAYMENT_PROVIDERS_CHECKLIST.md` (какво да вземете от доставчиците), `.env.example`

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

Приложение: `config/app.php` → `version` = **3.2.0**

---

## Docker

- App: http://localhost:8080  
- DB: MySQL `sport_birds` (compose)

---

## История (чат)

| Дата | Заявка | Резултат |
|------|--------|----------|
| 2026-05-19 | CHAT_SAFE + футър плащания | PaymentMethodsService, `/payment-methods` |
| 2026-05-19 | Всички методи + auto + bank + GitHub | v3.0.0 payments |
| 2026-05-19 | Футър като Imperia, реални checkout | footer column, gateways |
| 2026-05-19 | Фактури след плащане, Профил → Фактури | v3.1.0 invoices |
| 2026-05-19 | Проформа при банков превод, оригинал след плащане | v3.2.0 proforma |
| 2026-05-19 | Сейф навсякъде + GitHub | CHAT_SAFE, CHECKPOINT, VERSION, git push |
