@extends('layouts.auth')

@section('title', 'Nova palavra-passe')

@section('content')

<div class="auth-header">
    <div class="auth-title">
        Criar nova palavra-passe
    </div>
    <p class="auth-subtitle">
        Escolha uma palavra-passe forte para proteger a sua conta.
    </p>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="form-group">
        <label class="form-label">
            Email
        </label>
        <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" class="auth-input" value="{{ old('email', $email) }}" required autocomplete="email">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">
            Nova palavra-passe
        </label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" class="auth-input" placeholder="Nova palavra-passe" required autocomplete="new-password">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">
            Confirmar palavra-passe
        </label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password_confirmation" class="auth-input" placeholder="Repita a palavra-passe" required autocomplete="new-password">
        </div>
    </div>

    <button type="submit" class="auth-button">
        <i class="fas fa-key"></i>
        Atualizar palavra-passe
    </button>

</form>

<div class="auth-footer">

    <a href="{{ route('login') }}">
        Voltar ao login
    </a>

</div>

@endsection
