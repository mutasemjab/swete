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
            ['name' => 'علاء السويطي', 'email' => 'alaa@gmail.com', 'legacy_email' => 'alaa.suwaity@erp.local', 'role' => 'super-admin'],
            // العطاءات، X8، عروض الأسعار، العملاء، الموردون، المشاريع.
            ['name' => 'محمد الزعاترة', 'email' => 'm@gmail.com', 'legacy_email' => 'm@m.com', 'role' => 'project_manager'],
            // الصيانة وعروض الأسعار.
            ['name' => 'ثائر', 'email' => 'thaer@gmail.com', 'legacy_email' => 'thaer@erp.local', 'role' => 'maintenance_staff'],
            ['name' => 'محمد اللبان', 'email' => 'labban@gmail.com', 'legacy_email' => 'mohammad.lubban@erp.local', 'role' => 'accountant'],
            ['name' => 'رشدي', 'email' => 'roshdy@gmail.com', 'legacy_email' => 'roshdy@erp.local', 'role' => 'warehouse_officer'],
            // الطلبات الخارجية، المشاريع، العملاء، الموردون.
            ['name' => 'أم رسمي', 'email' => 'r@gmail.com', 'legacy_email' => 'r@r.com', 'role' => 'external_purchases_officer'],
            // نفس أقسام ثائر، مع الموافقة على طلبات الصيانة وتصنيف الزيارات.
            ['name' => 'أحمد الحومران', 'email' => 'homran@gmail.com', 'legacy_email' => 'ahmad.howmran@erp.local', 'role' => 'maintenance_manager', 'is_maintenance_manager' => true],
            // بوابة الفني لإنشاء الزيارات وتعبئة تقاريرها.
            ['name' => 'خليل', 'email' => 'khalil@gmail.com', 'legacy_email' => 'khalil@erp.local', 'role' => 'maintenance_technician'],
        ];

        foreach ($users as $data) {
            $user = User::where('email', $data['email'])->first();
            $legacyUser = User::where('email', $data['legacy_email'])->first();
            if ($user && $legacyUser && $user->id !== $legacyUser->id) {
                throw new \RuntimeException('Both current and legacy accounts exist for '.$data['name'].'. Resolve the duplicate before seeding.');
            }
            $user = $user ?? $legacyUser ?? new User();
            if (! $user->exists) {
                $user->password = Hash::make('password');
                $user->status = true;
            }

            $user->fill([
                'name' => $data['name'],
                'email' => $data['email'],
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
