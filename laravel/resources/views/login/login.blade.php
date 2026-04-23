@extends('layouts.storefront')

@section('title', 'Entrar - ' . config('app.name', 'e-com'))

@push('head')
<style>
    /* Header do card de login usando a paleta do projeto */
    .login-card .login-header {
        background: var(--brand-dark);
        color: #ffffff;
        padding: 1.75em 2em;
        border-bottom: 3px solid var(--brand-primary);
    }
    .login-card .login-header .ui.header,
    .login-card .login-header .ui.header .sub.header {
        color: #ffffff !important;
    }
    .login-card .login-header .ui.header .sub.header {
        color: rgba(255, 255, 255, 0.75) !important;
    }
    .login-card .login-body {
        padding: 2.25em 2.5em;
    }
    .login-card .login-footer {
        padding: 1em 2em;
        text-align: center;
        background: #fafafa;
        border-top: 1px solid var(--brand-border);
    }
    .login-card .ui.segments {
        border: 1px solid var(--brand-border);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(27, 20, 0, 0.06);
    }
    .login-card .brand-mark-lg {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: var(--brand-primary);
        color: #ffffff;
        font-size: 1.4rem;
        margin-right: 0.75em;
    }

    /* Campos com foco na cor da marca */
    .login-card .ui.form .ui.input input:focus {
        border-color: var(--brand-primary);
    }
    .login-card .ui.form .field.error .ui.input input {
        background: var(--brand-primary-soft);
        border-color: var(--brand-primary);
    }

    /* Ícone de mostrar/ocultar senha */
    .login-card .password-toggle {
        right: 1em;
        left: auto;
        cursor: pointer;
        pointer-events: auto;
        color: var(--brand-muted);
    }
    .login-card .password-toggle:hover {
        color: var(--brand-primary);
    }

    /* Linha entre lembrar / esqueci senha */
    .login-card .form-row-between {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25em;
    }
    .login-card .form-row-between a {
        color: var(--brand-primary);
        font-size: 0.92rem;
    }
    .login-card .form-row-between a:hover {
        color: var(--brand-primary-hover);
        text-decoration: underline;
    }

    /* Coluna mais larga, centralizada */
    .login-card-wrapper {
        display: flex;
        justify-content: center;
        padding: 1.5em 1em 2.5em;
    }
    .login-card {
        width: 100%;
        max-width: 620px;
    }

    /* Ícones do login alinhados ao texto */
    .login-card .ui.header i.icon,
    .login-card .ui.form .ui.left.icon.input i.icon,
    .login-card .ui.button i.icon,
    .login-card .login-footer i.icon {
        vertical-align: middle;
        line-height: 1;
    }
    .login-card .ui.button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5em;
    }
    .login-card .ui.button i.icon { margin: 0; }
    .login-card .login-footer a {
        display: inline-flex;
        align-items: center;
        gap: 0.4em;
    }
    .login-card .login-footer a i.icon { margin: 0; }
    .login-card .ui.checkbox label {
        display: inline-flex;
        align-items: center;
    }

    /* Tablet */
    @media (max-width: 768px) {
        .login-card { max-width: 540px; }
        .login-card .login-body { padding: 1.75em 1.5em; }
        .login-card .login-header { padding: 1.35em 1.5em; }
        .login-card .login-header .ui.header { font-size: 1.25rem; }
        .login-card .brand-mark-lg {
            width: 40px; height: 40px; font-size: 1.2rem;
        }
    }

    /* Celular */
    @media (max-width: 480px) {
        .login-card-wrapper { padding: 0.5em 0.5em 1.5em; }
        .login-card .login-body { padding: 1.25em 1em; }
        .login-card .login-header { padding: 1.1em 1em; }
        .login-card .login-header .ui.header { font-size: 1.15rem; }
        .login-card .login-header .ui.header .sub.header { font-size: 0.85rem; }
        .login-card .form-row-between {
            flex-wrap: wrap;
            gap: 0.5em;
            justify-content: flex-start;
        }
        .login-card .form-row-between .ui.checkbox,
        .login-card .form-row-between a {
            flex-basis: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="login-card-wrapper">
    <div class="login-card">
        <div class="ui segments">
            <div class="login-header">
                <h2 class="ui header" style="margin: 0; display: flex; align-items: center;">
                    <span class="brand-mark-lg">
                        <i class="sign-in icon"></i>
                    </span>
                    <div class="content" style="padding: 0; margin: 0;">
                        <span>Entrar na sua conta</span>
                        <div class="sub header" style="margin-top: 0.35em;">
                            Bem-vindo de volta! Informe suas credenciais para continuar.
                        </div>
                    </div>
                </h2>
            </div>

            <div class="ui segment login-body" style="margin: 0;">
                @if (session('success'))
                    <div class="ui positive message">
                        <i class="close icon"></i>
                        <div class="header">{{ session('success') }}</div>
                    </div>
                @endif

                @php
                    // Aceita erro vindo como variável para a view, erro em flash session,
                    // ou o legado "$erro" com typo, nessa ordem de prioridade.
                    $loginError = ($error ?? null) ?: (session('error') ?: ($erro ?? null));
                @endphp

                @if (!empty($loginError))
                    <div class="ui negative message">
                        <i class="close icon"></i>
                        <div class="header">Não foi possível entrar</div>
                        <p>{{ $loginError }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="ui negative message">
                        <i class="close icon"></i>
                        <div class="header">Verifique os campos:</div>
                        <ul class="list">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="ui form" method="POST" action="{{ route('login.attempt') }}" id="loginForm">
                    @csrf

                    <div class="field {{ $errors->has('email') ? 'error' : '' }}">
                        <label for="email">E-mail</label>
                        <div class="ui left icon input">
                            <i class="mail icon"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="seu@email.com"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                autofocus
                                required
                            >
                        </div>
                    </div>

                    <div class="field {{ $errors->has('password') ? 'error' : '' }}">
                        <label for="password">Senha</label>
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Sua senha"
                                autocomplete="current-password"
                                required
                            >
                            <i class="eye link icon password-toggle" id="togglePassword"></i>
                        </div>
                    </div>

                    <div class="form-row-between">
                        <div class="ui checkbox">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">Lembrar-me neste dispositivo</label>
                        </div>
                        <a href="#">Esqueci minha senha</a>
                    </div>

                    <button type="submit" class="ui fluid large red submit button" id="submitBtn">
                        <i class="sign-in icon"></i> Entrar
                    </button>
                </form>

                <div class="ui horizontal divider" style="margin: 2em 0 1.5em;">ou</div>

                <div class="ui center aligned basic segment" style="padding: 0;">
                    <p style="margin-bottom: 0.75em; color: var(--brand-muted);">Ainda não tem uma conta?</p>
                    <a href="{{ url('/register') }}" class="ui basic red button">
                        <i class="user plus icon"></i> Criar conta
                    </a>
                </div>
            </div>

            <div class="login-footer">
                <a href="{{ url('/') }}" style="color: var(--brand-muted);">
                    <i class="arrow left icon"></i> Voltar para a loja
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        // Ativa o checkbox do Semantic UI.
        $('.ui.checkbox').checkbox();

        // Fechar mensagens (.close icon).
        $('.message .close').on('click', function () {
            $(this).closest('.message').transition('fade');
        });

        // Mostrar / ocultar senha.
        $('#togglePassword').on('click', function () {
            var $input = $('#password');
            var isPassword = $input.attr('type') === 'password';
            $input.attr('type', isPassword ? 'text' : 'password');
            $(this).toggleClass('eye eye slash');
        });

        // Estado de "carregando" ao enviar o formulário.
        $('#loginForm').on('submit', function () {
            $('#submitBtn').addClass('loading disabled');
        });
    });
</script>
@endpush
