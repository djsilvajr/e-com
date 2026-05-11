@extends('layouts.admin')

@section('title', 'Tipos de produtos - Admin')

@push('head')
<style>
    .action-buttons {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.35rem;
    }
    .action-buttons > form {
        display: inline-flex;
        margin: 0;
    }
    .action-buttons .ui.button {
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="ui container">
    <div class="ui top attached inverted menu" style="border-radius: 0;">
        <a class="item" href="{{ route('admin.dashboard') }}">
            <i class="arrow left icon"></i> Voltar
        </a>
        <div class="header item">
            <i class="tags icon"></i> Tipos de produtos
        </div>
        <div class="right menu">
            <div class="item">
                <i class="user icon"></i>
                {{ \Illuminate\Support\Facades\Auth::guard('web')->user()->name ?? '' }}
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="ui item" style="background: transparent; border: 0; color: #fff; cursor: pointer;">
                    <i class="sign-out icon"></i> Sair
                </button>
            </form>
        </div>
    </div>

    <div class="ui bottom attached segment" style="padding: 2em;">
        <h2 class="ui header">
            <i class="tags icon"></i>
            <div class="content">
                Gerenciar tipos de produto
                <div class="sub header">Tipos pré-definidos do sistema e seus sub-tipos.</div>
            </div>
        </h2>

        @if (session('success'))
            <div class="ui positive message">
                <i class="close icon"></i>
                <div class="header">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="ui negative message">
                <i class="close icon"></i>
                <div class="header">{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="ui negative message">
                <i class="close icon"></i>
                <ul class="list" style="text-align: left;">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (empty($productTypes))
            <div class="ui info message">
                <div class="header">Nenhum tipo de produto cadastrado.</div>
                <p>Os tipos pré-definidos do sistema deverão ser criados via migration.</p>
            </div>
        @else
            <table class="ui celled striped table">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 260px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productTypes as $type)
                        @php
                            $type = is_object($type) ? (array) $type : $type;
                            $isActive = (bool) ($type['active'] ?? false);
                        @endphp
                        <tr>
                            <td>{{ $type['id'] ?? '' }}</td>
                            <td>
                                <strong>{{ $type['name'] ?? '' }}</strong>
                                @if (!empty($type['description']))
                                    <div class="ui small text" style="color: #666;">{{ \Illuminate\Support\Str::limit($type['description'], 80) }}</div>
                                @endif
                            </td>
                            <td><code>{{ $type['slug'] ?? '' }}</code></td>
                            <td>
                                @if ($isActive)
                                    <span class="ui green label">Ativo</span>
                                @else
                                    <span class="ui grey label">Inativo</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a
                                        href="{{ route('admin.types.show', ['id' => $type['id']]) }}"
                                        class="ui small primary button"
                                        data-loading
                                    >
                                        <i class="folder open icon"></i> Sub-tipos
                                    </a>
                                    <form action="{{ route('admin.types.toggle-status', ['id' => $type['id']]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $isActive ? 'FALSE' : 'TRUE' }}">
                                        <button type="submit" class="ui small {{ $isActive ? 'orange' : 'green' }} button">
                                            <i class="power off icon"></i>
                                            {{ $isActive ? 'Desativar' : 'Ativar' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('.message .close').on('click', function () {
            $(this).closest('.message').transition('fade');
        });
    });
</script>
@endpush
