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

        $templatePath = BASE_PATH . '/resources/views/' . str_replace('.', '/', $template) . '.php';
        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        if ($layout === null) {
            return $content;
        }

        $layoutPath = BASE_PATH . '/resources/views/' . str_replace('.', '/', $layout) . '.php';
        ob_start();
        require $layoutPath;
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
