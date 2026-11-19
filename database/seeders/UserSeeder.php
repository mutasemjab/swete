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
            ['name' => 'علاء السويطي', 'email' => 'alaa@gmail.com', 'role' => 'super-admin'],
            // العطاءات، X8، عروض الأسعار، العملاء، الموردون، المشاريع.
            ['name' => 'محمد الزعاترة', 'email' => 'm@gmail.com', 'role' => 'project_manager'],
            // الصيانة وعروض الأسعار.
            ['name' => 'ثائر', 'email' => 'thaer@gmail.com', 'role' => 'maintenance_staff'],
            ['name' => 'محمد اللبان', 'email' => 'labban@gmail.com', 'role' => 'accountant'],
            ['name' => 'رشدي', 'email' => 'roshdy@gmail.com', 'role' => 'warehouse_officer'],
            // الطلبات الخارجية، المشاريع، العملاء، الموردون.
            ['name' => 'أم رسمي', 'email' => 'r@gmail.com', 'role' => 'external_purchases_officer'],
            // نفس أقسام ثائر، مع الموافقة على طلبات الصيانة وتصنيف الزيارات.
            ['name' => 'أحمد الحومران', 'email' => 'homran@gmail.com', 'role' => 'maintenance_manager', 'is_maintenance_manager' => true],
            // بوابة الفني لإنشاء الزيارات وتعبئة تقاريرها.
            ['name' => 'خليل', 'email' => 'khalil@gmail.com', 'role' => 'maintenance_technician'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrNew(['email' => $data['email']]);
            if (! $user->exists) {
                $user->password = Hash::make('password');
                $user->status = true;
            }

            $user->fill([
                'name' => $data['name'],
                'is_maintenance_manager' => $data['is_maintenance_manager'] ?? false,
            ])->save();

            $user->syncRoles([$data['role']]);
            // Remove old direct grants so the role defines the complete access scope.
            $user->syncPermissions([]);
        }

        $this->command?->info('✓ Users and roles synchronized ('.count($users).' users).');
        $this->command?->line('  Default password for new users: password. Existing passwords are preserved.');
    }
}
