<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the initial administrator account reading password from ADMIN_PASSWORD env variable.
     */
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD', 'admin1234');

        User::updateOrCreate(
            ['email' => 'admin@wholesale.test'],
            [
                'name' => 'System Administrator',
                'company_name' => 'Wholesale Mobile Operations',
                'phone' => '+1-555-0100',
                'password' => Hash::make($password),
                'status' => 'approved',
                'is_admin' => true,
            ]
        );
    }
}
