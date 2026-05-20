<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/bootstrap/app.php';

use App\Core\Router;
use App\Core\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\MaintenanceMiddleware;
use App\Middleware\RequireInstallMiddleware;
use App\Middleware\InstallLockMiddleware;

Session::start();

$router = new Router();
$mw = [MaintenanceMiddleware::class];
$installed = [RequireInstallMiddleware::class];
$csrf = [CsrfMiddleware::class];

// Install (before app is installed)
$router->get('/install', 'InstallController@index', $mw);
$router->post('/install', 'InstallController@run', array_merge($mw, [InstallLockMiddleware::class], $csrf));

// Начална страница (продукционен изглед) — без изискване за инсталация
$router->get('/', 'HomeController@index', $mw);
$router->get('/dev', 'HomeController@testHub', $mw);
$router->get('/pricing', 'HomeController@pricing', array_merge($mw, $installed));
$router->get('/payment-methods', 'PaymentMethodsController@index', array_merge($mw, $installed));
$router->get('/payment-methods/{slug}', 'PaymentMethodsController@show', array_merge($mw, $installed));
$router->get('/legal/{slug}', 'LegalController@show', array_merge($mw, $installed));
$router->get('/pedigree/public/{id}', 'PublicPedigreeController@show', $mw);
$router->get('/announcements', 'AnnouncementController@index', array_merge($mw, $installed));
$router->get('/announcements/{id}', 'AnnouncementController@show', array_merge($mw, $installed));
$router->post('/announcements/{id}/register', 'AnnouncementController@register', array_merge($mw, $installed, $csrf));
$router->get('/events', 'EventController@index', array_merge($mw, $installed));
$router->get('/events/{id}', 'EventController@show', array_merge($mw, $installed));
$router->post('/events/{id}/register', 'EventController@register', array_merge($mw, $installed, $csrf));

// GPS API (устройства изпращат позиция)
$router->post('/api/gps/track', 'GpsApiController@track', $mw);
$router->get('/api/gps/track', 'GpsApiController@track', $mw);

$router->get('/login', 'AuthController@showLogin', array_merge($mw, $installed, [GuestMiddleware::class]));
$router->post('/login', 'AuthController@login', array_merge($mw, $installed, [GuestMiddleware::class], $csrf));
$router->get('/register', 'AuthController@showRegister', array_merge($mw, $installed, [GuestMiddleware::class]));
$router->post('/register', 'AuthController@register', array_merge($mw, $installed, [GuestMiddleware::class], $csrf));
$router->get('/verify-email/pending', 'AuthController@showVerifyPending', array_merge($mw, $installed));
$router->get('/verify-email', 'AuthController@verifyEmail', array_merge($mw, $installed));
$router->post('/verify-email/resend', 'AuthController@resendVerification', array_merge($mw, $installed, $csrf));
$router->post('/logout', 'AuthController@logout', array_merge($mw, $installed, $csrf));

// Плащания (webhooks без CSRF)
$router->post('/webhooks/stripe', 'WebhookController@stripe', $mw);
$router->post('/webhooks/epay', 'WebhookController@epay', $mw);
$router->post('/webhooks/paypal', 'WebhookController@paypal', $mw);
$router->post('/webhooks/revolut', 'WebhookController@revolut', $mw);

$router->group(['middleware' => array_merge($mw, $installed, [AuthMiddleware::class])], function (Router $r) use ($csrf) {
    $r->get('/payment/checkout/{slug}', 'PaymentMethodsController@checkout');
    $r->post('/payment/checkout/{slug}', 'PaymentMethodsController@checkout', $csrf);
    $r->get('/payment/bank/{token}', 'PaymentController@bank');
    $r->get('/payment/status/{token}', 'PaymentController@status');
    $r->get('/payment/return/{token}', 'PaymentController@return');
    $r->post('/payment/return/{token}', 'PaymentController@return');
    $r->get('/payment/cancel/{token}', 'PaymentController@cancel');
    $r->get('/payment/go/{token}', 'PaymentController@redirectForm');
});

// Общност (логнати потребители)
$router->group(['middleware' => array_merge($mw, $installed, [AuthMiddleware::class])], function (Router $r) {
    $r->get('/community', 'CommunityController@index');
    $r->get('/community/users/{id}', 'CommunityController@user');
    $r->get('/community/birds/{id}', 'CommunityController@bird');
    $r->get('/community/lofts/{id}', 'CommunityController@loft');
    $r->get('/community/breeding/{id}', 'CommunityController@breeding');
});

