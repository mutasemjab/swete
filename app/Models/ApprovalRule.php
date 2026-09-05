<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Admin-configured policy: "this route requires approval from one of these users."
 * Enforced generically by App\Http\Middleware\EnforceApprovalRules for every
 * matching store/update/destroy request — no per-controller code needed.
 */
class ApprovalRule extends Model
{
    /** Route names that can never be gated, so the mechanism can't deadlock itself. */
    public const EXCLUDED_ROUTE_PREFIXES = ['auth.', 'approvals.', 'lang.', 'settings.approval-rules.', 'passport.'];

    protected $fillable = [
        'route_name',
        'label',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function approvers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'approval_rule_approvers');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Could this route ever be gated — a create/edit/delete action, not on the exclusion list? */
    public static function isGateableRouteName(?string $routeName): bool
    {
        return $routeName
            && Str::endsWith($routeName, ['.store', '.update', '.destroy'])
            && ! Str::startsWith($routeName, self::EXCLUDED_ROUTE_PREFIXES);
    }
}
