@extends('layouts.auth')

@section('title', 'Entrar')

@section('content')

<div class="auth-header">
    <div class="auth-title">
        Bem-vindo de volta 👋
    </div>
    <p class="auth-subtitle">
        Entre na sua conta para continuar.
    </p>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ $errors->first() }}
</div>
@endif

@if (session('status'))
<div class="alert alert-success">
    <i class="fas fa-check-circle mr-1"></i>
    {{ session('status') }}
</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <label class="form-label">
            Email
        </label>
        <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" class="auth-input" placeholder="seu@email.com" value="{{ old('email') }}" required autofocus autocomplete="email">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">
            Palavra-passe
        </label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" class="auth-input" placeholder="Digite a sua palavra-passe" required autocomplete="current-password">
        </div>
    </div>

    <div class="remember-row">
        <label class="remember">
            <input type="checkbox" name="remember" value="1">
            Lembrar-me
        </label>

        @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="forgot-link">
            Esqueci a palavra-passe
        </a>
        @endif
    </div>

    <button type="submit" class="auth-button">
        <i class="fas fa-sign-in-alt"></i>
        Entrar
    </button>

</form>

<div class="auth-footer">
    Ainda não possui uma conta?
    @if (Route::has('register'))
    <a href="{{ route('register') }}">
        Criar conta
    </a>
    @endif
</div>

@endsection
