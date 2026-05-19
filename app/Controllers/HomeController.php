<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Services\Migrator;
use App\Services\SubscriptionService;

final class HomeController extends Controller
{
    public function index(): void
    {
        $cfg = require BASE_PATH . '/config/app.php';
        $this->view('home.landing', [
            'landingPage' => true,
            'installed' => App::isInstalled(),
            'plans' => App::isInstalled() ? SubscriptionService::plans() : [],
            'showDevLink' => $this->isDevEnvironment($cfg),
            'pageTitle' => $cfg['name'] . ' — ' . ($cfg['tagline'] ?? ''),
        ], 'layouts.guest');
    }

    public function testHub(): void
    {
        $cfg = require BASE_PATH . '/config/app.php';
        if (!$this->isDevEnvironment($cfg)) {
            $this->redirect('/');
        }
        $samples = $this->sampleIds();
        $this->view('home.portal', [
            'sections' => $this->testSections($samples),
            'installed' => App::isInstalled(),
            'version' => Migrator::currentVersion(),
            'appVersion' => $cfg['version'] ?? '2.0.0',
            'env' => $cfg['env'] ?? 'production',
            'isLoggedIn' => Auth::check(),
            'plans' => App::isInstalled() ? SubscriptionService::plans() : [],
        ], 'layouts.hub');
    }

    public function pricing(): void
    {
        $this->view('home.pricing', [
            'plans' => SubscriptionService::plans(),
        ], 'layouts.guest');
    }

    /** @param array<string, mixed> $cfg */
    private function isDevEnvironment(array $cfg): bool
    {
        return ($cfg['debug'] ?? false) || ($cfg['env'] ?? 'production') === 'local';
    }

    /** @return array{bird_id: int, loft_id: int, gps_id: int, breeding_id: int, competition_id: int, health_id: int, announcement_id: int, user_id: int} */
    private function sampleIds(): array
    {
        $defaults = [
            'bird_id' => 1,
            'loft_id' => 1,
            'gps_id' => 1,
            'breeding_id' => 1,
            'competition_id' => 1,
            'health_id' => 1,
            'announcement_id' => 1,
            'user_id' => 1,
        ];
        if (!App::isInstalled()) {
            return $defaults;
        }
        try {
            $map = [
                'bird_id' => 'birds',
                'loft_id' => 'lofts',
                'gps_id' => 'gps_devices',
                'breeding_id' => 'breeding_pairs',
                'competition_id' => 'competitions',
                'health_id' => 'health_records',
                'announcement_id' => 'competition_announcements',
                'user_id' => 'users',
            ];
            foreach ($map as $key => $table) {
                $row = Database::fetch("SELECT id FROM {$table} ORDER BY id ASC LIMIT 1");
                if ($row) {
                    $defaults[$key] = (int) $row['id'];
                }
            }
        } catch (\Throwable) {
        }
        return $defaults;
    }

