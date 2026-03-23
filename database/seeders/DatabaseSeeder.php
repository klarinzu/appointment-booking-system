<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\Category;
use App\Models\DocumateTransactionType;
use App\Models\Service;
use App\Models\StudentProfile;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (Schema::hasTable('settings') && Setting::count() === 0) {
            Setting::factory()->create();
        }

        $this->seedPermissionsAndRoles();
        $admin = $this->createCoreDocumateUsers();
        $this->seedDocumateTransactionTypes();

        if (Schema::hasTable('categories') && Category::count() === 0) {
            $this->createCategoriesAndServices($admin);
        }

        $this->call(DocumateExampleTransactionsSeeder::class);
    }

    protected function seedPermissionsAndRoles(): void
    {
        $permissions = [
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'settings.edit',
            'transactions.view',
            'transactions.submit',
            'transactions.review',
            'transactions.export',
            'clearances.tag',
            'handbook.manage',
            'chat.use',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $subscriberRole = Role::firstOrCreate(['name' => 'subscriber', 'guard_name' => 'web']);
        $administratorRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $studentOfficerRole = Role::firstOrCreate(['name' => 'student-officer', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $adminRole->syncPermissions(Permission::all());
        $administratorRole->syncPermissions(Permission::all());

        $moderatorPermissions = [
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
        ];

        $moderatorRole->syncPermissions(Permission::whereIn('name', $moderatorPermissions)->get());
        $employeeRole->syncPermissions(Permission::whereIn('name', ['appointments.view', 'appointments.edit'])->get());
        $subscriberRole->syncPermissions(Permission::whereIn('name', ['transactions.view', 'transactions.submit', 'chat.use'])->get());
        $studentRole->syncPermissions(Permission::whereIn('name', ['transactions.view', 'transactions.submit', 'chat.use'])->get());
        $studentOfficerRole->syncPermissions(Permission::whereIn('name', ['transactions.view', 'transactions.review', 'transactions.export', 'clearances.tag', 'chat.use'])->get());
    }

    protected function createCoreDocumateUsers(): User
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'DOCUMATE Administrator',
                'phone' => '1234567890',
                'status' => 1,
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
            ]
        );

        $admin->syncRoles(['admin', 'administrator']);

        Employee::firstOrCreate([
            'user_id' => $admin->id,
        ], [
            'days' => [
                "monday" => ["06:00-22:00"],
                "tuesday" => ["06:00-15:00", "16:00-22:00"],
                "wednesday" => ["09:00-12:00", "14:00-23:00"],
                "thursday" => ["09:00-20:00"],
                "friday" => ["06:00-17:00"],
                "saturday" => ["05:00-18:00"]
            ],
            'slot_duration' => 30
        ]);

        $officer = User::firstOrCreate(
            ['email' => 'officer@example.com'],
            [
                'name' => 'Student Officer',
                'phone' => '1234567891',
                'status' => 1,
                'email_verified_at' => now(),
                'password' => Hash::make('officer123'),
            ]
        );
        $officer->syncRoles(['student-officer']);

        $student = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'phone' => '1234567892',
                'status' => 1,
                'email_verified_at' => now(),
                'password' => Hash::make('student123'),
            ]
        );
        $student->syncRoles(['student']);

        StudentProfile::firstOrCreate(
            ['user_id' => $student->id],
            [
                'student_number' => '2026-0001',
                'course' => 'BS Information Technology',
                'college' => 'College of Computing',
                'year_level' => '3',
                'section' => 'A',
                'address' => 'Sample Student Address',
                'guardian_name' => 'Maria Student',
                'guardian_contact' => '09123456789',
                'emergency_contact' => '09123456780',
                'clearance_status' => 'cleared',
                'tagged_by' => $officer->id,
                'tagged_at' => now(),
            ]
        );

        return $admin;
    }

    protected function seedDocumateTransactionTypes(): void
    {
        foreach (config('documate.transaction_types', []) as $transactionType) {
            DocumateTransactionType::updateOrCreate(
                ['code' => $transactionType['code']],
                $transactionType
            );
        }
    }

    protected function createCategoriesAndServices(User $user)
    {
        $categories = [
            [
                'title' => 'Document Intake',
                'slug' => 'document-intake',
                'body' => 'Handles initial DOCUMATE submissions and front-desk transaction intake.'
            ],
            [
                'title' => 'Clearance Assistance',
                'slug' => 'clearance-assistance',
                'body' => 'Supports clearance validation and related student records processing.'
            ],
            [
                'title' => 'Student Affairs Support',
                'slug' => 'student-affairs-support',
                'body' => 'Supports student development requests, approvals, and office coordination.'
            ]
        ];

        $services = [];

        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData);

            switch ($category->title) {
                case 'Document Intake':
                    $services = [
                        [
                            'title' => 'Transaction Intake Support',
                            'slug' => 'transaction-intake-support',
                            'price' => 500,
                            'excerpt' => 'General support for receiving and processing student transaction requests.',
                            'status' => 1,
                        ],
                    ];
                    break;

                case 'Clearance Assistance':
                    $services = [
                        [
                            'title' => 'Clearance Evaluation',
                            'slug' => 'clearance-evaluation',
                            'price' => 1000,
                            'excerpt' => 'Validation support for clearance tagging and student accountability checks.',
                            'status' => 1,
                        ],
                    ];
                    break;

                case 'Student Affairs Support':
                    $services = [
                        [
                            'title' => 'Student Transaction Assistance',
                            'slug' => 'student-transaction-assistance',
                            'price' => 5000,
                            'excerpt' => 'Coordinated support for VPSD requests, schedules, and endorsements.',
                            'status' => 1,
                        ],
                    ];
                    break;
            }

            foreach ($services as $serviceData) {
                Service::create([
                    'title' => $serviceData['title'],
                    'slug' => $serviceData['slug'],
                    'price' => $serviceData['price'],
                    'excerpt' => $serviceData['excerpt'],
                    'category_id' => $category->id
                ]);
            }
        }

        if ($user->employee) {
            $allServices = Service::all();
            $user->employee->services()->sync($allServices->pluck('id'));
        }
    }
}
