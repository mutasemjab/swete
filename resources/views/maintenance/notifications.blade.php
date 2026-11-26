@extends('layouts.app')
@section('title', __('maintenance.notifications'))
@section('breadcrumb', __('maintenance.notifications'))
@section('content')
<h1 class="page-title mb-5">{{ __('maintenance.notifications') }}</h1>
@forelse($notifications as $notification)
<div class="card p-5 mb-3 flex justify-between gap-4 {{ $notification->read_at ? '' : 'border-indigo-300' }}">
    <div><p>{{ $notification->message }}</p><small>{{ $notification->created_at->format('Y-m-d H:i') }}</small>
    @can('maintenance.view')
    <a class="text-indigo-600" href="{{ route('maintenance-contracts.show', $notification->data['contract_id']) }}">{{ __('maintenance.contract') }}</a>
    @endcan</div>
    @unless($notification->read_at)<form method="POST" action="{{ route('maintenance-notifications.read', $notification) }}">@csrf<button class="btn-secondary">{{ __('maintenance.mark_read') }}</button></form>@endunless
</div>
@empty
<p>{{ __('maintenance.no_notifications') }}</p>
@endforelse
{{ $notifications->links() }}
@endsection
