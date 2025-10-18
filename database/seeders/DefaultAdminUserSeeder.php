<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DefaultAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super_admin role exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        if (!$superAdminRole) {
            $this->command->error('Super admin role not found. Please run RolesAndPermissionsSeeder first.');
            return;
        }

        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@heac.gov.jo')->first();
        
        if ($existingAdmin) {
            $this->command->warn('Admin user already exists with email: admin@heac.gov.jo');
            return;
        }

        // Create default super admin user
        $admin = User::create([
            'name' => 'HEAC Administrator',
            'email' => 'admin@heac.gov.jo',
            'password' => Hash::make('HeacAdmin2025!'),
            'email_verified_at' => now(),
        ]);

        // Assign super_admin role
        $admin->assignRole('super_admin');

        $this->command->info('Default admin user created successfully!');
        $this->command->info('Email: admin@heac.gov.jo');
        $this->command->warn('Password: HeacAdmin2025! (Please change this immediately after first login)');
    }
}
