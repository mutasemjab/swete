<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\ApprovalRule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;

class ApprovalRuleController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $routeNames = collect(RouteFacade::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn ($name) => ApprovalRule::isGateableRouteName($name))
            ->unique()
            ->sort()
            ->values();

        $rules = ApprovalRule::with('approvers')->get()->keyBy('route_name');
        $users = User::where('status', true)->orderBy('name')->get();

        return $this->moduleView('settings.approval-rules.index', compact('routeNames', 'rules', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_name'      => ['required', 'string'],
            'label'           => ['nullable', 'string', 'max:150'],
            'status'          => ['boolean'],
            'approver_ids'    => ['array'],
            'approver_ids.*'  => ['exists:users,id'],
        ]);

        if (! ApprovalRule::isGateableRouteName($validated['route_name'])) {
            abort(422);
        }

        $rule = ApprovalRule::updateOrCreate(
            ['route_name' => $validated['route_name']],
            [
                'label'      => $validated['label'] ?? null,
                'status'     => $request->boolean('status'),
                'created_by' => $request->user()->id,
            ]
        );

        $rule->approvers()->sync($validated['approver_ids'] ?? []);

        return redirect()->route('settings.approval-rules.index')
            ->with('success', __('settings.approval_rule_saved'));
    }
}
