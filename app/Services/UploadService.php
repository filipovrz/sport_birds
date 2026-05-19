<?php

declare(strict_types=1);

namespace App\Services;

final class UploadService
{
    private const MAX_BYTES = 5_242_880; // 5 MB
    private const ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public static function storeBirdPhoto(array $file, int $userId): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Грешка при качване на файл.');
        }
        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new \RuntimeException('Файлът е твърде голям (макс. 5 MB).');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED, true)) {
            throw new \RuntimeException('Позволени са само JPG, PNG, WebP и GIF.');
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };

        $dir = BASE_PATH . '/public/uploads/birds/' . $userId;
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            throw new \RuntimeException('Не може да се създаде директория за качване.');
        }

        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new \RuntimeException('Неуспешно запазване на файла.');
        }

        return '/uploads/birds/' . $userId . '/' . $name;
    }

    public static function delete(?string $relativePath): void
    {
        if (!$relativePath || str_contains($relativePath, '..')) {
            return;
        }
        $path = ltrim(str_replace('/uploads/', '', $relativePath), '/');
        $full = BASE_PATH . '/public/uploads/' . $path;
        if (is_file($full)) {
            unlink($full);
        }
    }
}
