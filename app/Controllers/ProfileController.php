<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

final class ProfileController extends Controller
{
    public function edit(): void
    {
        $this->view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(): void
    {
        $d = $this->validate(['name' => 'required', 'email' => 'required|email']);
        $uid = Auth::id();
        User::update($uid, [
            'name' => $d['name'],
            'email' => $d['email'],
            'phone' => trim($_POST['phone'] ?? '') ?: null,
            'city' => trim($_POST['city'] ?? '') ?: null,
            'club_name' => trim($_POST['club_name'] ?? '') ?: null,
            'password' => $_POST['password'] ?? '',
            'is_public_profile' => isset($_POST['is_public_profile']) ? 1 : 0,
            'default_public_birds' => isset($_POST['default_public_birds']) ? 1 : 0,
            'default_public_lofts' => isset($_POST['default_public_lofts']) ? 1 : 0,
            'default_public_breeding' => isset($_POST['default_public_breeding']) ? 1 : 0,
        ]);
        Session::flash('success', 'Профилът е обновен.');
        $this->redirect('/dashboard/profile');
    }
}
