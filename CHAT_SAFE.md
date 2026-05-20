# CHAT_SAFE — Best Sport Byrds

**Последно обновяване:** 2026-05-19  
**Версия:** 3.0.1

---

## Текущ фокус

✅ **Футър** — без списък/badge; само линк „Начини на плащане“ → `/payment-methods`  
✅ **Фаза 3.0 — онлайн плащания**

---

## Плащания 3.0.0 — какво е направено

### Методи
- **Банков превод** — референция `BSB-XXXXXXXX`, админ одобрение
- **Stripe** (карта) — Checkout Session + webhook
- **ePay.bg** — redirect форма + webhook
- **PayPal** — Checkout + capture + webhook
- **Revolut Merchant** — поръчка + webhook

### Файлове
- `database/phase4_payments.sql` — таблица `payments`
- `app/Services/PaymentService.php`, `PaymentFulfillmentService.php`, `CheckoutFlowService.php`
- `app/Services/Payment/Gateways/*`
- `app/Controllers/PaymentController.php`, `WebhookController.php`
- `docs/PAYMENTS.md`, `.env.example`

### Маршрути
- `/payment/bank|go|return|cancel|status/{token}`
- `/webhooks/stripe|epay|paypal|revolut`

### Конфигурация
- `.env` или **Админ → Настройки → Онлайн плащания**
- Webhook URL-и в админ панела

### Потоци
- Абонамент, обяви състезания, обяви събития — избор на метод → checkout

---

## Предишно (2.2.0)

- CHAT_SAFE, структуриран футър (`PaymentMethodsService`)
- Фази 1–3: GPS, обяви, правни, CSV, cron

---

## Docker

- App: http://localhost:8080  
- Миграция 3.0.0 при следващо зареждане с `.env`

---

## История

| Дата | Заявка | Резултат |
|------|--------|----------|
| 2026-05-19 | CHAT_SAFE + футър плащания | PaymentMethodsService |
| 2026-05-19 | Всички методи + auto + bank + GitHub | v3.0.0 payments |
