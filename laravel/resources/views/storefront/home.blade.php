@extends('layouts.storefront')

@section('title', config('app.name', 'e-com') . ' - Bem-vindo')

@php($active = 'home')

@section('content')
    <div class="ui stackable grid">
        <div class="sixteen wide column">
            <div class="ui raised segment" style="padding: 3em 2em;">
                <h1 class="ui header">
                    <i class="shopping bag icon"></i>
                    <div class="content">
                        Bem-vindo à loja
                        <div class="sub header">Descubra os melhores produtos com entrega rápida.</div>
                    </div>
                </h1>

                <p style="margin-top: 1em;">
                    Navegue pelas categorias, aproveite nossas ofertas e monte seu pedido em poucos cliques.
                </p>

                <a href="{{ url('/products') }}" class="ui primary button">
                    <i class="boxes icon"></i> Ver produtos
                </a>
                <a href="{{ url('/offers') }}" class="ui button">
                    <i class="tag icon"></i> Ofertas
                </a>
            </div>
        </div>
    </div>

    <h2 class="ui header" style="margin-top: 2em;">Destaques</h2>

    <div class="ui four stackable cards">
        <div class="ui card">
            <div class="image">
                <div class="ui placeholder"><div class="image"></div></div>
            </div>
            <div class="content">
                <div class="header">Produto em destaque</div>
                <div class="description">Uma breve descrição do produto em destaque.</div>
            </div>
            <div class="extra content">
                <span class="ui teal text">R$ 99,90</span>
            </div>
        </div>

        <div class="ui card">
            <div class="image">
                <div class="ui placeholder"><div class="image"></div></div>
            </div>
            <div class="content">
                <div class="header">Novidade</div>
                <div class="description">Acabou de chegar na loja.</div>
            </div>
            <div class="extra content">
                <span class="ui teal text">R$ 149,90</span>
            </div>
        </div>

        <div class="ui card">
            <div class="image">
                <div class="ui placeholder"><div class="image"></div></div>
            </div>
            <div class="content">
                <div class="header">Mais vendido</div>
                <div class="description">O queridinho da galera.</div>
            </div>
            <div class="extra content">
                <span class="ui teal text">R$ 79,90</span>
            </div>
        </div>

        <div class="ui card">
            <div class="image">
                <div class="ui placeholder"><div class="image"></div></div>
            </div>
            <div class="content">
                <div class="header">Oferta</div>
                <div class="description">Com desconto especial por tempo limitado.</div>
            </div>
            <div class="extra content">
                <span class="ui teal text">R$ 59,90</span>
            </div>
        </div>
    </div>
@endsection
