@php
    $statusKey = $status ?? null;
    $statusLabel = $label ?? config('documate.statuses.' . $statusKey) ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', (string) $statusKey));
    $statusTone = match ($statusKey) {
        'pending_admin_approval' => 'warning',
        'approved_for_form_access' => 'primary',
        'for_signatory', 'for_notary' => 'info',
        'appointment_scheduled' => 'accent',
        'under_review' => 'neutral',
        'completed' => 'success',
        'rejected' => 'danger',
        default => 'neutral',
    };
@endphp

<span class="doc-status-badge doc-status-{{ $statusTone }}">
    {{ $statusLabel }}
</span>
