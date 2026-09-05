<?php

namespace App\Models;

use App\Models\Concerns\HasApprovals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\ImplicitRouteBinding;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * A deferred create/update/delete, waiting on approval per an ApprovalRule.
 * Once approved, replays the original request through Laravel's own
 * ControllerDispatcher so the target controller's normal validation and
 * business logic run completely unmodified — see onApproved().
 */
class PendingAction extends Model
{
    use HasApprovals;

    protected $fillable = [
        'route_name',
        'method',
        'url',
        'payload',
        'requested_by',
        'status',
        'error_message',
        'executed_at',
    ];

    protected $casts = [
        'payload'     => 'array',
        'executed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvalLabel(): string
    {
        $verb = match (true) {
            str_ends_with($this->route_name, '.store')   => __('approvals.action_verb_create'),
            str_ends_with($this->route_name, '.update')   => __('approvals.action_verb_update'),
            str_ends_with($this->route_name, '.destroy')  => __('approvals.action_verb_delete'),
            default => '',
        };

        return trim("{$verb} — {$this->route_name}", ' —');
    }

    /** Called by ApprovalService right after one of the eligible approvers approves. */
    public function onApproved(): void
    {
        $route = app('router')->getRoutes()->getByName($this->route_name);

        if (! $route) {
            $this->update(['status' => 'failed', 'error_message' => "Route [{$this->route_name}] no longer exists."]);
            return;
        }

        $request = Request::create($this->url, $this->method, $this->payload ?? []);
        $request->setUserResolver(fn () => $this->requester);

        $previousRequest = app('request');
        $previousUser     = Auth::user();

        try {
            $route->bind($request);
            ImplicitRouteBinding::resolveForRoute(app(), $route);

            app()->instance('request', $request);
            $request->setRouteResolver(fn () => $route);
            Auth::setUser($this->requester);

            app(ControllerDispatcher::class)->dispatch(
                $route,
                $route->getController(),
                $route->getActionMethod()
            );

            $this->update(['status' => 'executed', 'executed_at' => now()]);
        } catch (\Throwable $e) {
            $this->update(['status' => 'failed', 'error_message' => Str::limit($e->getMessage(), 1000)]);
        } finally {
            app()->instance('request', $previousRequest);
            Auth::setUser($previousUser);
        }
    }

    /** Called by ApprovalService right after one of the eligible approvers rejects. */
    public function onRejected(): void
    {
        $this->update(['status' => 'rejected']);
    }
}
