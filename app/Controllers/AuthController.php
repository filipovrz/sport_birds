<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth.login', [], 'layouts.guest');
    }

    public function login(): void
    {
        $data = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::findByEmail($data['email']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            Session::flash('error', 'Грешни данни за вход.');
            $this->back();
        }
        if (!$user['is_active']) {
            Session::flash('error', 'Акаунтът е деактивиран.');
            $this->back();
        }
        Auth::login($user);
        if (in_array($user['role'], ['admin', 'super_admin'], true)) {
            $this->redirect('/admin');
        }
        $this->redirect('/dashboard');
    }

    public function showRegister(): void
    {
        $this->view('auth.register', [], 'layouts.guest');
    }

    public function register(): void
    {
        $data = $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'phone' => '',
            'city' => '',
        ]);
        if (User::findByEmail($data['email'])) {
            Session::flash('error', 'Имейлът вече е регистриран.');
            $this->back();
        }
        $userTypes = $_POST['user_type'] ?? ['owner'];
        $specialties = $_POST['bird_specialties'] ?? ['racing_pigeon'];
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $_POST['password'],
            'phone' => $data['phone'] ?: null,
            'city' => $data['city'] ?: null,
            'user_type' => implode(',', (array) $userTypes),
            'bird_specialties' => implode(',', (array) $specialties),
            'club_name' => trim($_POST['club_name'] ?? '') ?: null,
        ]);
        $user = User::findByEmail($data['email']);
        Auth::login($user);
        Session::flash('success', 'Добре дошли в Best Sport Byrds!');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/');
    }
}
