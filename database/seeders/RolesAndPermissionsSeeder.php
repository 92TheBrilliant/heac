<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for Pages
        $pagePermissions = [
            'view_pages',
            'create_pages',
            'edit_pages',
            'delete_pages',
            'publish_pages',
        ];

        // Create permissions for Research
        $researchPermissions = [
            'view_research',
            'create_research',
            'edit_research',
            'delete_research',
            'publish_research',
            'download_research',
        ];

        // Create permissions for Media
        $mediaPermissions = [
            'view_media',
            'upload_media',
            'edit_media',
            'delete_media',
        ];

        // Create permissions for Categories and Tags
        $taxonomyPermissions = [
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            'view_tags',
            'create_tags',
            'edit_tags',
            'delete_tags',
        ];

        // Create permissions for Contact Inquiries
        $inquiryPermissions = [
            'view_contact_inquiries',
            'edit_contact_inquiries',
            'delete_contact_inquiries',
            'respond_contact_inquiries',
        ];

        // Create permissions for Users
        $userPermissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',
        ];

        // Create permissions for Analytics
        $analyticsPermissions = [
            'view_analytics',
            'view_dashboard',
        ];

        // Create permissions for Settings
        $settingsPermissions = [
            'view_settings',
            'edit_settings',
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $pagePermissions,
            $researchPermissions,
            $mediaPermissions,
            $taxonomyPermissions,
            $inquiryPermissions,
            $userPermissions,
            $analyticsPermissions,
            $settingsPermissions
        );

        // Create all permissions (skip if already exists)
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Create roles and assign permissions

        // Super Admin - Full access to everything
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Admin - Can manage content, research, media, and view analytics
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            // Pages
            'view_pages',
            'create_pages',
            'edit_pages',
            'delete_pages',
            'publish_pages',
            // Research
            'view_research',
            'create_research',
            'edit_research',
            'delete_research',
            'publish_research',
            'download_research',
            // Media
            'view_media',
            'upload_media',
            'edit_media',
            'delete_media',
            // Taxonomies
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            'view_tags',
            'create_tags',
            'edit_tags',
            'delete_tags',
            // Inquiries
            'view_contact_inquiries',
            'edit_contact_inquiries',
            'respond_contact_inquiries',
            // Analytics
            'view_analytics',
            'view_dashboard',
        ]);

        // Editor - Can create and edit content but not delete or manage users
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions([
            // Pages
            'view_pages',
            'create_pages',
            'edit_pages',
            // Research
            'view_research',
            'create_research',
            'edit_research',
            'download_research',
            // Media
            'view_media',
            'upload_media',
            'edit_media',
            // Taxonomies
            'view_categories',
            'create_categories',
            'edit_categories',
            'view_tags',
            'create_tags',
            'edit_tags',
            // Inquiries
            'view_contact_inquiries',
            'respond_contact_inquiries',
            // Analytics
            'view_dashboard',
        ]);

        // Viewer - Read-only access
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'view_pages',
            'view_research',
            'download_research',
            'view_media',
            'view_categories',
            'view_tags',
            'view_contact_inquiries',
            'view_dashboard',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Roles: super_admin, admin, editor, viewer');
        $this->command->info('Total permissions: ' . count($allPermissions));
    }
}