// Authenticated user area
$router->group(['prefix' => '/dashboard', 'middleware' => array_merge($mw, $installed, [AuthMiddleware::class])], function (Router $r) use ($csrf) {
    $r->get('', 'DashboardController@index');
    $r->get('/profile', 'ProfileController@edit');
    $r->post('/profile', 'ProfileController@update', $csrf);

    $r->get('/invoices', 'InvoiceController@index');
    $r->get('/invoices/{id}', 'InvoiceController@show');
    $r->get('/invoices/{id}/print', 'InvoiceController@print');

    $r->get('/lofts', 'LoftController@index');
    $r->get('/lofts/create', 'LoftController@create');
    $r->post('/lofts', 'LoftController@store', $csrf);
    $r->get('/lofts/{id}', 'LoftController@show');
    $r->get('/lofts/{id}/edit', 'LoftController@edit');
    $r->post('/lofts/{id}', 'LoftController@update', $csrf);
    $r->post('/lofts/{id}/delete', 'LoftController@destroy', $csrf);

    $r->get('/birds', 'BirdController@index');
    $r->get('/birds/create', 'BirdController@create');
    $r->post('/birds', 'BirdController@store', $csrf);
    $r->get('/birds/{id}', 'BirdController@show');
    $r->get('/birds/{id}/edit', 'BirdController@edit');
    $r->post('/birds/{id}', 'BirdController@update', $csrf);
    $r->post('/birds/{id}/delete', 'BirdController@destroy', $csrf);
    $r->get('/birds/{id}/pedigree', 'PedigreeController@show');
    $r->get('/birds/{id}/pedigree/print', 'PedigreeController@print');

    $r->get('/breeding', 'BreedingController@index');
    $r->get('/breeding/create', 'BreedingController@create');
    $r->post('/breeding', 'BreedingController@store', $csrf);
    $r->get('/breeding/{id}', 'BreedingController@show');

    $r->get('/health', 'HealthController@index');
    $r->get('/health/create', 'HealthController@create');
    $r->post('/health', 'HealthController@store', $csrf);
    $r->get('/health/{id}/edit', 'HealthController@edit');
    $r->post('/health/{id}', 'HealthController@update', $csrf);

    $r->get('/training', 'TrainingController@index');
    $r->get('/training/create', 'TrainingController@create');
    $r->post('/training', 'TrainingController@store', $csrf);

    $r->get('/competitions', 'CompetitionController@index');
    $r->get('/competitions/create', 'CompetitionController@create');
    $r->post('/competitions', 'CompetitionController@store', $csrf);
    $r->get('/competitions/{id}', 'CompetitionController@show');
    $r->post('/competitions/{id}/results', 'CompetitionController@storeResult', $csrf);

    $r->get('/subscription', 'SubscriptionController@index');
    $r->post('/subscription/request', 'SubscriptionController@requestPlan', $csrf);

    $r->get('/map', 'MapController@index');
    $r->get('/export/birds.csv', 'ExportController@birds');
    $r->get('/export/lofts.csv', 'ExportController@lofts');
    $r->get('/export/competitions.csv', 'ExportController@competitions');
    $r->get('/gps', 'GpsDeviceController@index');
    $r->get('/gps/create', 'GpsDeviceController@create');
    $r->post('/gps', 'GpsDeviceController@store', $csrf);
    $r->get('/gps/{id}', 'GpsDeviceController@show');
    $r->get('/gps/{id}/edit', 'GpsDeviceController@edit');
    $r->post('/gps/{id}', 'GpsDeviceController@update', $csrf);
    $r->post('/gps/{id}/delete', 'GpsDeviceController@destroy', $csrf);
    $r->post('/gps/{id}/token', 'GpsDeviceController@regenerateToken', $csrf);

    $r->get('/announcements/my', 'AnnouncementController@my');
    $r->get('/announcements/create', 'AnnouncementController@create');
    $r->post('/announcements', 'AnnouncementController@store', $csrf);
    $r->post('/announcements/{id}/register', 'AnnouncementController@register', $csrf);

    $r->get('/events/my', 'EventController@my');
    $r->get('/events/create', 'EventController@create');
    $r->post('/events', 'EventController@store', $csrf);
    $r->post('/events/{id}/register', 'EventController@register', $csrf);
});

