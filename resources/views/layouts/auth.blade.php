<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @yield('title', config('app.name', 'Sistema'))
    </title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    
    <style>
        :root {
            --auth-primary: #2563eb;
            --auth-primary-dark: #1d4ed8;
            --auth-bg: #f4f7fb;
            --auth-text: #172033;
            --auth-muted: #64748b;
            --auth-border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
        }

        body.auth-page {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(circle at top left,
                    rgba(37, 99, 235, .12),
                    transparent 35%),
                radial-gradient(circle at bottom right,
                    rgba(14, 165, 233, .10),
                    transparent 35%),
                var(--auth-bg);

            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 1050px;
        }

        .auth-card {
            display: grid;
            grid-template-columns: 42% 58%;
            overflow: hidden;

            background: #fff;
            border-radius: 24px;

            box-shadow:
                0 25px 60px rgba(15, 23, 42, .10),
                0 5px 20px rgba(15, 23, 42, .05);
        }

        /* Lado esquerdo */

        .auth-brand {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;

            padding: 60px 50px;

            color: white;

            background:
                linear-gradient(145deg,
                    #2563eb 0%,
                    #1d4ed8 45%,
                    #0f172a 100%);

            overflow: hidden;
        }

        .auth-brand::before {
            content: "";
            position: absolute;

            width: 280px;
            height: 280px;

            right: -100px;
            top: -80px;

            border-radius: 50%;

            background: rgba(255, 255, 255, .08);
        }

        .auth-brand::after {
            content: "";
            position: absolute;

            width: 220px;
            height: 220px;

            left: -100px;
            bottom: -100px;

            border-radius: 50%;

            background: rgba(255, 255, 255, .06);
        }

        .brand-content {
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            width: 58px;
            height: 58px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 25px;

            border-radius: 16px;

            background: rgba(255, 255, 255, .15);

            backdrop-filter: blur(10px);

            font-size: 25px;
        }

        .brand-title {
            margin-bottom: 15px;

            font-size: 32px;
            font-weight: 700;
            letter-spacing: -.5px;
        }

        .brand-description {
            max-width: 360px;

            color: rgba(255, 255, 255, .75);

            font-size: 15px;
            line-height: 1.7;
        }

        .brand-features {
            margin-top: 35px;
            padding: 0;

            list-style: none;
        }

        .brand-features li {
            display: flex;
            align-items: center;

            margin-bottom: 14px;

            color: rgba(255, 255, 255, .85);

            font-size: 14px;
        }

        .brand-features i {
            width: 28px;

            color: #bfdbfe;
        }

        /* Formulário */

        .auth-form {
            padding: 55px 60px;
        }

        .auth-header {
            margin-bottom: 32px;
        }

        .auth-title {
            margin-bottom: 8px;

            color: var(--auth-text);

            font-size: 28px;
            font-weight: 700;
        }

        .auth-subtitle {
            margin: 0;

            color: var(--auth-muted);

            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;

            margin-bottom: 8px;

            color: #334155;

            font-size: 13px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;

            top: 50%;
            left: 15px;

            transform: translateY(-50%);

            color: #94a3b8;

            pointer-events: none;
        }

        .auth-input {
            width: 100%;

            height: 48px;

            padding: 0 15px 0 45px;

            border: 1px solid var(--auth-border);
            border-radius: 10px;

            background: #fff;

            color: var(--auth-text);

            font-size: 14px;

            transition: .2s ease;
        }

        .auth-input:focus {
            outline: none;

            border-color: var(--auth-primary);

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, .10);
        }

        .auth-button {
            width: 100%;
            height: 48px;

            border: 0;
            border-radius: 10px;

            background: var(--auth-primary);

            color: #fff;

            font-size: 14px;
            font-weight: 600;

            transition: .2s ease;
        }

        .auth-button:hover {
            background: var(--auth-primary-dark);

            transform: translateY(-1px);

            box-shadow:
                0 8px 20px rgba(37, 99, 235, .22);
        }

        .auth-button i {
            margin-right: 6px;
        }

        .auth-links {
            margin-top: 25px;

            text-align: center;

            font-size: 14px;
        }

        .auth-links a {
            color: var(--auth-primary);

            font-weight: 600;

            text-decoration: none;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 20px;
        }

        .remember {
            display: flex;
            align-items: center;

            color: var(--auth-muted);

            font-size: 13px;
        }

        .remember input {
            margin-right: 7px;
        }

        .forgot-link {
            color: var(--auth-primary);

            font-size: 13px;
            font-weight: 600;

            text-decoration: none;
        }

        .alert {
            border: 0;
            border-radius: 10px;

            font-size: 13px;
        }

        .auth-footer {
            margin-top: 30px;

            padding-top: 22px;

            border-top: 1px solid #f1f5f9;

            text-align: center;

            color: var(--auth-muted);

            font-size: 13px;
        }

        /* Mobile */

        @media (max-width: 768px) {

            .auth-wrapper {
                padding: 20px 15px;
            }

            .auth-card {
                display: block;

                border-radius: 18px;
            }

            .auth-brand {
                display: none;
            }

            .auth-form {
                padding: 40px 25px;
            }

            .auth-title {
                font-size: 24px;
            }
        }

    </style>

    @stack('styles')
</head>
<body class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-brand">
                    <div class="brand-content">
                        <div class="brand-logo">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="brand-title">
                            {{ config('app.name', 'Meu Sistema') }}
                        </div>
                        <div class="brand-description">
                            Uma plataforma moderna para gerir
                            o seu negócio de forma simples,
                            segura e eficiente.
                        </div>
                        <ul class="brand-features">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Gestão simples e intuitiva
                            </li>

                            <li>
                                <i class="fas fa-shield-alt"></i>
                                Segurança dos seus dados
                            </li>

                            <li>
                                <i class="fas fa-bolt"></i>
                                Rápido e eficiente
                            </li>
                        </ul>

                    </div>
                </div>
                <div class="auth-form">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>

    @stack('scripts')

</body>
</html>
