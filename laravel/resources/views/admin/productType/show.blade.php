@extends('layouts.admin')

@php
    $productType = is_object($productType ?? null) ? (array) $productType : ($productType ?? []);
    $parentId = (int) ($productType['id'] ?? 0);
    $parentName = $productType['name'] ?? '';
    $parentVariantType = (string) ($effectiveVariantType ?? ($productType['variant_type'] ?? ''));
    $grandparentId = $productType['parent_id'] ?? null;
    $variantTypeLabel = $parentVariantType !== ''
        ? ($variantTypes[$parentVariantType] ?? $parentVariantType)
        : '';
    $canCreate = $parentVariantType !== '';
@endphp

@section('title', 'Sub-tipos de ' . ($parentName ?: 'Tipo') . ' - Admin')

@push('head')
<style>
    .action-buttons {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
    }
    .action-buttons > form {
        display: inline-flex;
        margin: 0;
    }
    .action-buttons .ui.button {
        margin: 0;
        white-space: nowrap;
    }
    .form-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    .form-actions .ui.button {
        margin: 0;
    }
    .types-actions-cell {
        white-space: nowrap;
    }
    .types-table-wrapper {
        overflow-x: auto;
    }
</style>
@endpush

@section('content')
<div class="ui container">
    <div class="ui top attached inverted menu" style="border-radius: 0;">
        <a class="item" href="{{ route('admin.types.index') }}">
            <i class="arrow left icon"></i> Tipos de produtos
        </a>
        @if (!empty($grandparentId))
            <a class="item" href="{{ route('admin.types.show', ['id' => $grandparentId]) }}" data-loading>
                <i class="level up alternate icon"></i> Tipo pai
            </a>
        @endif
        <div class="header item">
            <i class="tags icon"></i> {{ $parentName ?: 'Tipo de produto' }}
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
            <i class="folder open icon"></i>
            <div class="content">
                {{ $parentName }}
                <div class="sub header">
                    Slug: <code>{{ $productType['slug'] ?? '' }}</code>
                    @if (!empty($parentVariantType))
                        &middot; Variante: <code>{{ $parentVariantType }}</code>
                    @endif
                </div>
            </div>
        </h2>

        @if (!empty($productType['description']))
            <p>{{ $productType['description'] }}</p>
        @endif

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

        <h3 class="ui header">
            <i class="list icon"></i>
            <div class="content">
                Sub-tipos
                <div class="sub header">Filhos cadastrados sob este tipo. Clique em "Sub-tipos" para descer na hierarquia.</div>
            </div>
        </h3>

        @if (empty($childProductTypes))
            <div class="ui info message">
                <div class="header">Nenhum sub-tipo cadastrado ainda.</div>
                <p>Use o formulário abaixo para criar o primeiro.</p>
            </div>
        @else
            <div class="types-table-wrapper">
                <table class="ui celled striped table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Nome</th>
                            <th style="width: 160px;">Variante</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 1px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($childProductTypes as $child)
                            @php
                                $child = is_object($child) ? (array) $child : $child;
                                $childActive = (bool) ($child['active'] ?? false);
                            @endphp
                            <tr>
                                <td>{{ $child['id'] ?? '' }}</td>
                                <td>
                                    <strong>{{ $child['name'] ?? '' }}</strong>
                                    <div class="ui small text" style="color: #666;">
                                        <code>{{ $child['slug'] ?? '' }}</code>
                                    </div>
                                </td>
                                <td>
                                    @if (!empty($child['variant_type']))
                                        <span class="ui basic label">{{ $child['variant_type'] }}</span>
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td>
                                    @if ($childActive)
                                        <span class="ui green label">Ativo</span>
                                    @else
                                        <span class="ui grey label">Inativo</span>
                                    @endif
                                </td>
                                <td class="types-actions-cell">
                                    <div class="action-buttons">
                                        <a
                                            href="{{ route('admin.types.show', ['id' => $child['id']]) }}"
                                            class="ui small primary button"
                                            data-loading
                                        >
                                            <i class="folder open icon"></i> Sub-tipos
                                        </a>
                                        <form action="{{ route('admin.types.toggle-status', ['id' => $child['id']]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $childActive ? 'FALSE' : 'TRUE' }}">
                                            <button type="submit" class="ui small {{ $childActive ? 'orange' : 'green' }} button">
                                                <i class="power off icon"></i>
                                                {{ $childActive ? 'Desativar' : 'Ativar' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.types.destroy', ['id' => $child['id']]) }}" method="POST" onsubmit="return confirm('Remover este sub-tipo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ui small red button">
                                                <i class="trash icon"></i> Remover
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="ui hidden divider"></div>

        <h3 class="ui header">
            <i class="plus icon"></i>
            <div class="content">
                Novo sub-tipo
                <div class="sub header">Adicione um sub-tipo a "{{ $parentName }}".</div>
            </div>
        </h3>

        @if (!$canCreate)
            <div class="ui warning message">
                <i class="exclamation triangle icon"></i>
                <div class="content">
                    <div class="header">Variante do tipo pai não definida.</div>
                    <p>O tipo pai precisa ter uma variante definida antes que sub-tipos possam ser criados.</p>
                </div>
            </div>
        @endif

        <form class="ui form" method="POST" action="{{ route('admin.types.store', ['id' => $parentId]) }}">
            @csrf
            <input type="hidden" name="variant_type" value="{{ $parentVariantType }}">

            <div class="three fields">
                <div class="field {{ $errors->has('name') ? 'error' : '' }}">
                    <label>Nome</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex.: Camisetas" required {{ $canCreate ? '' : 'disabled' }}>
                </div>

                <div class="field {{ $errors->has('slug') ? 'error' : '' }}">
                    <label>Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="Ex.: camisetas" required {{ $canCreate ? '' : 'disabled' }}>
                </div>

                <div class="field disabled">
                    <label>Tipo de variante</label>
                    <input
                        type="text"
                        value="{{ $variantTypeLabel ?: 'Não definida' }}"
                        readonly
                        disabled
                        title="Definida pelo tipo pai e não pode ser alterada."
                    >
                    <small style="color: #888;">Herdada do tipo pai.</small>
                </div>
            </div>

            <div class="field {{ $errors->has('description') ? 'error' : '' }}">
                <label>Descrição</label>
                <textarea name="description" rows="3" placeholder="Opcional" {{ $canCreate ? '' : 'disabled' }}>{{ old('description') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="ui primary button {{ $canCreate ? '' : 'disabled' }}" {{ $canCreate ? '' : 'disabled' }}>
                    <i class="save icon"></i> Criar sub-tipo
                </button>
                <a href="{{ route('admin.types.index') }}" class="ui button">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('.ui.dropdown').dropdown();
        $('.message .close').on('click', function () {
            $(this).closest('.message').transition('fade');
        });
    });
</script>
@endpush
