<?php

namespace App\Http\Controllers\Web\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminProductVariantRequest;
use App\Http\Requests\Admin\UpdateAdminProductVariantRequest;
use App\Services\ProductService;
use App\Services\ProductVariantService;
use App\Enums\ProductVariantType;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\InvalidParametersException;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\DuplicatedValueException;
use App\Exceptions\PersistenceErrorException;

class AdminProductVariantController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private ProductVariantService $productVariantService,
    ) {}

    /**
     * GET /admin/products/{product_id}/variants
     */
    public function index(int $product_id)
    {
        try {
            $product = $this->productService->getProductById($product_id);
        } catch (ResourceNotFoundException $e) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Produto não encontrado.');
        }

        $variants = [];
        try {
            $variants = $this->productVariantService->list($product_id);
        } catch (ResourceNotFoundException $e) {
            $variants = [];
        } catch (PersistenceErrorException $e) {
            $variants = [];
        }

        return view('admin.productVariant.index', [
            'product'  => $product,
            'variants' => $variants,
        ]);
    }

    /**
     * GET /admin/products/{product_id}/variants/create
     */
    public function create(int $product_id)
    {
        try {
            $product = $this->productService->getProductById($product_id);
        } catch (ResourceNotFoundException $e) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Produto não encontrado.');
        }

        return view('admin.productVariant.create', [
            'product'      => $product,
            'variantTypes' => $this->variantTypeOptions(),
        ]);
    }

    /**
     * POST /admin/products/{product_id}/variants
     */
    public function store(int $product_id, CreateAdminProductVariantRequest $request)
    {
        try {
            $created = $this->productVariantService->add($product_id, $this->buildVariantPayload($request, $product_id));
        } catch (ResourceNotFoundException | BusinessRuleException | DuplicatedValueException | InvalidParametersException $e) {
            return redirect()
                ->route('admin.products.variants.create', ['product_id' => $product_id])
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (PersistenceErrorException $e) {
            return redirect()
                ->route('admin.products.variants.create', ['product_id' => $product_id])
                ->withInput()
                ->with('error', 'Não foi possível salvar a variante.');
        }

        return redirect()
            ->route('admin.products.variants.index', ['product_id' => $product_id])
            ->with('success', 'Variante criada com sucesso.');
    }

    /**
     * GET /admin/products/{product_id}/variants/{id}/edit
     */
    public function edit(int $product_id, int $id)
    {
        try {
            $product = $this->productService->getProductById($product_id);
            $variant = $this->productVariantService->get($product_id, $id);
        } catch (ResourceNotFoundException $e) {
            return redirect()
                ->route('admin.products.variants.index', ['product_id' => $product_id])
                ->with('error', 'Variante não encontrada.');
        }

        return view('admin.productVariant.edit', [
            'product'      => $product,
            'variant'      => $variant,
            'variantTypes' => $this->variantTypeOptions(),
        ]);
    }

    /**
     * PUT /admin/products/{product_id}/variants/{id}
     */
    public function update(int $product_id, int $id, UpdateAdminProductVariantRequest $request)
    {
        try {
            $this->productVariantService->update($product_id, $id, $this->buildVariantPayload($request, $product_id));
        } catch (ResourceNotFoundException | BusinessRuleException | DuplicatedValueException | InvalidParametersException $e) {
            return redirect()
                ->route('admin.products.variants.edit', ['product_id' => $product_id, 'id' => $id])
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (PersistenceErrorException $e) {
            return redirect()
                ->route('admin.products.variants.edit', ['product_id' => $product_id, 'id' => $id])
                ->withInput()
                ->with('error', 'Não foi possível salvar a variante.');
        }

        return redirect()
            ->route('admin.products.variants.edit', ['product_id' => $product_id, 'id' => $id])
            ->with('success', 'Variante atualizada com sucesso.');
    }

    /**
     * DELETE /admin/products/{product_id}/variants/{id}
     */
    public function destroy(int $product_id, int $id, Request $request)
    {
        try {
            $this->productVariantService->delete($product_id, $id);
        } catch (ResourceNotFoundException | BusinessRuleException | InvalidParametersException $e) {
            return redirect()
                ->route('admin.products.variants.index', ['product_id' => $product_id])
                ->with('error', $e->getMessage());
        } catch (PersistenceErrorException $e) {
            return redirect()
                ->route('admin.products.variants.index', ['product_id' => $product_id])
                ->with('error', 'Não foi possível remover a variante.');
        }

        return redirect()
            ->route('admin.products.variants.index', ['product_id' => $product_id])
            ->with('success', 'Variante removida.');
    }

    /**
     * Builds a normalized variant payload from the form input.
     */
    private function buildVariantPayload(Request $request, int $productId): array
    {
        $dimensions = null;
        if ($request->filled('dimensions.altura') || $request->filled('dimensions.largura') || $request->filled('dimensions.profundidade')) {
            $dimensions = [
                'altura'       => $request->filled('dimensions.altura') ? (float) $request->input('dimensions.altura') : null,
                'largura'      => $request->filled('dimensions.largura') ? (float) $request->input('dimensions.largura') : null,
                'profundidade' => $request->filled('dimensions.profundidade') ? (float) $request->input('dimensions.profundidade') : null,
            ];
        }

        return [
            'product_id'       => $productId,
            'sku'              => (string) $request->input('sku', ''),
            'name'             => (string) $request->input('name', ''),
            'barcode'          => (string) $request->input('barcode', ''),
            'variant_type'     => (string) $request->input('variant_type', ''),
            'price_adjustment' => $request->filled('price_adjustment') ? (float) $request->input('price_adjustment') : 0,
            'stock'            => $request->filled('stock') ? (int) $request->input('stock') : 0,
            'reserved_stock'   => $request->filled('reserved_stock') ? (int) $request->input('reserved_stock') : 0,
            'min_stock'        => $request->filled('min_stock') ? (int) $request->input('min_stock') : 0,
            'weight'           => $request->filled('weight') ? (float) $request->input('weight') : null,
            'dimensions'       => $dimensions,
            'image_url'        => $request->filled('image_url') ? (string) $request->input('image_url') : null,
            'order'            => $request->filled('order') ? (int) $request->input('order') : 0,
            'active'           => $request->boolean('active'),
            'is_default'       => $request->boolean('is_default'),
        ];
    }

    /**
     * Available variant types displayed in the variant forms.
     *
     * @return array<string, string>
     */
    private function variantTypeOptions(): array
    {
        return [
            ProductVariantType::Clothing->value    => 'Roupas/Calçados',
            ProductVariantType::Electronics->value => 'Eletrônicos',
            ProductVariantType::Furniture->value   => 'Móveis',
            ProductVariantType::Books->value       => 'Livros',
            ProductVariantType::Simple->value      => 'Simples (sem variantes)',
        ];
    }
}
