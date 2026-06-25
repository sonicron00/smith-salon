<h2>Salon booking {{ $actionLabel }}</h2>

<p><strong>Client:</strong> {{ $appointment->customer_name }}</p>
<p><strong>Phone:</strong> {{ $appointment->customer_phone }}</p>

@if($appointment->customer_email)
    <p><strong>Email:</strong> {{ $appointment->customer_email }}</p>
@endif

@if($appointment->customer_message)
    <p><strong>Customer message:</strong></p>
    <p>{{ $appointment->customer_message }}</p>
@endif

<p><strong>Service:</strong> {{ $appointment->service->name }}</p>
<p><strong>Staff:</strong> {{ $appointment->staff->name }}</p>
<p><strong>When:</strong> {{ $appointment->starts_at->format('D j M Y H:i') }}</p>
<p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>

<p>
    <strong>Manage link:</strong>
    <a href="{{ $appointment->manageUrl() }}">{{ $appointment->manageUrl() }}</a>
</p>

@if($appointment->cancellation_reason)
    <p><strong>Cancellation reason:</strong> {{ $appointment->cancellation_reason }}</p>
@endif