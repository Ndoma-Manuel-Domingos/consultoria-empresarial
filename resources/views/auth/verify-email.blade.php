@extends('layouts.auth')

@section('title', 'Verificar email')

@section('content')

<div class="text-center">
    <div class="brand-logo mx-auto mb-4" style="background:#eff6ff;color:#2563eb;width:80px;height:80px;font-size:32px;">
        <i class="fas fa-envelope-open-text"></i>
    </div>
    <div class="auth-title">
        Verifique o seu email
    </div>
    <p class="auth-subtitle mt-2">
        Enviámos um link de confirmação para
        o endereço de email associado à sua conta.
    </p>
</div>

@if (session('status') == 'verification-link-sent')
<div class="alert alert-success mt-4">
    <i class="fas fa-check-circle mr-1"></i>
    Um novo link de verificação foi enviado
    para o seu email.
</div>

@endif

<div class="mt-4 p-3" style="background:#f8fafc;border-radius:12px;color:#64748b;font-size:13px;">
    <i class="fas fa-info-circle mr-1"></i>
    Não recebeu o email? Verifique também a
    pasta de spam ou lixo eletrónico.
</div>

<form method="POST" action="{{ route('verification.send') }}" class="mt-4">
    @csrf
    <button type="submit" class="auth-button">
        <i class="fas fa-paper-plane"></i>
        Reenviar email
    </button>
</form>

<div class="auth-footer">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-link text-muted">
            <i class="fas fa-sign-out-alt mr-1"></i>
            Sair
        </button>
    </form>
</div>

@endsection
