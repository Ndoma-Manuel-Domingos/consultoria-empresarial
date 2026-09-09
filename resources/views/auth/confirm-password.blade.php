@extends('layouts.auth')

@section('title', 'Confirmar palavra-passe')

@section('content')

<div class="auth-header text-center">

    <div class="brand-logo mx-auto mb-3" style="background:#eff6ff;color:#2563eb;">
        <i class="fas fa-shield-alt"></i>
    </div>

    <div class="auth-title">
        Confirme a sua identidade
    </div>

    <p class="auth-subtitle">
        Por motivos de segurança, confirme a sua
        palavra-passe antes de continuar.
    </p>

</div>

@if ($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="form-group">

        <label class="form-label">
            Palavra-passe
        </label>

        <div class="input-wrapper">

            <i class="fas fa-lock input-icon"></i>

            <input type="password" name="password" class="auth-input" placeholder="Digite a sua palavra-passe" required autofocus autocomplete="current-password">

        </div>

    </div>

    <button type="submit" class="auth-button">
        <i class="fas fa-check"></i>
        Confirmar e continuar
    </button>

</form>

<div class="auth-footer">

    @if (Route::has('password.request'))

    <a href="{{ route('password.request') }}">
        Esqueci-me da palavra-passe
    </a>

    @endif

</div>

@endsection
