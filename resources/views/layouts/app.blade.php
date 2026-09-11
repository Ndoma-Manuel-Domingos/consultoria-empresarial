<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', config('app.name', 'Sistema de Consultoria'))
    </title>

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    {{-- AdminLTE --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- CSS principal --}}
    <link rel="stylesheet" href="{{ asset('css/progress.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')

</head>


<body class="hold-transition sidebar-mini layout-fixed">
    <div x-data="ajaxSystem()" x-init="init()" x-cloak>
        {{-- ======== AJAX PROGRESS BAR ======== --}}
        <div class="ajax-progress" x-show="progress.visible" x-transition.opacity>
            <div class="ajax-progress-bar" :style="'width:' + progress.value + '%'"></div>
        </div>

        {{--======== AJAX TOAST ======= --}}

        <div class="ajax-toast" x-show="toast.visible" x-transition :class="'ajax-toast-' + toast.type">
            <div class="ajax-toast-icon">

                <i class="fas" :class="{
                    'fa-check-circle': toast.type === 'success',
                    'fa-exclamation-circle': toast.type === 'error',
                    'fa-info-circle': toast.type === 'info',
                    'fa-spinner fa-spin': toast.type === 'loading'
                }"></i>

            </div>
            <div class="ajax-toast-content">
                <div class="ajax-toast-title" x-text="toast.title"></div>
                <div class="ajax-toast-message" x-text="toast.message"></div>
            </div>
            <button type="button" class="ajax-toast-close" @click="toast.visible = false">
                <i class="fas fa-times"></i>
            </button>
        </div>


        {{-- ==== DELETE / CONFIRM MODAL ======== --}}

        <div class="ajax-modal-backdrop" x-show="confirm.visible" x-transition.opacity @keydown.escape.window="cancelConfirm()" @click.self="cancelConfirm()">

            <div class="ajax-modal" x-show="confirm.visible" x-transition:enter="ajax-modal-enter" x-transition:leave="ajax-modal-leave">

                <div class="ajax-modal-icon">

                    <i class="fas fa-trash-alt"></i>

                </div>


                <h3 class="ajax-modal-title" x-text="confirm.title"></h3>


                <p class="ajax-modal-message" x-text="confirm.message"></p>


                <div class="ajax-modal-actions">

                    <button type="button" class="btn btn-secondary" @click="cancelConfirm()" :disabled="confirm.loading">

                        <i class="fas fa-times mr-1"></i>

                        Cancelar

                    </button>


                    <button type="button" class="btn btn-danger" @click="confirmAction()" :disabled="confirm.loading">

                        <template x-if="!confirm.loading">

                            <span>

                                <i class="fas fa-trash mr-1"></i>

                                Eliminar

                            </span>

                        </template>


                        <template x-if="confirm.loading">

                            <span>

                                <i class="fas fa-spinner fa-spin mr-1"></i>

                                A eliminar...

                            </span>

                        </template>

                    </button>

                </div>

            </div>

        </div>
        {{-- ======== ADMINLTE WRAPPER ============= --}}
        <div class="wrapper">

            {{-- NAVBAR --}}
            @include('layouts.navbar')

            {{-- SIDEBAR --}}
            @include('layouts.sidebar')

            {{-- =========== CONTENT =============== --}}
            <div class="content-wrapper">
                {{-- Page Header --}}
                <section class="content-header">
                    @yield('page_header')
                </section>
                {{-- Page Content --}}
                <section class="content">
                    @yield('content')
                </section>
            </div>

            {{-- ========= FOOTER ========= --}}
            <footer class="main-footer">
                <div class="float-right d-none d-sm-block">
                    v1.0.0
                </div>
                <strong>
                    © {{ date('Y') }}
                    {{ config('app.name', 'Sistema de Consultoria') }}.
                </strong>
                Todos os direitos reservados.
            </footer>
        </div>
    </div>

    {{-- =============================================================
     JAVASCRIPT
============================================================= --}}

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>

    
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Aplicação --}}
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>

</html>
