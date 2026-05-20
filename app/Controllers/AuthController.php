<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;
use App\Services\EmailVerificationService;

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
        if (EmailVerificationService::needsVerification($user)) {
            Session::flash('error', 'Потвърдете имейла си преди вход. Проверете пощата или изпратете линка отново.');
            Session::flash('verify_email', $user['email']);
            $this->redirect('/verify-email/pending');
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
        if (strlen($_POST['password'] ?? '') < 8) {
            Session::flash('error', 'Паролата трябва да е поне 8 символа.');
            $this->back();
        }
        if (empty($_POST['accept_terms'])) {
            Session::flash('error', 'Трябва да приемете общите условия.');
            $this->back();
        }
        if (empty($_POST['confirm_age'])) {
            Session::flash('error', 'Трябва да потвърдите, че сте навършили 16 години.');
            $this->back();
        }
        if (User::findByEmail($data['email'])) {
            Session::flash('error', 'Имейлът вече е регистриран.');
            $this->back();
        }
        $token = EmailVerificationService::createToken();
        $now = date('Y-m-d H:i:s');
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
            'is_public_profile' => isset($_POST['is_public_profile']) ? 1 : 0,
            'default_public_birds' => isset($_POST['default_public_birds']) ? 1 : 0,
            'default_public_lofts' => isset($_POST['default_public_lofts']) ? 1 : 0,
            'default_public_breeding' => isset($_POST['default_public_breeding']) ? 1 : 0,
            'email_verification_token' => $token,
            'terms_accepted_at' => $now,
            'age_confirmed_at' => $now,
        ]);
        $user = User::findByEmail($data['email']);
        $sent = EmailVerificationService::sendVerificationEmail($user);
        Session::flash('verify_email', $data['email']);
        if (!$sent) {
            $config = require BASE_PATH . '/config/app.php';
            $debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true' || ($config['debug'] ?? false);
            if ($debug) {
                Session::flash('verify_link', EmailVerificationService::verificationUrl($token));
                Session::flash('success', 'Акаунтът е създаден. Имейлът не беше изпратен (локален сървър) — използвайте линка по-долу.');
            } else {
                Session::flash('success', 'Акаунтът е създаден. Ако не получите имейл до няколко минути, използвайте „Изпрати отново“.');
            }
        } else {
            Session::flash('success', 'Акаунтът е създаден. Изпратихме имейл за потвърждение — проверете пощата си.');
        }
        $this->redirect('/verify-email/pending');
    }

    public function showVerifyPending(): void
    {
        $user = Auth::user();
        if ($user && !EmailVerificationService::needsVerification($user)) {
            $this->redirect('/dashboard');
        }
        $this->view('auth.verify_pending', [
            'email' => Session::flash('verify_email') ?? ($_GET['email'] ?? Auth::user()['email'] ?? ''),
        ], 'layouts.guest');
    }

    public function verifyEmail(): void
    {
        $token = trim($_GET['token'] ?? '');
        if ($token === '') {
            Session::flash('error', 'Невалиден линк за потвърждение.');
            $this->redirect('/verify-email/pending');
        }
        $user = EmailVerificationService::verify($token);
        if (!$user) {
            Session::flash('error', 'Линкът е невалиден или вече е използван.');
            $this->redirect('/verify-email/pending');
        }
        Auth::login($user);
        Session::flash('success', 'Имейлът е потвърден. Добре дошли!');
        $this->redirect('/dashboard');
    }

    public function resendVerification(): void
    {
        $email = trim($_POST['email'] ?? '');
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Въведете валиден имейл.');
            $this->redirect('/verify-email/pending');
        }
        EmailVerificationService::resendForEmail($email);
        Session::flash('verify_email', $email);
        Session::flash('success', 'Ако акаунтът съществува и не е потвърден, изпратихме нов имейл.');
        $this->redirect('/verify-email/pending?email=' . urlencode($email));
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/');
    }
}
