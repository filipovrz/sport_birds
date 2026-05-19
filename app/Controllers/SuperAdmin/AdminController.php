<?php

declare(strict_types=1);

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\User;
use App\Services\AdminPermissionService;

final class AdminController extends Controller
{
    public function index(): void
    {
        $admins = Database::fetchAll(
            "SELECT * FROM users WHERE role IN ('admin','super_admin') ORDER BY role, name"
        );
        $this->view('super_admin.admins', [
            'admins' => $admins,
            'permissionLabels' => AdminPermissionService::PERMISSIONS,
        ], 'layouts.admin');
    }

    public function edit(string $id): void
    {
        $user = User::find((int) $id);
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            Session::flash('error', 'Администраторът не е намерен.');
            $this->redirect('/super-admin/admins');
        }
        $this->view('super_admin.admin_edit', [
            'admin' => $user,
            'permissionLabels' => AdminPermissionService::PERMISSIONS,
            'granted' => AdminPermissionService::permissionsForUser($user) ?? array_keys(AdminPermissionService::PERMISSIONS),
        ], 'layouts.admin');
    }

    public function store(): void
    {
        $email = trim($_POST['email'] ?? '');
        if (User::findByEmail($email)) {
            Session::flash('error', 'Имейлът вече съществува.');
            $this->back();
        }
        $permissions = $_POST['permissions'] ?? [];
        $id = User::create([
            'name' => trim($_POST['name'] ?? 'Admin'),
            'email' => $email,
            'password' => $_POST['password'] ?? 'ChangeMe123!',
            'role' => 'admin',
            'user_type' => 'owner',
            'bird_specialties' => 'racing_pigeon',
            'email_verified_at' => date('Y-m-d H:i:s'),
            'terms_accepted_at' => date('Y-m-d H:i:s'),
            'age_confirmed_at' => date('Y-m-d H:i:s'),
        ]);
        AdminPermissionService::saveForUser($id, is_array($permissions) ? $permissions : []);
        Session::flash('success', 'Администраторът е създаден.');
        $this->redirect('/super-admin/admins');
    }

    public function update(string $id): void
    {
        $user = User::find((int) $id);
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            Session::flash('error', 'Администраторът не е намерен.');
            $this->redirect('/super-admin/admins');
        }
        $data = [
            'name' => trim($_POST['name'] ?? $user['name']),
            'email' => trim($_POST['email'] ?? $user['email']),
        ];
        if (trim($_POST['password'] ?? '') !== '') {
            $data['password'] = $_POST['password'];
        }
        User::update((int) $id, $data);
        $permissions = $_POST['permissions'] ?? [];
        AdminPermissionService::saveForUser((int) $id, is_array($permissions) ? $permissions : []);
        Session::flash('success', 'Администраторът е обновен.');
        $this->redirect('/super-admin/admins/' . $id . '/edit');
    }

    public function revoke(string $id): void
    {
        $user = User::find((int) $id);
        if ($user && $user['role'] === 'admin') {
            User::update((int) $id, ['role' => 'user', 'admin_permissions' => null]);
            Session::flash('success', 'Администраторските права са премахнати.');
        }
        $this->redirect('/super-admin/admins');
    }
}
