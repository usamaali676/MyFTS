@extends('layouts.dashboard')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Notifications /</span> All</h4>

        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <h5 class="card-title mb-0">All Notifications</h5>
                <span class="badge rounded-pill bg-label-primary">{{ auth()->user()->unreadNotifications->count() }} New</span>
            </div>

            <ul class="list-group list-group-flush">
                @forelse ($notifications as $notification)
                    <li class="list-group-item list-group-item-action dropdown-notifications-item @if(is_null($notification->read_at)) notif-unread @endif"
                        data-notification-id="{{ $notification->id }}"
                        data-lead-id="{{ $notification->data['lead_id'] ?? '' }}"
                        data-lead-name="{{ $notification->data['lead_name'] ?? '' }}">
                        <div class="d-flex gap-2">
                            <div class="flex-shrink-0">
                                <div class="avatar me-1">
                                    <img src="../../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                                </div>
                            </div>
                            <div class="d-flex flex-column flex-grow-1 overflow-hidden">
                                <h6 class="mb-1">{{ $notification->data['title'] ?? 'New Notification' }} 🎉</h6>
                                <small class="text-truncate text-body">Added by: {{
                                    \App\Models\User::find($notification->data['added_by'] ?? null)->name ?? 'Unknown User' }}</small>
                            </div>
                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                <small class="text-muted">{{ \App\Helpers\GlobalHelper::timeAgoShort($notification->created_at) }}</small>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">
                        <p class="text-muted mb-0 text-center py-4">No notifications yet.</p>
                    </li>
                @endforelse
            </ul>

            @if($notifications->hasPages())
                <div class="card-body border-top">
                    {{ $notifications->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('custom-js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.rsInitNotifications) {
            rsInitNotifications({
                selector: '.dropdown-notifications-item[data-notification-id]',
                readUrlTemplate: "{{ route('front.notifications.read', ['id' => 'NOTIF_ID']) }}",
                leadEditUrlTemplate: "{{ route('lead.edit', ['id' => 'LEAD_ID']) }}",
                leadIndexUrl: "{{ route('lead.index') }}",
                onRead: function (count) {
                    var badge = document.querySelector('.card-header .badge');
                    if (badge) badge.textContent = count + ' New';
                }
            });
        }
    });
</script>
@endsection
