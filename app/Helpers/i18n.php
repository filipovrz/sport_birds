<?php

declare(strict_types=1);

use App\Services\LocaleService;

/** @param array<string, string|int|float> $replace */
function __(string $key, array $replace = []): string
{
    return LocaleService::translate($key, $replace);
}

function locale(): string
{
    return LocaleService::locale();
}
