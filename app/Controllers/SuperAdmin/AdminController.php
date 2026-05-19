<?php

declare(strict_types=1);

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\User;

final class AdminController extends Controller
{
    public function index(): void
    {
        $admins = Database::fetchAll(
            "SELECT * FROM users WHERE role IN ('admin','super_admin') ORDER BY role, name"
        );
        $this->view('super_admin.admins', ['admins' => $admins], 'layouts.admin');
    }

    public function store(): void
    {
        $email = trim($_POST['email'] ?? '');
        if (User::findByEmail($email)) {
            Session::flash('error', 'Имейлът вече съществува.');
            $this->back();
        }
        User::create([
            'name' => trim($_POST['name'] ?? 'Admin'),
            'email' => $email,
            'password' => $_POST['password'] ?? 'ChangeMe123!',
            'role' => 'admin',
            'user_type' => 'owner',
            'bird_specialties' => 'racing_pigeon',
        ]);
        Session::flash('success', 'Администраторът е създаден.');
        $this->redirect('/super-admin/admins');
    }

    public function revoke(string $id): void
    {
        $user = User::find((int) $id);
        if ($user && $user['role'] === 'admin') {
            User::update((int) $id, ['role' => 'user']);
        }
        $this->redirect('/super-admin/admins');
    }
}
