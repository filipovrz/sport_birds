<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/bootstrap/app.php';

use App\Core\Router;
use App\Core\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\PremiumMiddleware;
use App\Middleware\GuestMiddleware;

Session::start();

$router = new Router();

// Public
$router->get('/', 'HomeController@index');
$router->get('/pricing', 'HomeController@pricing');
$router->get('/login', 'AuthController@showLogin', [GuestMiddleware::class]);
$router->post('/login', 'AuthController@login', [GuestMiddleware::class]);
$router->get('/register', 'AuthController@showRegister', [GuestMiddleware::class]);
$router->post('/register', 'AuthController@register', [GuestMiddleware::class]);
$router->post('/logout', 'AuthController@logout');

// Install
$router->get('/install', 'InstallController@index');
$router->post('/install', 'InstallController@run');

// Authenticated user area
$router->group(['prefix' => '/dashboard', 'middleware' => [AuthMiddleware::class]], function (Router $r) {
    $r->get('', 'DashboardController@index');
    $r->get('/profile', 'ProfileController@edit');
    $r->post('/profile', 'ProfileController@update');

    $r->get('/lofts', 'LoftController@index');
    $r->get('/lofts/create', 'LoftController@create');
    $r->post('/lofts', 'LoftController@store');
    $r->get('/lofts/{id}', 'LoftController@show');
    $r->get('/lofts/{id}/edit', 'LoftController@edit');
    $r->post('/lofts/{id}', 'LoftController@update');
    $r->post('/lofts/{id}/delete', 'LoftController@destroy');

    $r->get('/birds', 'BirdController@index');
    $r->get('/birds/create', 'BirdController@create');
    $r->post('/birds', 'BirdController@store');
    $r->get('/birds/{id}', 'BirdController@show');
    $r->get('/birds/{id}/edit', 'BirdController@edit');
    $r->post('/birds/{id}', 'BirdController@update');
    $r->post('/birds/{id}/delete', 'BirdController@destroy');
    $r->get('/birds/{id}/pedigree', 'PedigreeController@show');

    $r->get('/breeding', 'BreedingController@index');
    $r->get('/breeding/create', 'BreedingController@create');
    $r->post('/breeding', 'BreedingController@store');
    $r->get('/breeding/{id}', 'BreedingController@show');

    $r->get('/health', 'HealthController@index');
    $r->get('/health/create', 'HealthController@create');
    $r->post('/health', 'HealthController@store');
    $r->get('/health/{id}/edit', 'HealthController@edit');
    $r->post('/health/{id}', 'HealthController@update');

    $r->get('/training', 'TrainingController@index');
    $r->get('/training/create', 'TrainingController@create');
    $r->post('/training', 'TrainingController@store');

    $r->get('/competitions', 'CompetitionController@index');
    $r->get('/competitions/create', 'CompetitionController@create');
    $r->post('/competitions', 'CompetitionController@store');
    $r->get('/competitions/{id}', 'CompetitionController@show');
    $r->post('/competitions/{id}/results', 'CompetitionController@storeResult');

    $r->get('/subscription', 'SubscriptionController@index');
    $r->post('/subscription/request', 'SubscriptionController@requestPlan');
});

// Admin panel
$router->group(['prefix' => '/admin', 'middleware' => [AuthMiddleware::class, AdminMiddleware::class]], function (Router $r) {
    $r->get('', 'Admin\DashboardController@index');
    $r->get('/users', 'Admin\UserController@index');
    $r->get('/users/{id}', 'Admin\UserController@show');
    $r->post('/users/{id}', 'Admin\UserController@update');
    $r->get('/plans', 'Admin\PlanController@index');
    $r->get('/plans/create', 'Admin\PlanController@create');
    $r->post('/plans', 'Admin\PlanController@store');
    $r->get('/plans/{id}/edit', 'Admin\PlanController@edit');
    $r->post('/plans/{id}', 'Admin\PlanController@update');
    $r->get('/subscriptions', 'Admin\SubscriptionController@index');
    $r->post('/subscriptions/{id}/approve', 'Admin\SubscriptionController@approve');
    $r->post('/subscriptions/{id}/reject', 'Admin\SubscriptionController@reject');
    $r->get('/settings', 'Admin\SettingsController@index');
    $r->post('/settings', 'Admin\SettingsController@update');
});

// Super admin only
$router->group(['prefix' => '/super-admin', 'middleware' => [AuthMiddleware::class, AdminMiddleware::class . ':super_admin']], function (Router $r) {
    $r->get('', 'SuperAdmin\DashboardController@index');
    $r->get('/admins', 'SuperAdmin\AdminController@index');
    $r->post('/admins', 'SuperAdmin\AdminController@store');
    $r->post('/admins/{id}/revoke', 'SuperAdmin\AdminController@revoke');
    $r->get('/system', 'SuperAdmin\SystemController@index');
    $r->post('/system', 'SuperAdmin\SystemController@update');
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
