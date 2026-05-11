@extends('layouts.admin')

@section('title', 'Login - Admin')

{{-- Login page has no header / user area since the user is not authenticated yet. --}}
@section('without-chrome', 'true')

@section('content')
<div class="ui middle aligned center aligned grid" style="min-height: calc(100vh - 4em); margin: 0;">
    <div class="column" style="max-width: 450px;">
        <h2 class="ui teal image header">
            <i class="shield alternate icon"></i>
            <div class="content">
                Painel Administrativo
            </div>
        </h2>

        @if (session('success'))
            <div class="ui positive message">
                <i class="close icon"></i>
                <div class="header">{{ session('success') }}</div>
            </div>
        @endif

        @if (!empty($error))
            <div class="ui negative message">
                <i class="exclamation triangle icon"></i>
                <div class="header">{{ $error }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="ui negative message">
                <i class="exclamation triangle icon"></i>
                <ul class="list" style="text-align: left;">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="ui large form" method="POST" action="{{ route('admin.login.attempt') }}">
            @csrf
            <div class="ui stacked segment">
                <div class="field {{ $errors->has('email') ? 'error' : '' }}">
                    <div class="ui left icon input">
                        <i class="user icon"></i>
                        <input
                            type="text"
                            name="email"
                            placeholder="E-mail"
                            value="{{ old('email') }}"
                            autofocus
                        >
                    </div>
                </div>
                <div class="field {{ $errors->has('password') ? 'error' : '' }}">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input type="password" name="password" placeholder="Senha">
                    </div>
                </div>

                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Lembrar-me</label>
                    </div>
                </div>

                <button type="submit" class="ui fluid large teal submit button">
                    <i class="sign-in icon"></i> Entrar
                </button>
            </div>
        </form>

        <div class="ui message">
            Voltar para a <a href="{{ url('/') }}">loja</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('.ui.checkbox').checkbox();
        $('.message .close').on('click', function () {
            $(this).closest('.message').transition('fade');
        });
    });
</script>
@endpush
