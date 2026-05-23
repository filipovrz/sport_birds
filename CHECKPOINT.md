# Best Sport Byrds — Checkpoint

**Версия:** 3.3.0 · **Фаза:** 3.3 · **Следваща:** 4.0 (production keys, full EN)  
**Статус:** Feature-complete без API ключове от payment providers

---

## Фаза 3.3 — EN + analytics + админ фактури ✅

- [x] **EN език** — `LocaleService`, `resources/lang/{bg,en}.php`, преключвател BG/EN в header
- [x] **Разширен analytics dashboard** — потребителско табло + админ обзор (приходи, плащания, фактури)
- [x] **Админ → Фактури** — списък, преглед, печат
- [x] Backfill проформи за стари банкови плащания
- [x] Map fix (`event_announcements`)
- [x] `docs/PAYMENT_PROVIDERS_CHECKLIST.md`

## Остава (извън код / по choice)

- [ ] API ключове Stripe, ePay, PayPal, Revolut
- [ ] Production HTTPS + webhook URL-и
- [ ] Пълен превод на всички ~105 views (EN v1 = shell + dashboard)

---

## Бърз тест

```bash
docker compose up -d
```

| URL | Описание |
|-----|----------|
| http://localhost:8080/dashboard | Статистика + analytics |
| http://localhost:8080/locale/en?return=/dashboard | English UI (shell) |
| http://localhost:8080/admin/invoices | Фактури (админ) |

Вижте `VERSION.md`, `CHAT_SAFE.md`.
