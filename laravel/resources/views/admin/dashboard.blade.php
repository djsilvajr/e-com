@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@push('head')
<style>
    .dashboard-greeting {
        margin-bottom: 1.5em;
    }
    .dashboard-greeting .greeting {
        font-size: 1.05rem;
        color: #555;
    }
    .ui.cards.admin-shortcuts {
        margin-left: 0;
        margin-right: 0;
    }
    .ui.cards.admin-shortcuts > .card.admin-card .content .header i.icon {
        margin-right: 0.4em;
        color: #2185d0;
    }
    .ui.cards.admin-shortcuts > .card.admin-card.disabled {
        opacity: 0.55;
        pointer-events: none;
    }
    .ui.cards.admin-shortcuts > .card.admin-card .disabled-pill {
        display: inline-block;
        margin-top: 0.6em;
        padding: 0.2em 0.7em;
        background: #e0e1e2;
        color: #777;
        border-radius: 999px;
        font-size: 0.72em;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
</style>
@endpush

@section('content')
@php
    $userName = \Illuminate\Support\Facades\Auth::guard('web')->user()->name ?? '';
    $firstName = trim(explode(' ', $userName)[0] ?? '');
    $hour = (int) now()->format('H');
    $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
@endphp

<div class="admin-page-header">
    <div class="ui breadcrumb">
        <div class="active section">Dashboard</div>
    </div>
</div>

<div class="admin-section">
    <div class="dashboard-greeting">
        <h2 class="ui header">
            {{ $greeting }}@if ($firstName), {{ $firstName }}@endif!
            <div class="sub header">Bem-vindo(a) ao painel administrativo.</div>
        </h2>
        <p class="greeting">Use os atalhos abaixo para gerenciar o catálogo da loja.</p>
    </div>

    <div class="ui four stackable cards admin-shortcuts">
        <a href="#" class="ui card admin-card disabled" aria-disabled="true" title="Em breve">
            <div class="content">
                <div class="header"><i class="boxes icon"></i> Produtos</div>
                <div class="description">Cadastrar e editar produtos</div>
                <span class="disabled-pill">Em breve</span>
            </div>
        </a>
        <a href="#" class="ui card admin-card disabled" aria-disabled="true" title="Em breve">
            <div class="content">
                <div class="header"><i class="dollar sign icon"></i> Preços</div>
                <div class="description">Atualizar valores</div>
                <span class="disabled-pill">Em breve</span>
            </div>
        </a>
        <a href="{{ route('admin.types.index') }}" class="ui card admin-card" data-loading>
            <div class="content">
                <div class="header"><i class="sitemap icon"></i> Categorias</div>
                <div class="description">Organizar o tipo de produto que é vendido no e-commerce.</div>
            </div>
        </a>
    </div>
</div>
@endsection
