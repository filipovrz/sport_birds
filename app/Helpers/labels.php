<?php

function species_label(string $key): string
{
    return match ($key) {
        'racing_pigeon' => 'Спортен гълъб',
        'sport_pigeon' => 'Друг спортен гълъб',
        'gamecock' => 'Бойна петле',
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
