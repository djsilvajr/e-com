@extends('layouts.admin')

@section('title', 'Novo produto - Admin')

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
@php
    $product = [];
    $avgDims = [];
    $today = now()->format('Y-m-d');
@endphp

<div class="admin-page-header">
    <div class="ui breadcrumb">
        <a class="section" href="{{ route('admin.dashboard') }}" data-loading>Dashboard</a>
        <i class="right angle icon divider"></i>
        <a class="section" href="{{ route('admin.products.index') }}" data-loading>Produtos</a>
        <i class="right angle icon divider"></i>
        <div class="active section">Novo produto</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.products.index') }}" class="ui small button" data-loading>
            <i class="arrow left icon"></i> Voltar
        </a>
    </div>
</div>

<div class="admin-section">
    <h2 class="ui header">
        <i class="plus icon"></i>
        <div class="content">
            Novo produto
            <div class="sub header">Preencha os dados abaixo para cadastrar um novo produto.</div>
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

    <form class="ui form" method="POST" action="{{ route('admin.products.store') }}">
        @csrf

        <div class="form-section">
            <h4 class="ui header">Identificação</h4>
            <div class="two fields">
                <div class="field {{ $errors->has('product_type_id') ? 'error' : '' }}">
                    <label>Tipo de produto <span style="color:#db2828;">*</span></label>
                    <select name="product_type_id" class="ui search dropdown" required>
                        <option value="">Selecione...</option>
                        @foreach ($productTypes as $option)
                            <option value="{{ $option['id'] }}" {{ (int) old('product_type_id') === (int) $option['id'] ? 'selected' : '' }}>
                                {{ $option['path'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field {{ $errors->has('name') ? 'error' : '' }}">
                    <label>Nome <span style="color:#db2828;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="255">
                </div>
            </div>
            <div class="two fields">
                <div class="field {{ $errors->has('sku') ? 'error' : '' }}">
                    <label>SKU <span style="color:#db2828;">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku') }}" required maxlength="255">
                </div>
                <div class="field {{ $errors->has('available_at') ? 'error' : '' }}">
                    <label>Disponível a partir de <span style="color:#db2828;">*</span></label>
                    <input type="date" name="available_at" value="{{ old('available_at', $today) }}" required>
                </div>
            </div>
            <div class="two fields">
                <div class="field {{ $errors->has('brand') ? 'error' : '' }}">
                    <label>Marca</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" maxlength="255">
                </div>
                <div class="field {{ $errors->has('model') ? 'error' : '' }}">
                    <label>Modelo</label>
                    <input type="text" name="model" value="{{ old('model') }}" maxlength="255">
                </div>
            </div>
            <div class="field {{ $errors->has('short_description') ? 'error' : '' }}">
                <label>Descrição curta <span style="color:#db2828;">*</span></label>
                <input type="text" name="short_description" value="{{ old('short_description') }}" required maxlength="255">
            </div>
            <div class="field {{ $errors->has('description') ? 'error' : '' }}">
                <label>Descrição completa <span style="color:#db2828;">*</span></label>
                <textarea name="description" rows="4" required>{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="form-section">
            <h4 class="ui header">Dimensões e peso</h4>
            <div class="four fields">
                <div class="field {{ $errors->has('avg_dimensions.width') ? 'error' : '' }}">
                    <label>Largura <span style="color:#db2828;">*</span></label>
                    <input type="number" step="0.01" min="0" name="avg_dimensions[width]"
                        value="{{ old('avg_dimensions.width') }}" required>
                </div>
                <div class="field {{ $errors->has('avg_dimensions.height') ? 'error' : '' }}">
                    <label>Altura <span style="color:#db2828;">*</span></label>
                    <input type="number" step="0.01" min="0" name="avg_dimensions[height]"
                        value="{{ old('avg_dimensions.height') }}" required>
                </div>
                <div class="field {{ $errors->has('avg_dimensions.length') ? 'error' : '' }}">
                    <label>Comprimento <span style="color:#db2828;">*</span></label>
                    <input type="number" step="0.01" min="0" name="avg_dimensions[length]"
                        value="{{ old('avg_dimensions.length') }}" required>
                </div>
                <div class="field {{ $errors->has('avg_dimensions.unit') ? 'error' : '' }}">
                    <label>Unidade <span style="color:#db2828;">*</span></label>
                    <select name="avg_dimensions[unit]" class="ui search dropdown" required>
                        @foreach (['cm','mm','m','in'] as $unit)
                            <option value="{{ $unit }}" {{ old('avg_dimensions.unit','cm') === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="three fields">
                <div class="field {{ $errors->has('avg_weight') ? 'error' : '' }}">
                    <label>Peso médio (kg)</label>
                    <input type="number" step="0.01" min="0" name="avg_weight" value="{{ old('avg_weight') }}">
                </div>
                <div class="field {{ $errors->has('total_stock') ? 'error' : '' }}">
                    <label>Estoque total</label>
                    <input type="number" min="0" name="total_stock" value="{{ old('total_stock', 0) }}">
                </div>
                <div class="field {{ $errors->has('min_stock') ? 'error' : '' }}">
                    <label>Estoque mínimo</label>
                    <input type="number" min="0" name="min_stock" value="{{ old('min_stock', 0) }}">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h4 class="ui header">SEO</h4>
            <div class="field {{ $errors->has('meta_title') ? 'error' : '' }}">
                <label>Meta title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title') }}" maxlength="255">
            </div>
            <div class="field {{ $errors->has('meta_description') ? 'error' : '' }}">
                <label>Meta description</label>
                <textarea name="meta_description" rows="2">{{ old('meta_description') }}</textarea>
            </div>
        </div>

        <div class="form-section">
            <h4 class="ui header">Flags</h4>
            <div class="inline fields">
                <div class="field">
                    <div class="ui toggle checkbox">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" id="active" value="1" {{ old('active', 1) ? 'checked' : '' }}>
                        <label for="active">Ativo</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui toggle checkbox">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <label for="is_featured">Destaque</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui toggle checkbox">
                        <input type="hidden" name="is_new" value="0">
                        <input type="checkbox" name="is_new" id="is_new" value="1" {{ old('is_new') ? 'checked' : '' }}>
                        <label for="is_new">Novidade</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui toggle checkbox">
                        <input type="hidden" name="has_variants" value="0">
                        <input type="checkbox" name="has_variants" id="has_variants" value="1" {{ old('has_variants', 1) ? 'checked' : '' }}>
                        <label for="has_variants">Possui variantes</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="ui primary button">
                <i class="save icon"></i> Criar produto
            </button>
            <a href="{{ route('admin.products.index') }}" class="ui button">Cancelar</a>
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
