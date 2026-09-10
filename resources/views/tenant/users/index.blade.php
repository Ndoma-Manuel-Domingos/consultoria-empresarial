@extends('layouts.app')

@section('title', 'Utilizadores')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Utilizadores
        </h1>
        <div class="page-subtitle">
            Gerencie os utilizadores e os perfis de acesso da sua organização.
        </div>
    </div>
    <div>
        <a href="{{ route('tenant.users.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-user-plus"></i>
            Novo utilizador
        </a>
    </div>
</div>
@endsection
@section('content')

{{-- ========== ALERTS =============== --}}
@if (session('success'))
<div class="alert alert-success mb-3">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger mb-3">
    <i class="fas fa-exclamation-circle"></i>
    <span>{{ $errors->first() }}</span>
</div>
@endif

{{-- =========  FILTERS ============= --}}
<div class="dashboard-card mb-4">
    <div class="p-3">
        <form id="users-filter-form" method="GET" action="{{ route('tenant.users.index') }}">
            <div class="row align-items-end">
                {{-- SEARCH --}}
                <div class="col-lg-5 col-md-6">
                    <div class="form-group mb-0">
                        <label class="form-label">
                            Pesquisar
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-search input-icon"></i>
                            <input type="text" name="search" id="users-search" class="auth-input" value="{{ request('search') }}" placeholder="Nome ou email..." autocomplete="off">
                        </div>
                    </div>
                </div>
                {{-- ROLE --}}
                <div class="col-lg-3 col-md-3">
                    <div class="form-group mb-0">
                        <label class="form-label">Perfil de acesso</label>
                        <select name="role" id="users-role" class="form-select">
                            <option value="">Todos os perfis</option>
                            @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- STATUS --}}
                <div class="col-lg-2 col-md-3">
                    <div class="form-group mb-0">
                        <label class="form-label">
                            Estado
                        </label>
                        <select name="status" id="users-status" class="form-select">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                </div>
                {{-- ACTIONS --}}
                <div class="col-lg-2 col-md-12 mt-3 mt-lg-0">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill" id="users-filter-button">
                            <i class="fas fa-filter"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('tenant.users.index') }}" class="btn btn-secondary btn-sm ml-2" title="Limpar filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== USERS CARD ============= --}}
<div class="dashboard-card">
    <div class="chart-card">
        {{-- HEADER --}}
        <div class="card-header-modern">
            <div>
                <h3 class="card-title-modern">
                    Lista de utilizadores
                </h3>
                <div class="card-subtitle-modern">
                    Utilizadores associados à organização atual.
                </div>
            </div>
            <div>
                <span class="status status-neutral">
                    {{ $users->total() }}
                    {{ $users->total() == 1 ? 'utilizador' : 'utilizadores' }}
                </span>
            </div>
        </div>
        {{-- TABLE --}}
        <div id="users-table-container" class="table-responsive">
            @include('tenant.users.partials.table')
        </div>
    </div>
</div>

