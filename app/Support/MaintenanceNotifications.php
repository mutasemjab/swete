<?php

namespace App\Support;

use App\Models\MaintenanceContract;
use App\Models\MaintenanceNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MaintenanceNotifications
{
    public const KINDS = ['visit_reminder', 'emergency_limit'];

    public static function record(string $kind, string $key, array $data): void
    {
        $userIds = DB::table('maintenance_notification_recipients')->where('kind', $kind)->pluck('user_id');
        foreach (User::whereIn('id', $userIds)->where('status', true)->pluck('id') as $userId) {
            DB::table('maintenance_notifications')->insertOrIgnore([
                'user_id' => $userId, 'dedupe_key' => $key, 'kind' => $kind,
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public static function checkEmergencyLimit(MaintenanceContract $contract): void
    {
        if ($contract->emergency_visit_limit === null) {
            return;
        }
        $used = $contract->emergencyVisits()->count();
        if ($used > 0 && $used >= $contract->emergency_visit_limit) {
            self::record('emergency_limit', 'emergency:'.$contract->id.':'.$contract->emergency_visit_limit, [
                'contract_id' => $contract->id, 'number' => $contract->number,
                'limit' => $contract->emergency_visit_limit, 'used' => $used,
            ]);
        }
    }
}
