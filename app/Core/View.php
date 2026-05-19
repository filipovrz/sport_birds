<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layouts.app'): string
    {
        extract($data);
        $config = require BASE_PATH . '/config/app.php';
        $user = Auth::user();

        ob_start();
        require BASE_PATH . '/resources/views/' . $template . '.php';
        $content = ob_get_clean();

        if ($layout === null) {
            return $content;
        }

        ob_start();
        require BASE_PATH . '/resources/views/' . $layout . '.php';
        return ob_get_clean();
    }

    public static function json(array $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