{{-- ========== DELETE MODAL =========== --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <div class="stat-icon red mr-3">
                        <i class="fas fa-trash"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">
                            Eliminar utilizador
                        </h5>
                        <div style="
                                margin-top:3px;
                                color:#64748b;
                                font-size:11px;
                            ">
                            Esta ação requer confirmação.
                        </div>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="
                        padding:14px;
                        background:#fef2f2;
                        border:1px solid #fecaca;
                        border-radius:10px;
                    ">

                    <div style="
                            color:#991b1b;
                            font-size:12px;
                            font-weight:600;
                        ">
                        Tem certeza que deseja eliminar este utilizador?
                    </div>

                    <div id="delete-user-name" style="
                            margin-top:5px;
                            color:#64748b;
                            font-size:11px;
                        ">
                    </div>

                </div>

                <div style="
                        margin-top:12px;
                        color:#94a3b8;
                        font-size:10px;
                    ">
                    Se o utilizador pertencer a outras organizações,
                    será removida apenas a associação com a organização atual.
                </div>

            </div>


            <div class="modal-footer">

                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Cancelar
                </button>

                <button type="button" class="btn btn-outline-danger btn-sm" id="confirm-delete-user">
                    <i class="fas fa-trash mr-1"></i>
                    Sim, eliminar
                </button>

            </div>

        </div>

    </div>

</div>


{{-- ======= PAGE JAVASCRIPT ========= --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        let deleteUserUrl = null;

        /*
        |--------------------------------------------------------------------------
        | Mostrar modal de eliminação
        |--------------------------------------------------------------------------
        */

        $(document).on('click', '.delete-user-btn', function() {

            deleteUserUrl = $(this).data('url');

            const userName = $(this).data('name');

            $('#delete-user-name').html(
                'Utilizador: <strong>' +
                $('<div>').text(userName).html() +
                '</strong>'
            );

            $('#deleteUserModal').modal('show');
        });


        /*
        |--------------------------------------------------------------------------
        | Confirmar eliminação
        |--------------------------------------------------------------------------
        */

        $('#confirm-delete-user').on('click', function() {

            if (!deleteUserUrl) {
                return;
            }

            const button = $(this);

            button
                .prop('disabled', true)
                .html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i>' +
                    ' A eliminar...'
                );

            /*
            | O sistema global de AJAX/progress-bar que vamos
            | criar depois poderá assumir este loading.
            */

            $.ajax({

                url: deleteUserUrl,

                type: 'POST',

                data: {
                    _token: '{{ csrf_token() }}'
                    , _method: 'DELETE'
                },

                success: function(response) {

                    $('#deleteUserModal').modal('hide');

                    if (typeof showToast === 'function') {

                        showToast(
                            response.message || 'Utilizador eliminado com sucesso.'
                            , 'success'
                        );

                    } else {

                        alert(
                            response.message ||
                            'Utilizador eliminado com sucesso.'
                        );
                    }

                    loadUsers();

                },

                error: function(xhr) {

                    let message =
                        'Não foi possível eliminar o utilizador.';

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {
                        message = xhr.responseJSON.message;
                    }

                    if (typeof showToast === 'function') {

                        showToast(
                            message
                            , 'error'
                        );

                    } else {

                        alert(message);
                    }

                },

                complete: function() {

                    button
                        .prop('disabled', false)
                        .html(
                            '<i class="fas fa-trash mr-1"></i>' +
                            ' Sim, eliminar'
                        );

                    deleteUserUrl = null;
                }

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Pesquisa / filtros
        |--------------------------------------------------------------------------
        */

        $('#users-filter-form').on('submit', function(e) {

            e.preventDefault();

            loadUsers();

        });


        /*
        |--------------------------------------------------------------------------
        | Pesquisa ao escrever
        |--------------------------------------------------------------------------
        */

        let searchTimer = null;

        $('#users-search').on('input', function() {

            clearTimeout(searchTimer);

            searchTimer = setTimeout(function() {

                loadUsers();

            }, 450);

        });


        /*
        |--------------------------------------------------------------------------
        | Alteração dos filtros
        |--------------------------------------------------------------------------
        */

        $('#users-role, #users-status').on('change', function() {

            loadUsers();

        });


        /*
        |--------------------------------------------------------------------------
        | Paginação AJAX
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'click'
            , '#users-table-container .pagination a'
            , function(e) {

                e.preventDefault();

                const url = $(this).attr('href');

                loadUsers(url);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Carregar utilizadores
        |--------------------------------------------------------------------------
        */

        function loadUsers(url = null) {

            const form = $('#users-filter-form');

            const targetUrl =
                url || form.attr('action');

            const data =
                form.serialize();


            $('#users-table-container').css({
                opacity: '0.45'
                , pointerEvents: 'none'
            });


            $.ajax({

                url: targetUrl,

                type: 'GET',

                data: data,

                success: function(response) {

                    if (response.html) {

                        $('#users-table-container')
                            .html(response.html);

                    }

                },

                error: function(xhr) {

                    let message =
                        'Não foi possível carregar os utilizadores.';

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {
                        message = xhr.responseJSON.message;
                    }

                    if (typeof showToast === 'function') {

                        showToast(
                            message
                            , 'error'
                        );

                    } else {

                        console.error(message);
                    }

                },

                complete: function() {

                    $('#users-table-container').css({
                        opacity: '1'
                        , pointerEvents: 'auto'
                    });

                }

            });

        }

    });
</script>
@endpush
@endsection
