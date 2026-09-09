@extends('layouts.auth')

@section('title', 'Criar conta')

@section('content')

<div class="auth-header">
    <div class="auth-title">
        Criar a sua conta
    </div>
    <p class="auth-subtitle">
        Preencha os dados abaixo para começar.
    </p>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <label class="form-label">
            Nome da empresa
        </label>
        <div class="input-wrapper">
            <i class="fas fa-building input-icon"></i>
            <input type="text" name="tenant_name" class="auth-input" placeholder="Nome da empresa" value="{{ old('tenant_name') }}" required autofocus autocomplete="organization">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">
            Slug
        </label>
        <div class="input-wrapper">
            <i class="fas fa-globe input-icon"></i>
            <input type="text" name="slug" class="auth-input" placeholder="Slug" value="{{ old('slug') }}" required autocomplete="organization">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">
            Nome completo
        </label>
        <div class="input-wrapper">
            <i class="fas fa-user input-icon"></i>
            <input type="text" name="name" class="auth-input" placeholder="Seu nome" value="{{ old('name') }}" required autofocus autocomplete="name">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">
            Email
        </label>
        <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" class="auth-input" placeholder="seu@email.com" value="{{ old('email') }}" required autocomplete="email">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">
            Palavra-passe
        </label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" class="auth-input" placeholder="Crie uma palavra-passe" required autocomplete="new-password">
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
        <i class="fas fa-user-plus"></i>
        Criar conta
    </button>
</form>

<div class="auth-footer">
    Já possui uma conta?
    <a href="{{ route('login') }}">
        Entrar
    </a>
</div>

@endsection
