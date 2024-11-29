<?php

declare(strict_types=1);

use App\Constants\Enum\AdminRole;
use App\Model\Admin;
use Hyperf\Database\Seeders\Seeder;
use function Hyperf\Support\env;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        $admin = [
            'name' => 'admin',
            'mail' => env('ADMIN_DEFAULT_EMAIL'),
            'password' => password_hash('123456', PASSWORD_BCRYPT),
            'role' => AdminRole::SuperAdmin,
        ];
        Admin::firstOrCreate(['mail' => $admin['mail']], $admin);
    }
}
