@extends('layouts.admin')

@section('title', 'Produtos - Admin')

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
    .products-table-wrapper {
        overflow-x: auto;
        margin-top: 1em;
    }
    .product-search-form {
        margin-bottom: 1em;
    }
    .product-search-form .grouped-fields {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: flex-end;
    }
    .product-search-form .grouped-fields .field {
        flex: 1 1 220px;
        margin: 0 !important;
    }
    .product-search-form .grouped-fields .actions {
        display: inline-flex;
        gap: 0.4rem;
    }
</style>
@endpush

@section('content')
<div class="admin-page-header">
    <div class="ui breadcrumb">
        <a class="section" href="{{ route('admin.dashboard') }}" data-loading>Dashboard</a>
        <i class="right angle icon divider"></i>
        <div class="active section">Produtos</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.products.create') }}" class="ui small primary button" data-loading>
            <i class="plus icon"></i> Novo produto
        </a>
    </div>
</div>

<div class="admin-section">
    <h2 class="ui header">
        <i class="boxes icon"></i>
        <div class="content">
            Gerenciar produtos
            <div class="sub header">
                Selecione a categoria principal e, se quiser, refine pelo sub-tipo e por parte do nome (mín. 3 caracteres).
            </div>
        </div>
    </h2>

    @if (session('success'))
        <div class="ui positive message">
            <i class="close icon"></i>
            <div class="header">{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error') || $errorMessage)
        <div class="ui negative message">
            <i class="close icon"></i>
            <div class="header">{{ session('error') ?? $errorMessage }}</div>
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

    <form class="ui form product-search-form" method="GET" action="{{ route('admin.products.index') }}" data-no-loading="true">
        <div class="grouped-fields">
            <div class="field {{ $errors->has('variant_type') ? 'error' : '' }}">
                <label for="variant_type">Categoria principal <span style="color:#db2828;">*</span></label>
                <select name="variant_type" id="variant_type" class="ui search dropdown" required>
                    <option value="">Selecione...</option>
                    @foreach ($variantOptions as $value => $label)
                        <option
                            value="{{ $value }}"
                            {{ old('variant_type', $variantType) === $value ? 'selected' : '' }}
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field {{ $errors->has('name') ? 'error' : '' }}">
                <label for="name">Nome (opcional, mín. 3 caracteres)</label>
                <div class="ui icon input">
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $name) }}"
                        placeholder="Buscar por nome..."
                        autocomplete="off"
                    >
                    <i class="search icon"></i>
                </div>
            </div>

            <div class="field" style="flex: 0 0 auto;">
                <label>&nbsp;</label>
                <div class="actions">
                    <button type="submit" class="ui primary button">
                        <i class="search icon"></i> Buscar
                    </button>
                    @if ($hasSearch)
                        <a href="{{ route('admin.products.index') }}" class="ui button" data-loading>
                            <i class="redo icon"></i> Limpar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    @if (!$hasSearch)
        <div class="ui info message">
            <i class="search icon"></i>
            <div class="content">
                <div class="header">Selecione uma categoria principal e clique em "Buscar" para ver os resultados.</div>
                <p>Os produtos só aparecerão após a primeira pesquisa.</p>
            </div>
        </div>
    @elseif (empty($products))
        <div class="ui info message admin-empty-state">
            <i class="search icon"></i>
            <div class="content">
                <div class="header">Nenhum produto encontrado</div>
                <p>Ajuste os filtros ou crie um novo produto.</p>
            </div>
        </div>
    @else
        <div class="ui admin-toolbar">
            <div class="ui icon input">
                <input
                    id="products-search"
                    type="text"
                    placeholder="Filtrar resultados nesta página..."
                    autocomplete="off"
                >
                <i class="filter icon"></i>
            </div>
            <div class="page-size-select">
                <label for="products-page-size">Por página:</label>
                <select id="products-page-size">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="0">Todos</option>
                </select>
            </div>
            <div class="toolbar-info" id="products-info"></div>
        </div>

        <div class="products-table-wrapper">
            <table
                class="ui celled striped table js-paginated-table"
                data-page-size="10"
                data-search="#products-search"
                data-info="#products-info"
                data-pagination="#products-pagination"
                data-empty="#products-empty"
                data-page-size-select="#products-page-size"
            >
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nome</th>
                        <th style="width: 180px;">SKU</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 80px;">Estoque</th>
                        <th style="width: 1px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        @php
                            $product = is_object($product) ? (array) $product : $product;
                            $isActive = (bool) ($product['active'] ?? false);
                            $sku = (string) ($product['sku'] ?? '');
                            $stock = (int) ($product['total_stock'] ?? 0);
                            $searchText = trim(
                                ($product['name'] ?? '') . ' ' .
                                $sku . ' ' .
                                ($product['brand'] ?? '') . ' ' .
                                ($product['model'] ?? '')
                            );
                        @endphp
                        <tr data-search-text="{{ \Illuminate\Support\Str::lower($searchText) }}">
                            <td>{{ $product['id'] ?? '' }}</td>
                            <td>
                                <strong>{{ $product['name'] ?? '' }}</strong>
                                @if (!empty($product['short_description']))
                                    <div class="ui small text" style="color: #666;">{{ \Illuminate\Support\Str::limit($product['short_description'], 80) }}</div>
                                @endif
                            </td>
                            <td><code>{{ $sku }}</code></td>
                            <td>
                                @if ($isActive)
                                    <span class="ui green label">Ativo</span>
                                @else
                                    <span class="ui grey label">Inativo</span>
                                @endif
                            </td>
                            <td>{{ $stock }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.products.variants.index', ['product_id' => $product['id']]) }}"
                                       class="ui small teal button" data-loading>
                                        <i class="copy icon"></i> Variantes
                                    </a>
                                    <a href="{{ route('admin.products.edit', ['id' => $product['id']]) }}"
                                       class="ui small primary button" data-loading>
                                        <i class="edit icon"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.products.destroy', ['id' => $product['id']]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Remover este produto?');">
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

        <div id="products-empty" class="ui info message admin-empty-state" style="display: none;">
            <i class="search icon"></i>
            <div class="content">
                <div class="header">Nenhum resultado encontrado</div>
                <p>Ajuste o filtro de busca acima.</p>
            </div>
        </div>

        <div id="products-pagination" class="admin-pagination-container"></div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        // Searchable main-category dropdown (Select2-like UX).
        $('.ui.search.dropdown').dropdown({
            forceSelection: false,
            fullTextSearch: 'exact',
            match: 'text',
            clearable: true,
            selectOnKeydown: false,
        });

        $('.message .close').on('click', function () {
            $(this).closest('.message').transition('fade');
        });
    });
</script>
@endpush
