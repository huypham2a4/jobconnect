<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

require dirname(__DIR__).'/vendor/autoload.php';

try {
    if (! extension_loaded('pdo_mysql')) {
        throw new RuntimeException('Can bat extension pdo_mysql trong PHP cua Laragon.');
    }

    $app = require dirname(__DIR__).'/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();

    $driver = config('database.default');
    if (! in_array($driver, ['mysql', 'mariadb'], true)) {
        throw new RuntimeException('Nut khoi dong nay can DB_CONNECTION=mysql hoac mariadb trong .env.');
    }

    $settings = config('database.connections.'.$driver);
    $database = $settings['database'];
    if (! preg_match('/\A[a-zA-Z0-9_]+\z/', $database)) {
        throw new RuntimeException('Ten database local chi duoc chua chu, so va dau gach duoi.');
    }

    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $settings['host'], $settings['port']),
        $settings['username'],
        $settings['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
    );
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `'.$database.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $statement = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ?');
    $statement->execute([$database]);
    $pendingSeed = storage_path('app/jobconnect-local/seed-'.sha1($database).'.pending');
    if ((int) $statement->fetchColumn() === 0) {
        file_put_contents($pendingSeed, 'Seed only this newly initialized database.');
    }

    $status = Artisan::call('migrate', ['--force' => true, '--no-interaction' => true]);
    echo Artisan::output();
    if ($status !== 0) {
        throw new RuntimeException('Migration chua hoan tat. Du lieu hien co duoc giu nguyen.');
    }

    if (is_file($pendingSeed)) {
        if (! User::exists()) {
            DB::transaction(function (): void {
                if (Artisan::call('db:seed', ['--force' => true, '--no-interaction' => true]) !== 0) {
                    throw new RuntimeException('Khong tao duoc du lieu mau.');
                }
                echo Artisan::output();
            });
        }
        unlink($pendingSeed);
    }

    if (Artisan::call('db:seed', ['--class' => 'DemoCvSeeder', '--force' => true, '--no-interaction' => true]) !== 0) {
        throw new RuntimeException('Khong chuan bi duoc tep CV mau.');
    }
    if (Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true, '--no-interaction' => true]) !== 0) {
        throw new RuntimeException('Khong cap nhat duoc danh sach quyen mac dinh.');
    }
    echo 'Database '.$database.' san sang; giu nguyen du lieu da co.'.PHP_EOL;
} catch (Throwable $exception) {
    fwrite(STDERR, 'LOI DATABASE: '.$exception->getMessage().PHP_EOL);
    exit(1);
}
