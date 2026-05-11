@extends('layouts.admin')

@section('title', 'Tipos de produtos - Admin')

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
    .types-actions-cell {
        white-space: nowrap;
    }
    .types-table-wrapper {
        overflow-x: auto;
    }
</style>
@endpush

@section('content')
<div class="admin-page-header">
    <div class="ui breadcrumb">
        <a class="section" href="{{ route('admin.dashboard') }}" data-loading>Dashboard</a>
        <i class="right angle icon divider"></i>
        <div class="active section">Tipos de produtos</div>
    </div>
</div>

<div class="admin-section">
    <h2 class="ui header">
        <i class="tags icon"></i>
        <div class="content">
            Gerenciar tipos de produto
            <span class="count-badge" id="types-count">{{ count($productTypes) }}</span>
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
        <div class="ui admin-toolbar">
            <div class="ui icon input">
                <input
                    id="types-search"
                    type="text"
                    placeholder="Pesquisar por nome, slug ou descrição..."
                    autocomplete="off"
                >
                <i class="search icon"></i>
            </div>
            <div class="page-size-select">
                <label for="types-page-size">Por página:</label>
                <select id="types-page-size">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="0">Todos</option>
                </select>
            </div>
            <div class="toolbar-info" id="types-info"></div>
        </div>

        <div class="types-table-wrapper">
            <table
                class="ui celled striped table js-paginated-table"
                data-page-size="10"
                data-search="#types-search"
                data-info="#types-info"
                data-pagination="#types-pagination"
                data-empty="#types-empty"
                data-page-size-select="#types-page-size"
                data-count-badge="#types-count"
            >
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nome</th>
                        <th style="width: 200px;">Slug</th>
                        <th style="width: 160px;">Variante</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 1px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productTypes as $type)
                        @php
                            $type = is_object($type) ? (array) $type : $type;
                            $isActive = (bool) ($type['active'] ?? false);
                            $variant = (string) ($type['variant_type'] ?? '');
                            $variantLabel = $variant !== ''
                                ? ($variantTypes[$variant] ?? $variant)
                                : '';
                            $searchText = trim(
                                ($type['name'] ?? '') . ' ' .
                                ($type['slug'] ?? '') . ' ' .
                                ($type['description'] ?? '') . ' ' .
                                $variant . ' ' .
                                $variantLabel
                            );
                        @endphp
                        <tr data-search-text="{{ \Illuminate\Support\Str::lower($searchText) }}">
                            <td>{{ $type['id'] ?? '' }}</td>
                            <td>
                                <strong>{{ $type['name'] ?? '' }}</strong>
                                @if (!empty($type['description']))
                                    <div class="ui small text" style="color: #666;">{{ \Illuminate\Support\Str::limit($type['description'], 80) }}</div>
                                @endif
                            </td>
                            <td><code>{{ $type['slug'] ?? '' }}</code></td>
                            <td>
                                @if ($variant !== '')
                                    <span class="ui basic label">{{ $variantLabel }}</span>
                                @else
                                    <span style="color:#999;">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                @if ($isActive)
                                    <span class="ui green label">Ativo</span>
                                @else
                                    <span class="ui grey label">Inativo</span>
                                @endif
                            </td>
                            <td class="types-actions-cell">
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
        </div>

        <div id="types-empty" class="ui info message admin-empty-state" style="display: none;">
            <i class="search icon"></i>
            <div class="content">
                <div class="header">Nenhum resultado encontrado</div>
                <p>Ajuste a busca ou limpe o campo para ver todos os tipos.</p>
            </div>
        </div>

        <div id="types-pagination" class="admin-pagination-container"></div>
    @endif
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
