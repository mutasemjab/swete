@extends('layouts.app')

@section('title', __('settings.approval_rules_list'))
@section('breadcrumb', __('settings.approval_rules'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.approval_rules_list') }}</h1>
        <p class="page-subtitle">{{ __('settings.approval_rules_subtitle') }}</p>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-user-shield text-indigo-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $rules->where('status', true)->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('settings.active_approval_rules') }}</p>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.approval_rule_route') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.approval_rule_label') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.approval_rule_approvers') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.approval_rule_enabled') }}</th>
                    <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($routeNames as $routeName)
                @php $rule = $rules->get($routeName); @endphp
                <tr>
                    <form action="{{ route('settings.approval-rules.store') }}" method="POST" class="contents">
                        @csrf
                        <input type="hidden" name="route_name" value="{{ $routeName }}">
                        <td class="px-5 py-3">
                            <span class="font-mono text-sm text-slate-700 bg-slate-100 px-2 py-1 rounded-lg">{{ $routeName }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <input type="text" name="label" value="{{ $rule?->label }}"
                                   placeholder="{{ __('settings.approval_rule_label_placeholder') }}"
                                   class="form-input !py-1.5 !text-sm w-40">
                        </td>
                        <td class="px-5 py-3">
                            <select name="approver_ids[]" multiple class="form-select !py-1.5 !text-sm min-w-40" size="2">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected($rule && $rule->approvers->contains('id', $user->id))>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-5 py-3">
                            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox" name="status" value="1" class="sr-only peer" @checked($rule?->status)>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                            peer-checked:bg-indigo-600 transition-all
                                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                            after:bg-white after:rounded-full after:h-5 after:w-5
                                            after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </td>
                        <td class="px-5 py-3 text-end">
                            <button type="submit" class="btn-secondary btn-sm">
                                <i class="fa-solid fa-floppy-disk"></i>
                                {{ __('app.save') }}
                            </button>
                        </td>
                    </form>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
