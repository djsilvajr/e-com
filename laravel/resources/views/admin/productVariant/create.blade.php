@extends('layouts.admin')

@php
    $product = is_object($product ?? null) ? (array) $product : ($product ?? []);
    $productId = (int) ($product['id'] ?? 0);
@endphp

@section('title', 'Nova variante - Admin')

@push('head')
<style>
    .form-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .form-actions .ui.button {
        margin: 0;
    }
    .form-section + .form-section {
        margin-top: 1.5em;
        padding-top: 1.25em;
        border-top: 1px solid #eee;
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
        <a class="section" href="{{ route('admin.products.variants.index', ['product_id' => $productId]) }}" data-loading>Variantes</a>
        <i class="right angle icon divider"></i>
        <div class="active section">Nova</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.products.variants.index', ['product_id' => $productId]) }}"
           class="ui small button" data-loading>
            <i class="arrow left icon"></i> Voltar
        </a>
    </div>
</div>

<div class="admin-section">
    <h2 class="ui header">
        <i class="plus icon"></i>
        <div class="content">
            Nova variante
            <div class="sub header">Para o produto "{{ $product['name'] ?? '' }}".</div>
        </div>
    </h2>

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

    <form class="ui form" method="POST" action="{{ route('admin.products.variants.store', ['product_id' => $productId]) }}">
        @csrf

        <div class="form-section">
            <h4 class="ui header">Identificação</h4>
            <div class="two fields">
                <div class="field {{ $errors->has('name') ? 'error' : '' }}">
                    <label>Nome <span style="color:#db2828;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="Ex.: Azul - M">
                </div>
                <div class="field {{ $errors->has('variant_type') ? 'error' : '' }}">
                    <label>Tipo de variante <span style="color:#db2828;">*</span></label>
                    <select name="variant_type" class="ui search dropdown" required>
                        <option value="">Selecione...</option>
                        @foreach ($variantTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('variant_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="two fields">
                <div class="field {{ $errors->has('sku') ? 'error' : '' }}">
                    <label>SKU <span style="color:#db2828;">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku') }}" required maxlength="255">
                </div>
                <div class="field {{ $errors->has('barcode') ? 'error' : '' }}">
                    <label>Código de barras <span style="color:#db2828;">*</span></label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" required maxlength="255">
                </div>
            </div>
            <div class="field {{ $errors->has('image_url') ? 'error' : '' }}">
                <label>URL da imagem</label>
                <input type="text" name="image_url" value="{{ old('image_url') }}" maxlength="1024">
            </div>
        </div>

        <div class="form-section">
            <h4 class="ui header">Estoque e preço</h4>
            <div class="four fields">
                <div class="field {{ $errors->has('price_adjustment') ? 'error' : '' }}">
                    <label>Ajuste de preço</label>
                    <input type="number" step="0.01" name="price_adjustment" value="{{ old('price_adjustment', 0) }}">
                </div>
                <div class="field {{ $errors->has('stock') ? 'error' : '' }}">
                    <label>Estoque</label>
                    <input type="number" min="0" name="stock" value="{{ old('stock', 0) }}">
                </div>
                <div class="field {{ $errors->has('reserved_stock') ? 'error' : '' }}">
                    <label>Estoque reservado</label>
                    <input type="number" min="0" name="reserved_stock" value="{{ old('reserved_stock', 0) }}">
                </div>
                <div class="field {{ $errors->has('min_stock') ? 'error' : '' }}">
                    <label>Estoque mínimo</label>
                    <input type="number" min="0" name="min_stock" value="{{ old('min_stock', 0) }}">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h4 class="ui header">Dimensões e peso</h4>
            <div class="four fields">
                <div class="field">
                    <label>Peso (kg)</label>
                    <input type="number" step="0.01" min="0" name="weight" value="{{ old('weight') }}">
                </div>
                <div class="field">
                    <label>Altura</label>
                    <input type="number" step="0.01" min="0" name="dimensions[altura]" value="{{ old('dimensions.altura') }}">
                </div>
                <div class="field">
                    <label>Largura</label>
                    <input type="number" step="0.01" min="0" name="dimensions[largura]" value="{{ old('dimensions.largura') }}">
                </div>
                <div class="field">
                    <label>Profundidade</label>
                    <input type="number" step="0.01" min="0" name="dimensions[profundidade]" value="{{ old('dimensions.profundidade') }}">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h4 class="ui header">Ordenação e flags</h4>
            <div class="three fields">
                <div class="field {{ $errors->has('order') ? 'error' : '' }}">
                    <label>Ordem</label>
                    <input type="number" min="0" name="order" value="{{ old('order', 0) }}">
                </div>
                <div class="field">
                    <label>&nbsp;</label>
                    <div class="ui toggle checkbox">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" id="active" value="1" {{ old('active', 1) ? 'checked' : '' }}>
                        <label for="active">Ativa</label>
                    </div>
                </div>
                <div class="field">
                    <label>&nbsp;</label>
                    <div class="ui toggle checkbox">
                        <input type="hidden" name="is_default" value="0">
                        <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        <label for="is_default">Padrão</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="ui primary button">
                <i class="save icon"></i> Criar variante
            </button>
            <a href="{{ route('admin.products.variants.index', ['product_id' => $productId]) }}" class="ui button">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('.ui.search.dropdown').dropdown({
            forceSelection: true,
            fullTextSearch: 'exact',
            match: 'text',
            clearable: true,
        });
        $('.ui.checkbox').checkbox();
        $('.message .close').on('click', function () {
            $(this).closest('.message').transition('fade');
        });
    });
</script>
@endpush
