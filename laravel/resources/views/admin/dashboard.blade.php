@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')
<div class="ui container">
    <div class="ui top attached inverted menu" style="border-radius: 0;">
        <div class="header item">
            <i class="shield alternate icon"></i> Painel Administrativo
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
        <h2 class="ui header">Bem-vindo(a) ao admin</h2>
        <p>Use o menu para gerenciar produtos, tipos de produto, preços e categorias.</p>

        <div class="ui four stackable cards">
            <a href="#" class="ui card">
                <div class="content">
                    <div class="header"><i class="boxes icon"></i> Produtos</div>
                    <div class="description">Cadastrar e editar produtos</div>
                </div>
            </a>
            <a href="#" class="ui card">
                <div class="content">
                    <div class="header"><i class="sitemap icon"></i> Categorias</div>
                    <div class="description">Organizar o catálogo</div>
                </div>
            </a>
            <a href="#" class="ui card">
                <div class="content">
                    <div class="header"><i class="dollar sign icon"></i> Preços</div>
                    <div class="description">Atualizar valores</div>
                </div>
            </a>
            <a href="{{ route('admin.types.index') }}" class="ui card">
                <div class="content">
                    <div class="header"><i class="tags icon"></i> Tipos de produtos</div>
                    <div class="description">Gerenciar tipos de produto</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
