# Какво да вземете от доставчиците на плащания

Чеклист за договаряне и активиране на всеки метод в **Best Sport Byrds**.  
След като получите данните → **Админ → Настройки → Онлайн плащания** или `.env` (виж `.env.example`).

Техническа настройка в системата: [PAYMENTS.md](PAYMENTS.md)

**Заменете `ВАШИЯТ-ДОМЕЙН`** с реалния адрес (напр. `https://sportbirds.example.com`). За локални тестове: `http://localhost:8080` (webhook-ите изискват публичен HTTPS — използвайте ngrok или staging).

---

## Обобщена таблица

| Метод | Какво да поискате от доставчика | Къде в системата | Webhook (да им дадете) |
|--------|--------------------------------|------------------|-------------------------|
| **Stripe** | Secret key, Webhook signing secret | `STRIPE_SECRET_KEY`, `STRIPE_WEBHOOK_SECRET` | `https://ВАШИЯТ-ДОМЕЙН/webhooks/stripe` |
| **ePay.bg** | MIN (код търговец), Secret | `EPAY_MIN`, `EPAY_SECRET` | `https://ВАШИЯТ-ДОМЕЙН/webhooks/epay` |
| **PayPal** | Client ID, Secret, sandbox/live | `PAYPAL_CLIENT_ID`, `PAYPAL_SECRET`, `PAYPAL_MODE` | `https://ВАШИЯТ-ДОМЕЙН/webhooks/paypal` |
| **Revolut** | API Secret (sandbox/live) | `REVOLUT_API_SECRET`, `REVOLUT_MODE` | `https://ВАШИЯТ-ДОМЕЙН/webhooks/revolut` |
| **Банка** | IBAN, име на получател, банка (без API) | Админ → Настройки → Банкови реквизити | — |

---

## Stripe (Visa / Mastercard)

### Поискайте от Stripe
| Данни | Описание |
|--------|----------|
| **Secret key** | `sk_live_…` за продукция (или `sk_test_…` за тест) |
| **Webhook signing secret** | След създаване на endpoint в Stripe Dashboard → `whsec_…` |
| Merchant акаунт | Регистрация на фирмата (ЕИК, банкова сметка, представител) |

### Дайте на Stripe (в Dashboard)
| Настройка | Стойност |
|-----------|----------|
| Webhook URL | `https://ВАШИЯТ-ДОМЕЙН/webhooks/stripe` |
| Събития (events) | `checkout.session.completed` |
| Success URL | автоматично от приложението (`/payment/return/{token}`) |

### В системата
```
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_ENABLED=true
```

Документация: https://stripe.com/docs/api/checkout/sessions

---

## ePay.bg

### Поискайте от ePay
| Данни | Описание |
|--------|----------|
| **MIN** | Код на търговец (идентификатор) |
| **Secret** | Таен ключ за подпис (ENCODED/CHECKSUM) |
| Договор / активиране | Търговски акаунт за вашата фирма |
| Тестова среда (ако има) | MIN/Secret за тест |

### Дайте на ePay
| Настройка | Стойност |
|-----------|----------|
| URL за известие (notify/webhook) | `https://ВАШИЯТ-ДОМЕЙН/webhooks/epay` |
| URL за връщане на клиента | `https://ВАШИЯТ-ДОМЕЙН/payment/return/{token}` (управлява се от приложението) |

### В системата
```
EPAY_MIN=...
EPAY_SECRET=...
EPAY_URL=https://www.epay.bg/
EPAY_ENABLED=true
```

Документация: https://www.epay.bg/en/developers

---

## PayPal

### Поискайте от PayPal
| Данни | Описание |
|--------|----------|
| **Client ID** | От PayPal Developer / Business акаунт |
| **Secret** | Client secret (пазете като парола) |
| **Режим** | `sandbox` за тест, `live` за продукция |
| Business акаунт | Верифициран акаунт, свързан с фирмата |

### Дайте на PayPal
| Настройка | Стойност |
|-----------|----------|
| Webhook URL | `https://ВАШИЯТ-ДОМЕЙН/webhooks/paypal` |
| Събития | `CHECKOUT.ORDER.APPROVED`, `PAYMENT.CAPTURE.COMPLETED` |
| Return URL | автоматично (`/payment/return/{token}`) |

### В системата
```
PAYPAL_CLIENT_ID=...
PAYPAL_SECRET=...
PAYPAL_MODE=live
PAYPAL_ENABLED=true
```

Документация: https://developer.paypal.com/docs/api/orders/v2/

---

## Revolut Merchant

### Поискайте от Revolut
| Данни | Описание |
|--------|----------|
| **API Secret** | Secret key за Merchant API |
| **Режим** | `sandbox` или `live` |
| Merchant onboarding | Регистрация на бизнес в Revolut Merchant |

### Дайте на Revolut
| Настройка | Стойност |
|-----------|----------|
| Webhook URL | `https://ВАШИЯТ-ДОМЕЙН/webhooks/revolut` |
| Събитие | `ORDER_COMPLETED` |

### В системата
```
REVOLUT_API_SECRET=...
REVOLUT_MODE=live
REVOLUT_ENABLED=true
```

Документация: https://developer.revolut.com/docs/merchant/create-order

---

## Банков превод (без API)

### Поискайте / подгответе сами
| Данни | Описание |
|--------|----------|
| **IBAN** | Сметка на фирмата |
| **Име на получател** | Юридическо име |
| **BIC / банка** | По избор в текста с инструкции |
| Текст с инструкции | Какво да посочи клиентът (референцията се генерира автоматично: `BSB-00000001`) |

### В системата
- **Админ → Настройки → Банкови реквизити** (поле с инструкции)
- **Админ → Футър → Фирмени данни** — за фактури (ЕИК, ДДС №, адрес)
- Няма webhook; админ **одобрява** плащането след превод → издава се оригинална фактура (проформата е веднага)

---

## Какво да подготвите преди разговор с доставчик

Общо за всички онлайн методи:

- **Домейн с HTTPS** (валиден SSL сертификат)
- **Юридическо лице** — ЕИК, адрес, представител
- **Банкова сметка** за изплащания от доставчика
- **Описание на дейността** (абонаменти и такси за публикуване на обяви)
- **Валута** — приложението работи в **EUR** (BGN се показва по курс `PAYMENT_EUR_BGN_RATE`)

---

## След получаване на ключовете

1. Попълнете **Админ → Настройки → Онлайн плащания** (или `.env`).
2. Включете съответния метод (`*_ENABLED=true`).
3. Регистрирайте webhook URL при доставчика.
4. Тест: **Абонамент** → план → избор на метод → пренасочване към checkout или банков екран.
5. На страницата **Начини на плащане** (`/payment-methods`) методът трябва да е активен.

---

## Свързани файлове

| Файл | Съдържание |
|------|------------|
| [PAYMENTS.md](PAYMENTS.md) | Поток в приложението, технически детайли |
| `/.env.example` | Имена на променливи |
| `CHAT_SAFE.md` | Версия и обобщение на фазите |