    /**
     * @param array<string, int> $s
     * @return list<array{title: string, icon: string, id: string, links: list<array{label: string, url: string, note: string, auth?: bool}>}>
     */
    private function testSections(array $s): array
    {
        $b = $s['bird_id'];
        $l = $s['loft_id'];
        $g = $s['gps_id'];
        $br = $s['breeding_id'];
        $c = $s['competition_id'];
        $h = $s['health_id'];
        $a = $s['announcement_id'];
        $u = $s['user_id'];

        return [
            [
                'title' => 'Система и публични',
                'icon' => '⚙',
                'id' => 'system',
                'links' => [
                    ['label' => 'Начална страница', 'url' => '/', 'note' => 'Продукционен изглед'],
                    ['label' => 'Инсталация', 'url' => '/install', 'note' => 'Първо стъпка при нова инсталация'],
                    ['label' => 'Цени и планове', 'url' => '/pricing', 'note' => ''],
                    ['label' => 'Вход', 'url' => '/login', 'note' => ''],
                    ['label' => 'Регистрация', 'url' => '/register', 'note' => 'имейл потвърждение'],
                    ['label' => 'Потвърди имейл', 'url' => '/verify-email/pending', 'note' => ''],
                    ['label' => 'Обяви за състезания', 'url' => '/announcements', 'note' => 'Публичен списък'],
                    ['label' => 'Обява (детайл)', 'url' => "/announcements/{$a}", 'note' => "ID={$a}"],
                    ['label' => 'Публично родословие', 'url' => "/pedigree/public/{$b}", 'note' => 'Ако птицата е публична'],
                ],
            ],
            [
                'title' => 'Табло и профил',
                'icon' => '◉',
                'id' => 'dashboard',
                'links' => [
                    ['label' => 'Табло', 'url' => '/dashboard', 'note' => '', 'auth' => true],
                    ['label' => 'Профил', 'url' => '/dashboard/profile', 'note' => '', 'auth' => true],
                    ['label' => 'Абонамент', 'url' => '/dashboard/subscription', 'note' => '', 'auth' => true],
                ],
            ],
            [
                'title' => 'Гълъбарници',
                'icon' => '⌂',
                'id' => 'lofts',
                'links' => [
                    ['label' => 'Списък гълъбарници', 'url' => '/dashboard/lofts', 'note' => '', 'auth' => true],
                    ['label' => 'Нов гълъбарник', 'url' => '/dashboard/lofts/create', 'note' => 'С карта', 'auth' => true],
                    ['label' => 'Преглед гълъбарник', 'url' => "/dashboard/lofts/{$l}", 'note' => "ID={$l}", 'auth' => true],
                    ['label' => 'Редакция гълъбарник', 'url' => "/dashboard/lofts/{$l}/edit", 'note' => '', 'auth' => true],
                ],
            ],
            [
                'title' => 'Птици и родословие',
                'icon' => '🕊',
                'id' => 'birds',
                'links' => [
                    ['label' => 'Списък птици', 'url' => '/dashboard/birds', 'note' => '', 'auth' => true],
                    ['label' => 'Нова птица', 'url' => '/dashboard/birds/create', 'note' => 'Със снимка', 'auth' => true],
                    ['label' => 'Преглед птица', 'url' => "/dashboard/birds/{$b}", 'note' => "ID={$b}", 'auth' => true],
                    ['label' => 'Редакция птица', 'url' => "/dashboard/birds/{$b}/edit", 'note' => '', 'auth' => true],
                    ['label' => 'Родословие', 'url' => "/dashboard/birds/{$b}/pedigree", 'note' => '', 'auth' => true],
                    ['label' => 'Печат / PDF родословие', 'url' => "/dashboard/birds/{$b}/pedigree/print", 'note' => 'Pro план', 'auth' => true],
                ],
            ],
            [
                'title' => 'GPS и карта',
                'icon' => '📍',
                'id' => 'gps',
                'links' => [
                    ['label' => 'GPS устройства', 'url' => '/dashboard/gps', 'note' => '', 'auth' => true],
                    ['label' => 'Регистрация GPS', 'url' => '/dashboard/gps/create', 'note' => '', 'auth' => true],
                    ['label' => 'Детайли GPS + API', 'url' => "/dashboard/gps/{$g}", 'note' => "ID={$g}", 'auth' => true],
                    ['label' => 'Редакция GPS', 'url' => "/dashboard/gps/{$g}/edit", 'note' => '', 'auth' => true],
                    ['label' => 'Глобална карта', 'url' => '/dashboard/map', 'note' => 'Гълъбарници + GPS + обяви', 'auth' => true],
                    ['label' => 'API тест (GET)', 'url' => '/api/gps/track?token=ТОКЕН&latitude=42.15&longitude=24.75', 'note' => 'Заменете ТОКЕН'],
                ],
            ],
            [
                'title' => 'Развъждане, здраве, тренировки',
                'icon' => '✚',
                'id' => 'care',
                'links' => [
                    ['label' => 'Развъждане', 'url' => '/dashboard/breeding', 'note' => '', 'auth' => true],
                    ['label' => 'Нова двойка', 'url' => '/dashboard/breeding/create', 'note' => '', 'auth' => true],
                    ['label' => 'Детайли развъждане', 'url' => "/dashboard/breeding/{$br}", 'note' => '', 'auth' => true],
                    ['label' => 'Здраве', 'url' => '/dashboard/health', 'note' => '', 'auth' => true],
                    ['label' => 'Нов здравен запис', 'url' => '/dashboard/health/create', 'note' => '', 'auth' => true],
                    ['label' => 'Редакция здраве', 'url' => "/dashboard/health/{$h}/edit", 'note' => "ID={$h}", 'auth' => true],
                    ['label' => 'Тренировки', 'url' => '/dashboard/training', 'note' => '', 'auth' => true],
                    ['label' => 'Нова тренировка', 'url' => '/dashboard/training/create', 'note' => '', 'auth' => true],
                ],
            ],
            [
                'title' => 'Състезания и обяви (логин)',
                'icon' => '🏆',
                'id' => 'events',
                'links' => [
                    ['label' => 'Мои състезания', 'url' => '/dashboard/competitions', 'note' => '', 'auth' => true],
                    ['label' => 'Ново състезание', 'url' => '/dashboard/competitions/create', 'note' => '', 'auth' => true],
                    ['label' => 'Резултати състезание', 'url' => "/dashboard/competitions/{$c}", 'note' => "ID={$c}", 'auth' => true],
                    ['label' => 'Моите обяви', 'url' => '/dashboard/announcements/my', 'note' => '', 'auth' => true],
                    ['label' => 'Публикувай обява', 'url' => '/dashboard/announcements/create', 'note' => 'С такса', 'auth' => true],
                ],
            ],
            [
                'title' => 'Администрация',
                'icon' => '🔐',
                'id' => 'admin',
                'links' => [
                    ['label' => 'Админ табло', 'url' => '/admin', 'note' => 'admin / super_admin', 'auth' => true],
                    ['label' => 'Потребители', 'url' => '/admin/users', 'note' => '', 'auth' => true],
                    ['label' => 'Потребител (пример)', 'url' => "/admin/users/{$u}", 'note' => "ID={$u}", 'auth' => true],
                    ['label' => 'Планове', 'url' => '/admin/plans', 'note' => '', 'auth' => true],
                    ['label' => 'Абонаменти (заявки)', 'url' => '/admin/subscriptions', 'note' => '', 'auth' => true],
                    ['label' => 'Настройки сайт', 'url' => '/admin/settings', 'note' => '', 'auth' => true],
                    ['label' => 'Футър и политики', 'url' => '/admin/footer', 'note' => 'право settings', 'auth' => true],
                    ['label' => 'Поверителност', 'url' => '/legal/privacy', 'note' => 'публично', 'auth' => false],
                    ['label' => 'Условия', 'url' => '/legal/terms', 'note' => 'публично', 'auth' => false],
                    ['label' => 'Бисквитки', 'url' => '/legal/cookies', 'note' => 'публично', 'auth' => false],
                    ['label' => 'Плащания за обяви', 'url' => '/admin/announcement-payments', 'note' => '', 'auth' => true],
                    ['label' => 'Архив състезания', 'url' => '/admin/competition-archive', 'note' => '', 'auth' => true],
                    ['label' => 'Обяви събития', 'url' => '/events', 'note' => 'публично', 'auth' => false],
                    ['label' => 'Общност', 'url' => '/community', 'note' => 'публични профили', 'auth' => true],
                    ['label' => 'Плащания събития', 'url' => '/admin/event-payments', 'note' => '', 'auth' => true],
                    ['label' => 'Архив събития', 'url' => '/admin/event-archive', 'note' => '', 'auth' => true],
                    ['label' => 'Супер админ', 'url' => '/super-admin', 'note' => 'super_admin', 'auth' => true],
                    ['label' => 'Администратори', 'url' => '/super-admin/admins', 'note' => '', 'auth' => true],
                    ['label' => 'Система / поддръжка', 'url' => '/super-admin/system', 'note' => '', 'auth' => true],
                ],
            ],
        ];
    }
}
