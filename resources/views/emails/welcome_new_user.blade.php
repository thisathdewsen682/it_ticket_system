@extends('emails.layout', [
    'headerColor' => '#2563eb',
    'headerColorDark' => '#1d4ed8',
    'accentColor' => '#2563eb',
])

@section('header')
    <h1>🎉 Welcome to IT Job Management System</h1>
    <p>Your account has been created</p>
@endsection

@section('content')
    <p class="greeting">Hello <strong>{{ $user->name }}</strong>,</p>

    <p class="message">
        An account has been created for you on the IT Job Management System. Please find your login credentials below.
    </p>

    <div class="info-card">
        <h3>🔑 Your Login Credentials</h3>

        <div class="info-row">
            <span class="info-label">Name:</span>
            <span class="info-value"><strong>{{ $user->name }}</strong></span>
        </div>

        <div class="info-row">
            <span class="info-label">Employee No:</span>
            <span class="info-value"><strong>{{ $user->employee_no }}</strong></span>
        </div>

        @if($user->email)
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $user->email }}</span>
        </div>
        @endif

        <div class="info-row">
            <span class="info-label">Role:</span>
            <span class="info-value">
                <span class="badge badge-normal">{{ $user->role?->name ?? 'N/A' }}</span>
            </span>
        </div>

        <div class="info-row">
            <span class="info-label">Temporary Password:</span>
            <span class="info-value" style="font-family: monospace; font-size: 16px; font-weight: 700; color: #dc2626; letter-spacing: 1px;">{{ $temporaryPassword }}</span>
        </div>
    </div>

    <div class="alert-box">
        <p><strong>⚠️ Important:</strong> You will be required to change your password when you log in for the first time. Please choose a strong, unique password.</p>
    </div>

    <div class="button-container">
        <a href="{{ url('/login') }}" class="button button-primary">
            Log In Now →
        </a>
    </div>

    <div class="divider"></div>

    <p class="message" style="font-size: 13px; color: #6b7280;">
        If you did not expect this email or believe it was sent in error, please contact your system administrator.
    </p>
@endsection
