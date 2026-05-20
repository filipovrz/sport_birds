<?php

function csrf_field(): string
{
    return \App\Core\Csrf::field();
}

function species_label(string $key): string
{
    return match ($key) {
        'racing_pigeon' => 'Спортен гълъб',
        'sport_pigeon' => 'Други гълъби',
        'other' => 'Друга спортна птица',
        default => $key,
    };
}

function sex_label(string $key): string
{
    return match ($key) {
        'male' => 'Мъжки',
        'female' => 'Женски',
        'unknown' => 'Неизвестен',
        default => $key,
    };
}

function health_record_type_label(string $key): string
{
    return match ($key) {
        'vaccination' => 'Ваксинация',
        'treatment' => 'Лечение',
        'illness' => 'Заболяване',
        'parasite' => 'Паразити',
        'checkup' => 'Преглед',
        'other' => 'Друго',
        default => $key,
    };
}

function status_label(string $key): string
{
    return match ($key) {
        'active' => 'Активен',
        'sold' => 'Продаден',
        'deceased' => 'Починал',
        'retired' => 'В пенсия',
        'breeding' => 'Развъден',
        default => $key,
    };
}

function role_label(string $key): string
{
    return match ($key) {
        'user' => 'Потребител',
        'admin' => 'Администратор',
        'super_admin' => 'Супер админ',
        default => $key,
    };
}

/** @param array<string, mixed> $plan */
function plan_price_eur(array $plan): float
{
    return (float) ($plan['price_eur'] ?? $plan['price_bgn'] ?? 0);
}

function format_eur(float $amount): string
{
    if ($amount <= 0) {
        return 'Безплатно';
    }

    return number_format($amount, 2, ',', ' ') . ' €';
}

/** @param array<string, mixed> $plan */
function format_plan_price(array $plan): string
{
    return format_eur(plan_price_eur($plan));
}

/** @param array<string, mixed> $plan */
function is_free_plan(array $plan): bool
{
    return ($plan['slug'] ?? '') === 'free' || plan_price_eur($plan) <= 0;
}

/** @param array<string, mixed> $plan */
function format_plan_period(array $plan): string
{
    if (is_free_plan($plan)) {
        return 'Безплатен';
    }

    $days = (int) ($plan['duration_days'] ?? 30);
    if ($days >= 28 && $days <= 31) {
        return 'месечен абонамент';
    }
    if ($days >= 365) {
        return 'годишен абонамент';
    }

    return $days . ' дни';
}

/** @param array<string, mixed> $plan */
function subscription_request_status_label(string $status): string
{
    return match ($status) {
        'pending' => 'Чака одобрение',
        'approved' => 'Одобрена',
        'rejected' => 'Отхвърлена',
        default => $status,
    };
}

function subscription_request_status_html(string $status): string
{
    $label = subscription_request_status_label($status);
    $class = match ($status) {
        'approved' => 'status-dot status-dot--approved',
        'rejected' => 'status-dot status-dot--rejected',
        'pending' => 'status-dot status-dot--pending',
        default => 'status-dot',
    };

    return '<span class="status-with-dot"><span class="' . $class . '" aria-hidden="true"></span>'
        . htmlspecialchars($label) . '</span>';
}

function announcement_payment_status_label(string $status): string
{
    return match ($status) {
        'not_required' => 'Без такса',
        'pending' => 'Чака плащане',
        'approved' => 'Платено',
        'rejected' => 'Отхвърлено',
        default => $status,
    };
}

function announcement_status_label(string $status): string
{
    return match ($status) {
        'draft' => 'Чернова / чака одобрение',
        'published' => 'Публикувана',
        'cancelled' => 'Отменена',
        'completed' => 'Приключена',
        default => $status,
    };
}

function competition_type_label(string $type): string
{
    return match ($type) {
        'race' => 'Гонка',
        'show' => 'Изложба',
        'fight' => 'Бой',
        'training_race' => 'Тренировъчна гонка',
        'other' => 'Друго',
        default => $type,
    };
}

/** Типове за избор в формуляри (без „бой“). */
function competition_type_options(): array
{
    return ['race', 'show', 'training_race', 'other'];
}

function competition_species_label(string $species): string
{
    return match ($species) {
        'racing_pigeon' => 'Състезателен гълъб',
        'sport_pigeon' => 'Спортен гълъб',
        'gamecock' => 'Петел',
        'other' => 'Друго',
        default => $species,
    };
}

function user_type_labels(string $csv): string
{
    $map = [
        'owner' => 'Собственик',
        'competitor' => 'Състезател',
        'breeder' => 'Развъдчик',
    ];
    $parts = array_filter(array_map('trim', explode(',', $csv)));
    if ($parts === []) {
        return '—';
    }
    return implode(', ', array_map(fn ($p) => $map[$p] ?? $p, $parts));
}

function bird_specialty_labels(string $csv): string
{
    $map = [
        'racing_pigeon' => 'Спортни гълъби',
        'sport_pigeon' => 'Други гълъби',
        'gamecock' => 'Петели',
        'other_sport_bird' => 'Други спортни птици',
    ];
    $parts = array_filter(array_map('trim', explode(',', $csv)));
    if ($parts === []) {
        return '—';
    }
    return implode(', ', array_map(fn ($p) => $map[$p] ?? $p, $parts));
}

function event_type_label(string $type): string
{
    return match ($type) {
        'gathering' => 'Сбор',
        'assembly' => 'Събор',
        'meeting' => 'Среща',
        'exhibition' => 'Изложба',
        'social' => 'Социално събитие',
        'other' => 'Друго',
        default => $type,
    };
}

/** @param array<string, mixed> $plan */
function format_plan_price_suffix(array $plan): string
{
    if (is_free_plan($plan)) {
        return '';
    }

    $days = (int) ($plan['duration_days'] ?? 30);
    if ($days >= 28 && $days <= 31) {
        return '/ месец';
    }
    if ($days >= 365) {
        return '/ година';
    }

    return '';
}

function invoice_document_type_label(string $type): string
{
    return match ($type) {
        'proforma' => 'Проформа',
        'invoice' => 'Фактура',
        default => $type,
    };
}

function payment_icon_text(string $icon): string
{
    return match ($icon) {
        'bank' => 'BANK',
        'card' => 'VISA',
        'epay' => 'ePay',
        'paypal' => 'PayPal',
        'revolut' => 'Revolut',
        default => '€',
    };
}
