<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // علاء السويطي — super-admin
            [
                'name'                   => 'علاء السويطي',
                'email'                  => 'alaa.suwaity@erp.local',
                'password'               => Hash::make('password'),
                'status'                 => true,
                'is_maintenance_manager' => false,
                'role'                   => 'super-admin',
                'permissions'            => [],
            ],

            // محمد الزعاترة — مدير المشاريع
            [
                'name'                   => 'محمد الزعاترة',
                'email'                  => 'm@m.com',
                'password'               => Hash::make('password'),
                'status'                 => true,
                'is_maintenance_manager' => false,
                'role'                   => null,
                'permissions'            => [
                    'tenders.view', 'tenders.create', 'tenders.edit',
                    'tenders.price_quotes.create', 'tenders.price_quotes.edit',
                    'clients.view',
                    'clients.customers.create', 'clients.customers.edit',
                    'clients.suppliers.create', 'clients.suppliers.edit',
                    'clients.customer_groups.create', 'clients.customer_groups.edit',
                    'clients.supplier_groups.create', 'clients.supplier_groups.edit',
                    'projects.view', 'projects.edit',
                    'quotations.view',
                    'quotations.price_quotes.create', 'quotations.price_quotes.edit',
                ],
            ],

            // ثائر — صيانة وعروض أسعار
            [
                'name'                   => 'ثائر',
                'email'                  => 'thaer@erp.local',
                'password'               => Hash::make('password'),
                'status'                 => true,
                'is_maintenance_manager' => false,
                'role'                   => null,
                'permissions'            => [
                    'maintenance.view',
                    'maintenance.reports.create', 'maintenance.reports.edit',
                    'quotations.view',
                    'quotations.price_quotes.create', 'quotations.price_quotes.edit',
                ],
            ],

            // محمد اللبان — محاسبة فقط
            [
                'name'                   => 'محمد اللبان',
                'email'                  => 'mohammad.lubban@erp.local',
                'password'               => Hash::make('password'),
                'status'                 => true,
                'is_maintenance_manager' => false,
                'role'                   => 'accountant',
                'permissions'            => [],
            ],

            // رشدي — مستودعات فقط
            [
                'name'                   => 'رشدي',
                'email'                  => 'roshdy@erp.local',
                'password'               => Hash::make('password'),
                'status'                 => true,
                'is_maintenance_manager' => false,
                'role'                   => 'warehouse_officer',
                'permissions'            => [],
            ],

            // أم رسمي — مشتريات خارجية ومشاريع
            [
                'name'                   => 'أم رسمي',
                'email'                  => 'r@r.com',
                'password'               => Hash::make('password'),
                'status'                 => true,
                'is_maintenance_manager' => false,
                'role'                   => null,
                'permissions'            => [
                    'external_purchases.view',
                    'external_purchases.purchase_requests.create',
                    'external_purchases.purchase_requests.edit',
                    'projects.view', 'projects.edit',
                ],
            ],

            // أحمد الحومران — مدير الصيانة
            [
                'name'                   => 'أحمد الحومران',
                'email'                  => 'ahmad.howmran@erp.local',
                'password'               => Hash::make('password'),
                'status'                 => true,
                'is_maintenance_manager' => true,
                'role'                   => null,
                'permissions'            => [
                    'maintenance.view',
                    'maintenance.reports.create', 'maintenance.reports.edit',
                    'maintenance.reports.approve_materials',
                    'maintenance.contracts.create', 'maintenance.contracts.edit',
                    'maintenance.requests.manage', 'maintenance.visits.manage',
                    'maintenance.contract_payments.create',
                    'maintenance.contract_scheduled_visits.create',
                ],
            ],
        ];

        foreach ($users as $data) {
            $role        = $data['role'];
            $permissions = $data['permissions'];

            unset($data['role'], $data['permissions']);

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                $data,
            );

            if (! $user->wasRecentlyCreated) {
                $user->update($data);
            }

            if ($role) {
                $user->syncRoles([$role]);
                $user->syncPermissions([]);
            } else {
                $user->syncRoles([]);
                $user->syncPermissions($permissions);
            }
        }

        $this->command->info('✓ Users seeded (7 users).');
        $this->command->line('  Default password for all: password');
    }
}
