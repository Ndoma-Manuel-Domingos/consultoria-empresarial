@extends('layouts.auth')

@section('title', 'Recuperar palavra-passe')
@section('content')
<div class="auth-header">
    <div class="auth-title">
        Recuperar acesso
    </div>
    <p class="auth-subtitle">
        Não se preocupe. Informe o seu email e
        enviaremos um link para redefinir a sua palavra-passe.
    </p>
</div>

@if (session('status'))
<div class="alert alert-success">
    <i class="fas fa-check-circle mr-1"></i>
    {{ session('status') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="form-group">
        <label class="form-label">
            Endereço de email
        </label>
        <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" class="auth-input" placeholder="seu@email.com" value="{{ old('email') }}" required autofocus autocomplete="email">
        </div>
    </div>

    <button type="submit" class="auth-button">
        <i class="fas fa-paper-plane"></i>
        Enviar link de recuperação
    </button>

</form>

<div class="auth-footer">
    <a href="{{ route('login') }}">
        <i class="fas fa-arrow-left mr-1"></i>
        Voltar ao login
    </a>
</div>

@endsection
