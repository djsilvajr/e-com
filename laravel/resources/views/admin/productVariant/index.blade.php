@extends('layouts.admin')

@php
    $product = is_object($product ?? null) ? (array) $product : ($product ?? []);
    $productId = (int) ($product['id'] ?? 0);
@endphp

@section('title', 'Variantes de ' . ($product['name'] ?? 'Produto') . ' - Admin')

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
    .variants-table-wrapper {
        overflow-x: auto;
    }
</style>
@endpush

@section('content')
<div class="admin-page-header">
    <div class="ui breadcrumb">
        <a class="section" href="{{ route('admin.dashboard') }}" data-loading>Dashboard</a>
        <i class="right angle icon divider"></i>
        <a class="section" href="{{ route('admin.products.index') }}" data-loading>Produtos</a>
        <i class="right angle icon divider"></i>
        <a class="section" href="{{ route('admin.products.edit', ['id' => $productId]) }}" data-loading>{{ $product['name'] ?? '' }}</a>
        <i class="right angle icon divider"></i>
        <div class="active section">Variantes</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.products.variants.create', ['product_id' => $productId]) }}"
           class="ui small primary button" data-loading>
            <i class="plus icon"></i> Nova variante
        </a>
        <a href="{{ route('admin.products.edit', ['id' => $productId]) }}"
           class="ui small button" data-loading>
            <i class="arrow left icon"></i> Voltar ao produto
        </a>
    </div>
</div>

<div class="admin-section">
    <h2 class="ui header">
        <i class="copy icon"></i>
        <div class="content">
            Variantes de "{{ $product['name'] ?? '' }}"
            <span class="count-badge" id="variants-count">{{ count($variants) }}</span>
            <div class="sub header">
                SKU do produto: <code>{{ $product['sku'] ?? '' }}</code>
            </div>
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

    @if (empty($variants))
        <div class="ui info message">
            <i class="info icon"></i>
            <div class="content">
                <div class="header">Nenhuma variante cadastrada para este produto.</div>
                <p>Use o botão "Nova variante" acima para adicionar a primeira.</p>
            </div>
        </div>
    @else
        <div class="ui admin-toolbar">
            <div class="ui icon input">
                <input
                    id="variants-search"
                    type="text"
                    placeholder="Pesquisar por nome, SKU ou código de barras..."
                    autocomplete="off"
                >
                <i class="search icon"></i>
            </div>
            <div class="page-size-select">
                <label for="variants-page-size">Por página:</label>
                <select id="variants-page-size">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="0">Todos</option>
                </select>
            </div>
            <div class="toolbar-info" id="variants-info"></div>
        </div>

        <div class="variants-table-wrapper">
            <table
                class="ui celled striped table js-paginated-table"
                data-page-size="10"
                data-search="#variants-search"
                data-info="#variants-info"
                data-pagination="#variants-pagination"
                data-empty="#variants-empty"
                data-page-size-select="#variants-page-size"
                data-count-badge="#variants-count"
            >
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Nome</th>
                        <th style="width: 160px;">SKU</th>
                        <th style="width: 160px;">Código barras</th>
                        <th style="width: 100px;">Estoque</th>
                        <th style="width: 90px;">Status</th>
                        <th style="width: 1px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($variants as $variant)
                        @php
                            $variant = is_object($variant) ? (array) $variant : $variant;
                            $variantActive = (bool) ($variant['active'] ?? false);
                            $isDefault = (bool) ($variant['is_default'] ?? false);
                            $variantSku = (string) ($variant['sku'] ?? '');
                            $variantBarcode = (string) ($variant['barcode'] ?? '');
                            $variantSearchText = trim(
                                ($variant['name'] ?? '') . ' ' .
                                $variantSku . ' ' .
                                $variantBarcode . ' ' .
                                ($variant['variant_type'] ?? '')
                            );
                        @endphp
                        <tr data-search-text="{{ \Illuminate\Support\Str::lower($variantSearchText) }}">
                            <td>{{ $variant['id'] ?? '' }}</td>
                            <td>
                                <strong>{{ $variant['name'] ?? '' }}</strong>
                                @if ($isDefault)
                                    <span class="ui mini blue label" style="margin-left:0.4em;">Padrão</span>
                                @endif
                                @if (!empty($variant['variant_type']))
                                    <div class="ui small text" style="color: #666;">
                                        <code>{{ $variant['variant_type'] }}</code>
                                    </div>
                                @endif
                            </td>
                            <td><code>{{ $variantSku }}</code></td>
                            <td><code>{{ $variantBarcode }}</code></td>
                            <td>{{ (int) ($variant['stock'] ?? 0) }}</td>
                            <td>
                                @if ($variantActive)
                                    <span class="ui green label">Ativa</span>
                                @else
                                    <span class="ui grey label">Inativa</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.products.variants.edit', ['product_id' => $productId, 'id' => $variant['id']]) }}"
                                       class="ui small primary button" data-loading>
                                        <i class="edit icon"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.products.variants.destroy', ['product_id' => $productId, 'id' => $variant['id']]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Remover esta variante?');">
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

        <div id="variants-empty" class="ui info message admin-empty-state" style="display: none;">
            <i class="search icon"></i>
            <div class="content">
                <div class="header">Nenhum resultado encontrado</div>
                <p>Ajuste a busca ou limpe o campo para ver todas as variantes.</p>
            </div>
        </div>

        <div id="variants-pagination" class="admin-pagination-container"></div>
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
