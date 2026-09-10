@extends('layouts.app')

@php 
$isEdit = isset($permission) && $permission;
@endphp

@section('title', $isEdit ? 'Editar permissão' : 'Nova permissão')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title"> {{ $isEdit ? 'Editar permissão' : 'Nova permissão' }} </h1>
        <div class="page-subtitle">
            {{ $isEdit
            ? 'Atualize as informações da permissão.'
            : 'Crie uma nova permissão para controlar o acesso às funcionalidades.'
        }}
        </div>
    </div>

    <div class="d-flex align-items-center" style="gap:8px;">
        <a href="{{ route('tenant.permissions.index') }}" class="btn btn-secondary btn-sm px-3">
            <i class="fas fa-arrow-left mr-1"></i>
            Voltar
        </a>
        <button type="submit" form="permission-form" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-save mr-1"></i>
            {{ $isEdit ? 'Guardar alterações' : 'Criar permissão' }}
        </button>
    </div>
</div> 
@endsection

@section('content')

<form id="permission-form" method="POST" action="{{ $isEdit ? route('tenant.permissions.update', $permission) : route('tenant.permissions.store') }}" data-ajax>
    @csrf
    @if ($isEdit)
    @method('PUT')
    @endif
    {{-- ALERTS --}}
    @if (session('success'))
    <div class="alert alert-success mb-4">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <div class="row">
        {{-- MAIN FORM --}}
        <div class="col-12 col-lg-12">
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Informações da permissão
                        </h3>
                        <div class="card-subtitle-modern">
                            Defina o nome e as informações básicas da permissão.
                        </div>
                    </div>
                </div>
                {{-- NAME --}}
                <div class="form-group">
                    <label class="form-label required">
                        Nome da permissão
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="text" name="name" class="auth-input @error('name') is-invalid @enderror" value="{{ old('name', $permission->name ?? '') }}" placeholder="Ex.: clients.view" maxlength="255" required>
                    </div>
                    @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                    <small style="display:block;margin-top:6px;color:var(--text-light);font-size:10px;">
                        Utilize um nome descritivo e único, por exemplo:
                        <strong style="font-weight:600;color:var(--text-muted);">
                            clients.view
                        </strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
