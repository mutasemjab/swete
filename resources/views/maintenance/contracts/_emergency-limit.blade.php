@if(\App\Support\MaintenanceFeatures::ready())
<div>
    <label class="form-label">{{ __('maintenance.emergency_visit_limit') }}</label>
    <input class="form-input" type="number" name="emergency_visit_limit" min="0" max="100000" value="{{ old('emergency_visit_limit', $contract?->emergency_visit_limit) }}">
    <p class="text-xs text-slate-400 mt-1">{{ __('maintenance.emergency_visit_limit_hint') }}</p>
    @error('emergency_visit_limit')<p class="form-error">{{ $message }}</p>@enderror
</div>
@endif
