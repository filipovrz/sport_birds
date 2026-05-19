<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $template, array $data = [], ?string $layout = 'layouts.app'): never
    {
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
        }
        echo View::render($template, $data, $layout);
        exit;
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    protected function back(): never
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($ref);
    }

    /** @return array<string, mixed> */
    protected function validate(array $rules): array
    {
        $data = [];
        $errors = [];
        foreach ($rules as $field => $rule) {
            $value = trim($_POST[$field] ?? '');
            if (str_contains($rule, 'required') && $value === '') {
                $errors[$field] = 'Полето е задължително.';
                continue;
            }
            if (str_contains($rule, 'email') && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = 'Невалиден имейл.';
            }
            $data[$field] = $value;
        }
        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $_POST);
            $this->back();
        }
        return $data;
    }
}
