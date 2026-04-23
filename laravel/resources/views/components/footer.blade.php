@props([
    'brand' => config('app.name', 'e-com'),
])

<div class="site-footer" style="margin-top: 3em; padding: 3em 0 0; background: var(--brand-dark); color: #f5f5f5;">
    <div class="ui container">
        <div class="ui stackable divided equal height stackable grid" style="color: inherit;">
            <div class="four wide column">
                <h4 style="color: #ffffff; margin-bottom: 0.75em; display: inline-flex; align-items: center; gap: 0.5em;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background: var(--brand-primary); color: #ffffff;">
                        <i class="shopping bag icon" style="margin: 0;"></i>
                    </span>
                    {{ $brand }}
                </h4>
                <p style="color: rgba(255,255,255,0.75);">Sua loja online de confiança. Produtos selecionados com qualidade e entrega rápida para todo o Brasil.</p>
            </div>

            <div class="three wide column">
                <h4 style="color: #ffffff; margin-bottom: 0.75em;">Loja</h4>
                <div class="footer-links">
                    <a href="{{ url('/products') }}">Produtos</a>
                    <a href="{{ url('/categories') }}">Categorias</a>
                    <a href="{{ url('/offers') }}">Ofertas</a>
                    <a href="{{ url('/new') }}">Novidades</a>
                </div>
            </div>

            <div class="three wide column">
                <h4 style="color: #ffffff; margin-bottom: 0.75em;">Atendimento</h4>
                <div class="footer-links">
                    <a href="{{ url('/help') }}">Central de ajuda</a>
                    <a href="{{ url('/shipping') }}">Entrega</a>
                    <a href="{{ url('/returns') }}">Trocas e devoluções</a>
                    <a href="{{ url('/contact') }}">Fale conosco</a>
                </div>
            </div>

            <div class="three wide column">
                <h4 style="color: #ffffff; margin-bottom: 0.75em;">Institucional</h4>
                <div class="footer-links">
                    <a href="{{ url('/about') }}">Sobre nós</a>
                    <a href="{{ url('/terms') }}">Termos de uso</a>
                    <a href="{{ url('/privacy') }}">Política de privacidade</a>
                </div>
            </div>

            <div class="three wide column">
                <h4 style="color: #ffffff; margin-bottom: 0.75em;">Siga-nos</h4>
                <div class="footer-links">
                    <a href="#"><i class="instagram icon"></i> Instagram</a>
                    <a href="#"><i class="facebook icon"></i> Facebook</a>
                    <a href="#"><i class="twitter icon"></i> Twitter</a>
                    <a href="#"><i class="whatsapp icon"></i> WhatsApp</a>
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid rgba(255,255,255,0.12); margin-top: 2.5em; padding: 1.25em 0; text-align: center;">
            <p style="margin: 0; color: rgba(255,255,255,0.65); font-size: 0.9rem;">
                &copy; {{ date('Y') }} {{ $brand }}. Todos os direitos reservados.
            </p>
        </div>
    </div>
</div>

<style>
    .site-footer { line-height: 1.5; }
    .site-footer h4 {
        font-size: 1rem;
        font-weight: 700;
    }
    .site-footer .footer-links {
        display: flex;
        flex-direction: column;
        gap: 0.4em;
    }
    .site-footer .footer-links a {
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5em;
    }
    .site-footer .footer-links a i.icon {
        margin: 0;
        line-height: 1;
    }
    .site-footer .footer-links a:hover {
        color: var(--brand-primary);
    }

    @media (max-width: 768px) {
        .site-footer { padding: 2em 0 0 !important; }
        .site-footer h4 { margin-top: 1em; }
        .site-footer .ui.stackable.grid > .column { padding-bottom: 0 !important; }
    }
</style>
