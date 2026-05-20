# Плащания (v3.0.0)

## Методи

| Метод | Slug | Автоматично | Конфигурация |
|-------|------|-------------|--------------|
| Банков превод | `bank` | Не (админ одобрява) | IBAN в Настройки |
| Карта (Stripe) | `stripe` | Да | `STRIPE_SECRET_KEY`, webhook `/webhooks/stripe` |
| ePay.bg | `epay` | Да | `EPAY_MIN`, `EPAY_SECRET`, `/webhooks/epay` |
| PayPal | `paypal` | Да | `PAYPAL_CLIENT_ID`, `PAYPAL_SECRET`, `/webhooks/paypal` |
| Revolut Pay | `revolut` | Да | `REVOLUT_API_SECRET`, `/webhooks/revolut` |

Секретите могат да се зададат в `.env` или в **Админ → Настройки → Онлайн плащания**.

## Поток

1. Потребителят избира план/обява и **начин на плащане**.
2. Създава се запис в `payments` + свързан `subscription_request` / обява.
3. **Банка:** `/payment/bank/{token}` — референция `BSB-00000001`.
4. **Онлайн:** `/payment/go/{token}` → Stripe / ePay / PayPal / Revolut.
5. При успех: webhook или return URL → `PaymentFulfillmentService` активира услугата.

## Webhooks (без CSRF)

- `POST /webhooks/stripe`
- `POST /webhooks/epay`
- `POST /webhooks/paypal`
- `POST /webhooks/revolut`

## Миграция

При старт с `.env` се изпълнява `database/phase4_payments.sql` (версия 3.0.0).
