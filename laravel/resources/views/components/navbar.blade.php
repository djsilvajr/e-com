@props([
    'brand' => config('app.name', 'e-com'),
    'active' => '',
])

@php
    $authUser = \Illuminate\Support\Facades\Auth::guard('web')->user();
    $isAdmin  = $authUser && ($authUser->is_admin ?? null) === 'Y';
@endphp

<nav class="site-navbar">
    {{-- Top row: brand + quick access (Entrar / Admin) --}}
    <div class="nav-top">
        <div class="ui container">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 1em;">
                <a href="{{ url('/') }}" class="brand" style="text-decoration: none;">
                    <span class="brand-mark">
                        <i class="shopping bag icon"></i>
                    </span>
                    <span>{{ $brand }}</span>
                </a>

                {{-- <div class="nav-top-actions computer only">
                    @if ($authUser)
                        <span style="opacity: 0.75; display: inline-flex; align-items: center; gap: 0.35em;">
                            <i class="user circle icon"></i>
                            <span>Olá, {{ $authUser->name ?? 'cliente' }}</span>
                        </span>
                    @endif
                </div> --}}
            </div>
        </div>
    </div>

    {{-- Bottom row: nav links + search + cart + user --}}
    <div class="nav-bottom">
        <div class="ui container">
            <div class="nav-row">
                <div class="nav-links">
                    <a href="{{ url('/') }}" class="{{ $active === 'home' ? 'active' : '' }}">
                        <i class="home icon"></i><span>Início</span>
                    </a>
                    <a href="{{ url('/products') }}" class="{{ $active === 'products' ? 'active' : '' }}">
                        <i class="boxes icon"></i><span>Produtos</span>
                    </a>
                    <a href="{{ url('/about') }}" class="{{ $active === 'about' ? 'active' : '' }}">
                        <i class="info circle icon"></i><span>Sobre</span>
                    </a>
                </div>

                <div class="nav-search">
                    <form action="{{ url('/products') }}" method="GET" style="width: 100%; display: flex; justify-content: center;">
                        <div class="ui icon input" style="width: 100%; max-width: 520px;">
                            <input type="text" name="q" placeholder="Buscar produtos..." value="{{ request('q') }}">
                            <i class="search link icon"></i>
                        </div>
                    </form>
                </div>

                <div class="nav-right">
                    <a href="{{ url('/cart') }}" class="{{ $active === 'cart' ? 'active' : '' }}" title="Carrinho">
                        <i class="shopping cart icon"></i>
                        <span>Carrinho</span>
                        @php $cartCount = session('cart_count', 0); @endphp
                        @if ($cartCount > 0)
                            <span class="cart-count">{{ $cartCount }}</span>
                        @endif
                    </a>

                    @if ($authUser)
                        <div class="ui dropdown user-menu">
                            <a href="#">
                                <i class="user icon"></i>
                                <span>Minha conta</span>
                                <i class="dropdown icon"></i>
                            </a>
                            <div class="menu">
                                <a class="item" href="{{ url('/account') }}">
                                    <i class="id card icon"></i>
                                    <span>Meus dados</span>
                                </a>
                                <a class="item" href="{{ url('/orders') }}">
                                    <i class="box icon"></i>
                                    <span>Meus pedidos</span>
                                </a>
                                <div class="divider"></div>

                                <a class="item" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="sign-out icon"></i>
                                    <span>Sair</span>
                                </a>

                                <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ url('/login') }}" title="Entrar / Criar conta">
                            <i class="user icon"></i>
                            <span>Entrar</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</nav>
