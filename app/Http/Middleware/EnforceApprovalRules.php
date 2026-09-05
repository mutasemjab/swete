<?php

namespace App\Http\Middleware;

use App\Models\ApprovalRule;
use App\Models\PendingAction;
use App\Services\ApprovalService;
use Closure;
use Illuminate\Http\Request;

/**
 * Generic "maker-checker" gate: if an enabled ApprovalRule exists for the current
 * route, the request is deferred into a PendingAction instead of reaching its
 * controller. No per-controller code required — see PendingAction::onApproved()
 * for how an approved action is actually carried out later.
 */
class EnforceApprovalRules
{
    public function handle(Request $request, Closure $next)
    {
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if (! ApprovalRule::isGateableRouteName($routeName)) {
            return $next($request);
        }

        $rule = ApprovalRule::with('approvers')->where('route_name', $routeName)->where('status', true)->first();

        if (! $rule || $rule->approvers->isEmpty()) {
            return $next($request);
        }

        $pendingAction = PendingAction::create([
            'route_name'   => $routeName,
            'method'       => $request->method(),
            'url'          => $request->getPathInfo(),
            'payload'      => $request->except(['_token', '_method']),
            'requested_by' => $request->user()->id,
            'status'       => 'pending',
        ]);

        app(ApprovalService::class)->requestToMany($pendingAction, $rule->approvers, 'default');

        return redirect()->back()->with('success', __('approvals.action_pending'));
    }
}
