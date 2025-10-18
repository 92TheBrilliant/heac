# Database Seeding Guide

This guide explains how to use the database seeders to populate your HEAC CMS with initial data.

## Available Seeders

### 1. RolesAndPermissionsSeeder
Creates the role-based access control system with four roles and comprehensive permissions.

**Roles Created:**
- `super_admin` - Full system access
- `admin` - Content and research management
- `editor` - Content editing only
- `viewer` - Read-only access

**Permissions:** 40+ permissions covering pages, research, media, users, analytics, and settings.

### 2. DefaultAdminUserSeeder
Creates a default super admin user account for initial system access.

**Default Credentials:**
- Email: `admin@heac.gov.jo`
- Password: `HeacAdmin2025!`

**⚠️ IMPORTANT:** Change this password immediately after first login!

### 3. SampleContentSeeder
Populates the database with sample content for testing and demonstration.

**Creates:**
- 5 Categories (Quality Assurance, Accreditation Standards, etc.)
- 10 Tags (Quality Standards, Best Practices, etc.)
- 5 Pages (About, Contact, Accreditation Process, etc.)
- 6 Research Publications with authors, categories, and tags

## Running Seeders

### Seed Everything
To run all seeders at once:
```bash
php artisan db:seed
```

### Seed Individual Seeders
To run specific seeders:
```bash
# Roles and permissions only
php artisan db:seed --class=RolesAndPermissionsSeeder

# Default admin user only
php artisan db:seed --class=DefaultAdminUserSeeder

# Sample content only
php artisan db:seed --class=SampleContentSeeder
```

### Fresh Migration with Seeding
To reset the database and seed:
```bash
php artisan migrate:fresh --seed
```

## Post-Seeding Steps

1. **Login to Admin Panel**
   - Navigate to `/admin`
   - Use the default credentials above
   - Change the password immediately

2. **Review Sample Content**
   - Check the pages in the admin panel
   - Review research publications
   - Verify categories and tags

3. **Customize Content**
   - Update sample pages with real content
   - Add your own research publications
   - Modify categories and tags as needed

4. **Create Additional Users**
   - Add team members with appropriate roles
   - Assign permissions based on responsibilities

## Security Notes

- The default admin password is intentionally strong but should be changed
- The seeder checks if an admin user already exists to prevent duplicates
- All passwords are hashed using Laravel's secure bcrypt hashing
- Consider disabling the DefaultAdminUserSeeder in production after initial setup

## Troubleshooting

**Error: "Super admin role not found"**
- Run RolesAndPermissionsSeeder first: `php artisan db:seed --class=RolesAndPermissionsSeeder`

**Error: "Admin user already exists"**
- This is expected if you've already run the seeder
- The seeder will skip creating a duplicate user

**Error: Foreign key constraint fails**
- Ensure migrations are run before seeding: `php artisan migrate`
- Run seeders in order: Roles → Admin User → Sample Content