// Admin panel
$router->group(['prefix' => '/admin', 'middleware' => array_merge($mw, $installed, [AuthMiddleware::class, AdminMiddleware::class])], function (Router $r) use ($csrf) {
    $r->get('', 'Admin\DashboardController@index');
    $r->get('/users', 'Admin\UserController@index');
    $r->get('/users/{id}', 'Admin\UserController@show');
    $r->post('/users/{id}', 'Admin\UserController@update', $csrf);
    $r->get('/plans', 'Admin\PlanController@index');
    $r->get('/plans/create', 'Admin\PlanController@create');
    $r->post('/plans', 'Admin\PlanController@store', $csrf);
    $r->get('/plans/{id}/edit', 'Admin\PlanController@edit');
    $r->post('/plans/{id}', 'Admin\PlanController@update', $csrf);
    $r->post('/plans/{id}/delete', 'Admin\PlanController@destroy', $csrf);
    $r->get('/subscriptions', 'Admin\SubscriptionController@index');
    $r->get('/subscriptions/{id}/print', 'Admin\SubscriptionController@print');
    $r->get('/subscriptions/{id}', 'Admin\SubscriptionController@show');
    $r->post('/subscriptions/{id}/approve', 'Admin\SubscriptionController@approve', $csrf);
    $r->post('/subscriptions/{id}/reject', 'Admin\SubscriptionController@reject', $csrf);
    $r->post('/subscriptions/{id}/delete', 'Admin\SubscriptionController@destroy', $csrf);
    $r->get('/settings', 'Admin\SettingsController@index');
    $r->post('/settings', 'Admin\SettingsController@update', $csrf);
    $r->get('/footer', 'Admin\FooterController@index');
    $r->post('/footer', 'Admin\FooterController@update', $csrf);
    $r->get('/announcement-payments', 'Admin\AnnouncementPaymentController@index');
    $r->get('/announcement-payments/{id}/print', 'Admin\AnnouncementPaymentController@print');
    $r->get('/announcement-payments/{id}', 'Admin\AnnouncementPaymentController@show');
    $r->post('/announcement-payments/{id}/approve', 'Admin\AnnouncementPaymentController@approve', $csrf);
    $r->post('/announcement-payments/{id}/reject', 'Admin\AnnouncementPaymentController@reject', $csrf);
    $r->post('/announcement-payments/{id}/delete', 'Admin\AnnouncementPaymentController@destroy', $csrf);
    $r->get('/competition-archive', 'Admin\CompetitionArchiveController@index');
    $r->get('/competition-archive/{id}/print', 'Admin\CompetitionArchiveController@print');
    $r->get('/competition-archive/{id}/edit', 'Admin\CompetitionArchiveController@edit');
    $r->get('/competition-archive/{id}', 'Admin\CompetitionArchiveController@show');
    $r->post('/competition-archive/{id}', 'Admin\CompetitionArchiveController@update', $csrf);
    $r->post('/competition-archive/{id}/delete', 'Admin\CompetitionArchiveController@destroy', $csrf);
    $r->get('/event-payments', 'Admin\EventPaymentController@index');
    $r->get('/event-payments/{id}/print', 'Admin\EventPaymentController@print');
    $r->get('/event-payments/{id}', 'Admin\EventPaymentController@show');
    $r->post('/event-payments/{id}/approve', 'Admin\EventPaymentController@approve', $csrf);
    $r->post('/event-payments/{id}/reject', 'Admin\EventPaymentController@reject', $csrf);
    $r->post('/event-payments/{id}/delete', 'Admin\EventPaymentController@destroy', $csrf);
    $r->get('/event-archive', 'Admin\EventArchiveController@index');
    $r->get('/event-archive/{id}/print', 'Admin\EventArchiveController@print');
    $r->get('/event-archive/{id}/edit', 'Admin\EventArchiveController@edit');
    $r->get('/event-archive/{id}', 'Admin\EventArchiveController@show');
    $r->post('/event-archive/{id}', 'Admin\EventArchiveController@update', $csrf);
    $r->post('/event-archive/{id}/delete', 'Admin\EventArchiveController@destroy', $csrf);
    $r->post('/health-reminders/send', 'Admin\NotificationController@sendHealthReminders', $csrf);
});

// Super admin only
$router->group(['prefix' => '/super-admin', 'middleware' => array_merge($mw, $installed, [AuthMiddleware::class, AdminMiddleware::class . ':super_admin'])], function (Router $r) use ($csrf) {
    $r->get('', 'SuperAdmin\DashboardController@index');
    $r->get('/admins', 'SuperAdmin\AdminController@index');
    $r->post('/admins', 'SuperAdmin\AdminController@store', $csrf);
    $r->get('/admins/{id}/edit', 'SuperAdmin\AdminController@edit');
    $r->post('/admins/{id}', 'SuperAdmin\AdminController@update', $csrf);
    $r->post('/admins/{id}/revoke', 'SuperAdmin\AdminController@revoke', $csrf);
    $r->get('/system', 'SuperAdmin\SystemController@index');
    $r->post('/system', 'SuperAdmin\SystemController@update', $csrf);
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
