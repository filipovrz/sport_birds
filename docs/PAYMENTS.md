# Плащания — настройка в системата

> **Какво да поискате от доставчиците (ключове, webhook, IBAN):**  
> вижте **[PAYMENT_PROVIDERS_CHECKLIST.md](PAYMENT_PROVIDERS_CHECKLIST.md)**

## Поток в приложението

1. Потребителят избира услуга (абонамент / обява) и метод.
2. Създава се запис в `payments` + връзка към заявката.
3. **Банка** → `/payment/bank/{token}` (реквизити + референция BSB-…).
4. **Онлайн** → `/payment/go/{token}` → API на доставчика → return/webhook → автоматично активиране.

От футъра: **Начини на плащане** → метод → **Продължи** → `/payment/checkout/{slug}`.

---

## Stripe (карта)

| Параметър | `.env` / Админ настройки |
|-----------|---------------------------|
| Secret key | `STRIPE_SECRET_KEY` |
| Webhook secret | `STRIPE_WEBHOOK_SECRET` |

**Webhook URL:** `https://ВАШИЯТ-ДОМЕЙН/webhooks/stripe`  
**Събития:** `checkout.session.completed`

Документация: https://stripe.com/docs/api/checkout/sessions

---

## ePay.bg

| Параметър | `.env` / Админ |
|-----------|----------------|
| MIN (код търговец) | `EPAY_MIN` |
| Secret | `EPAY_SECRET` |
| URL | `EPAY_URL` (по подразбиране https://www.epay.bg/) |

**Webhook URL:** `https://ВАШИЯТ-ДОМЕЙН/webhooks/epay` (сървърен notify)  
**Return:** потребителят се връща на `/payment/return/{token}`

Документация: https://www.epay.bg/en/developers

---

## PayPal

| Параметър | `.env` / Админ |
|-----------|----------------|
| Client ID | `PAYPAL_CLIENT_ID` |
| Secret | `PAYPAL_SECRET` |
| Mode | `PAYPAL_MODE` = `sandbox` или `live` |

**Webhook URL:** `https://ВАШИЯТ-ДОМЕЙН/webhooks/paypal`  
**Събития:** `CHECKOUT.ORDER.APPROVED`, `PAYMENT.CAPTURE.COMPLETED`

Документация: https://developer.paypal.com/docs/api/orders/v2/

---

## Revolut Merchant

| Параметър | `.env` / Админ |
|-----------|----------------|
| API Secret | `REVOLUT_API_SECRET` |
| Mode | `REVOLUT_MODE` = `sandbox` или `live` |

**Webhook URL:** `https://ВАШИЯТ-ДОМЕЙН/webhooks/revolut`  
**Събитие:** `ORDER_COMPLETED`

Документация: https://developer.revolut.com/docs/merchant/create-order

---

## Банков превод

Само **IBAN и текст** в Админ → Настройки → Банкови реквизити.  
Ръчно одобрение в Админ → Абонаменти / Плащания.

---

## Проверка

След попълване на ключовете: Абонамент → избор на план → метод → трябва да пренасочи към Stripe/ePay/PayPal/Revolut или екрана за банка.
