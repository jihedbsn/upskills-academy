<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Has full access to all features and settings',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Has access to most administrative features',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'instructor',
                'display_name' => 'Instructor',
                'description' => 'Can manage courses and students',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'moderator',
                'display_name' => 'Moderator',
                'description' => 'Can moderate content and users',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'content_manager',
                'display_name' => 'Content Manager',
                'description' => 'Can manage website content',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'support',
                'display_name' => 'Support',
                'description' => 'Can handle support tickets and student queries',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);

        // Create Permissions
        $permissions = [
            // User Management
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'group' => 'users', 'description' => 'Create, edit, delete users'],
            ['name' => 'view_users', 'display_name' => 'View Users', 'group' => 'users', 'description' => 'View user list and details'],
            ['name' => 'block_users', 'display_name' => 'Block Users', 'group' => 'users', 'description' => 'Block/unblock user accounts'],

            // Role Management
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'group' => 'roles', 'description' => 'Create, edit, delete roles'],
            ['name' => 'assign_roles', 'display_name' => 'Assign Roles', 'group' => 'roles', 'description' => 'Assign roles to users'],
            ['name' => 'manage_permissions', 'display_name' => 'Manage Permissions', 'group' => 'roles', 'description' => 'Manage role permissions'],

            // Course Management
            ['name' => 'manage_courses', 'display_name' => 'Manage Courses', 'group' => 'courses', 'description' => 'Create, edit, delete courses'],
            ['name' => 'view_courses', 'display_name' => 'View Courses', 'group' => 'courses', 'description' => 'View course list and details'],
            ['name' => 'manage_course_content', 'display_name' => 'Manage Course Content', 'group' => 'courses', 'description' => 'Manage videos, quizzes, exams'],

            // Student Management
            ['name' => 'manage_students', 'display_name' => 'Manage Students', 'group' => 'students', 'description' => 'Create, edit, delete students'],
            ['name' => 'view_students', 'display_name' => 'View Students', 'group' => 'students', 'description' => 'View student list and details'],
            ['name' => 'manage_enrollments', 'display_name' => 'Manage Enrollments', 'group' => 'students', 'description' => 'Enroll/unenroll students'],

            // Order Management
            ['name' => 'manage_orders', 'display_name' => 'Manage Orders', 'group' => 'orders', 'description' => 'Process and manage orders'],
            ['name' => 'view_orders', 'display_name' => 'View Orders', 'group' => 'orders', 'description' => 'View order list and details'],

            // Content Management
            ['name' => 'manage_articles', 'display_name' => 'Manage Articles', 'group' => 'content', 'description' => 'Create, edit, delete articles'],
            ['name' => 'manage_news', 'display_name' => 'Manage News', 'group' => 'content', 'description' => 'Create, edit, delete news'],
            ['name' => 'manage_resources', 'display_name' => 'Manage Resources', 'group' => 'content', 'description' => 'Manage resource library'],

            // Settings
            ['name' => 'manage_settings', 'display_name' => 'Manage Settings', 'group' => 'settings', 'description' => 'Manage system settings'],
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'group' => 'reports', 'description' => 'View analytics and reports'],
            ['name' => 'manage_coupons', 'display_name' => 'Manage Coupons', 'group' => 'marketing', 'description' => 'Create and manage discount coupons'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Assign permissions to roles
        $rolePermissions = [
            'super_admin' => 'all', // Super admin gets all permissions
            'admin' => [
                'view_users', 'manage_users', 'block_users',
                'view_courses', 'manage_courses', 'manage_course_content',
                'view_students', 'manage_students', 'manage_enrollments',
                'view_orders', 'manage_orders',
                'manage_articles', 'manage_news', 'manage_resources',
                'view_reports', 'manage_coupons',
            ],
            'instructor' => [
                'view_courses', 'manage_courses', 'manage_course_content',
                'view_students', 'view_orders',
            ],
            'moderator' => [
                'view_users', 'block_users',
                'view_courses', 'view_students',
                'manage_articles', 'manage_news',
            ],
            'content_manager' => [
                'manage_articles', 'manage_news', 'manage_resources',
                'view_courses', 'manage_course_content',
            ],
            'support' => [
                'view_users', 'view_students', 'view_courses', 'view_orders',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = DB::table('roles')->where('name', $roleName)->first();

            if ($perms === 'all') {
                // Assign all permissions to super admin
                $allPermissions = DB::table('permissions')->pluck('id');
                foreach ($allPermissions as $permId) {
                    DB::table('role_permission')->insert([
                        'role_id' => $role->id,
                        'permission_id' => $permId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Assign specific permissions
                foreach ($perms as $permName) {
                    $permission = DB::table('permissions')->where('name', $permName)->first();
                    if ($permission) {
                        DB::table('role_permission')->insert([
                            'role_id' => $role->id,
                            'permission_id' => $permission->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
