<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

final class UserController extends Controller
{
    public function index(): void
    {
        $this->view('admin.users.index', ['users' => User::allForAdmin()], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $user = User::find((int) $id);
        if (!$user) {
            http_response_code(404);
            exit;
        }
        $this->view('admin.users.show', ['u' => $user], 'layouts.admin');
    }

    public function update(string $id): void
    {
        User::update((int) $id, [
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'role' => $_POST['role'] ?? 'user',
            'subscription_plan_id' => ($_POST['subscription_plan_id'] ?? '') ? (int) $_POST['subscription_plan_id'] : null,
            'subscription_expires_at' => ($_POST['subscription_expires_at'] ?? '') ?: null,
        ]);
        Session::flash('success', 'Потребителят е обновен.');
        $this->redirect('/admin/users/' . $id);
    }
}
