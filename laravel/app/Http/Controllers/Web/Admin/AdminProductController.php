<?php

namespace App\Http\Controllers\Web\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminProductRequest;
use App\Http\Requests\Admin\UpdateAdminProductRequest;
use App\Services\ProductService;
use App\Services\ProductTypeService;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\InvalidParametersException;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\PersistenceErrorException;

class AdminProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private ProductTypeService $productTypeService,
    ) {}

    /**
     * GET /admin/products
     * Renders the search form. Results only appear after a submitted search.
     *
     * Search rules:
     *   - variant_type (root category) is REQUIRED. Maps to one of the
     *     fixed root categories: Roupas/Eletrônicos/Móveis/Livros.
     *   - product_type_id (specific sub-type) is OPTIONAL. When provided
     *     it must belong to the selected variant_type — the dropdown in
     *     the view already filters that, server still rejects mismatches.
     *   - name is OPTIONAL, minimum 3 characters (LIKE search).
     */
    public function index(Request $request)
    {
        $variantOptions = $this->variantCategoryOptions();
        $productTypes   = $this->getProductTypeOptions(true);

        $hasSearch     = $request->has('variant_type') || $request->has('product_type_id') || $request->has('name');
        $variantType   = trim((string) $request->input('variant_type', ''));
        $productTypeId = (int) $request->input('product_type_id', 0);
        $name          = trim((string) $request->input('name', ''));

        $products     = [];
        $errorMessage = null;

        if ($hasSearch) {
            $allowedVariants = array_keys($variantOptions);

            $validator = Validator::make([
                'variant_type'    => $variantType,
                'product_type_id' => $productTypeId,
                'name'            => $name,
            ], [
                'variant_type'    => ['required', 'string', 'in:' . implode(',', $allowedVariants)],
                'product_type_id' => ['nullable', 'integer', 'min:0'],
                'name'            => ['nullable', 'string', 'min:3', 'max:255'],
            ], [
                'variant_type.required' => 'Selecione uma categoria principal para iniciar a busca.',
                'variant_type.in'       => 'Categoria principal inválida.',
                'name.min'              => 'Use pelo menos 3 caracteres para buscar por nome.',
                'name.max'              => 'O nome deve ter no máximo 255 caracteres.',
            ]);

            // Enforce that the chosen sub-type belongs to the chosen variant.
            $validator->after(function ($v) use ($variantType, $productTypeId, $productTypes) {
                if ($productTypeId <= 0) {
                    return;
                }
                foreach ($productTypes as $option) {
                    if ((int) $option['id'] === $productTypeId) {
                        if ((string) ($option['variant_type'] ?? '') !== $variantType) {
                            $v->errors()->add(
                                'product_type_id',
                                'O sub-tipo selecionado não pertence à categoria principal escolhida.'
                            );
                        }
                        return;
                    }
                }
                $v->errors()->add('product_type_id', 'Sub-tipo inválido.');
            });

            if ($validator->fails()) {
                return redirect()
                    ->route('admin.products.index')
                    ->withErrors($validator)
                    ->withInput();
            }

            try {
                $result = $this->productService->getProduct([
                    'variant_type'    => $variantType,
                    'product_type_id' => $productTypeId,
                    'name'            => $name,
                ]);
                $products = is_array($result) ? $result : [];
            } catch (ResourceNotFoundException $e) {
                $products = [];
            } catch (PersistenceErrorException $e) {
                $errorMessage = 'Não foi possível buscar os produtos.';
            }
        }

        return view('admin.product.index', [
            'variantOptions' => $variantOptions,
            'productTypes'   => $productTypes,
            'products'       => $products,
            'hasSearch'      => $hasSearch,
            'variantType'    => $variantType,
            'productTypeId'  => $productTypeId,
            'name'           => $name,
            'errorMessage'   => $errorMessage,
        ]);
    }

    /**
     * GET /admin/products/create
     */
    public function create()
    {
        return view('admin.product.create', [
            'productTypes' => $this->getProductTypeOptions(true),
        ]);
    }

    /**
     * POST /admin/products
     */
    public function store(CreateAdminProductRequest $request)
    {
        try {
            $result = $this->productService->createProduct($this->buildProductPayload($request));
        } catch (ResourceNotFoundException | BusinessRuleException | InvalidParametersException $e) {
            return redirect()
                ->route('admin.products.create')
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (PersistenceErrorException $e) {
            return redirect()
                ->route('admin.products.create')
                ->withInput()
                ->with('error', 'Não foi possível salvar o produto.');
        }

        $id = $result['id'] ?? null;

        return redirect()
            ->route('admin.products.edit', ['id' => $id])
            ->with('success', 'Produto criado com sucesso.');
    }

    /**
     * GET /admin/products/{id}/edit
     */
    public function edit(int $id)
    {
        try {
            $product = $this->productService->getProductById($id);
        } catch (ResourceNotFoundException $e) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Produto não encontrado.');
        }

        return view('admin.product.edit', [
            'product'      => $product,
            'productTypes' => $this->getProductTypeOptions(true),
        ]);
    }

    /**
     * PUT /admin/products/{id}
     */
    public function update(int $id, UpdateAdminProductRequest $request)
    {
        try {
            $this->productService->updateProduct($id, $this->buildProductPayload($request));
        } catch (ResourceNotFoundException | BusinessRuleException | InvalidParametersException $e) {
            return redirect()
                ->route('admin.products.edit', ['id' => $id])
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (PersistenceErrorException $e) {
            return redirect()
                ->route('admin.products.edit', ['id' => $id])
                ->withInput()
                ->with('error', 'Não foi possível salvar o produto.');
        }

        return redirect()
            ->route('admin.products.edit', ['id' => $id])
            ->with('success', 'Produto atualizado com sucesso.');
    }

    /**
     * DELETE /admin/products/{id}
     */
    public function destroy(int $id, Request $request)
    {
        try {
            $this->productService->deleteProduct(['id' => $id]);
        } catch (ResourceNotFoundException | BusinessRuleException | InvalidParametersException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        } catch (PersistenceErrorException $e) {
            return redirect()
                ->back()
                ->with('error', 'Não foi possível remover o produto.');
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto removido.');
    }

    /**
     * Builds a normalized payload from the FormRequest input, parsing the
     * structured fields (dimensions) and coercing booleans.
     */
    private function buildProductPayload(Request $request): array
    {
        $payload = [
            'product_type_id'   => (int) $request->input('product_type_id', 0),
            'name'              => (string) $request->input('name', ''),
            'sku'               => (string) $request->input('sku', ''),
            'description'       => (string) $request->input('description', ''),
            'short_description' => (string) $request->input('short_description', ''),
            'brand'             => $request->filled('brand') ? (string) $request->input('brand') : null,
            'model'             => $request->filled('model') ? (string) $request->input('model') : null,
            'avg_weight'        => $request->filled('avg_weight') ? (float) $request->input('avg_weight') : null,
            'avg_dimensions'    => [
                'width'  => (float) $request->input('avg_dimensions.width', 0),
                'height' => (float) $request->input('avg_dimensions.height', 0),
                'length' => (float) $request->input('avg_dimensions.length', 0),
                'unit'   => (string) $request->input('avg_dimensions.unit', 'cm'),
            ],
            'total_stock'       => $request->filled('total_stock') ? (int) $request->input('total_stock') : 0,
            'min_stock'         => $request->filled('min_stock') ? (int) $request->input('min_stock') : 0,
            'meta_title'        => $request->filled('meta_title') ? (string) $request->input('meta_title') : null,
            'meta_description'  => $request->filled('meta_description') ? (string) $request->input('meta_description') : null,
            'active'            => $request->boolean('active'),
            'is_featured'       => $request->boolean('is_featured'),
            'is_new'            => $request->boolean('is_new'),
            'has_variants'      => $request->boolean('has_variants'),
            'available_at'      => (string) $request->input('available_at', ''),
        ];

        return $payload;
    }

    /**
     * Returns the fixed list of main categories (variant_type values) used
     * to scope the product search. Maps the enum value to a friendly label.
     *
     * @return array<string, string>
     */
    private function variantCategoryOptions(): array
    {

        return [
            'clothing'    => 'Roupas',
            'electronics' => 'Eletrônicos',
            'furniture'   => 'Móveis',
            'books'       => 'Livros',
        ];
    }

    /**
     * Returns a flat hierarchical list of product types suitable for
     * <select> options. Each entry carries `depth` (0 = root) so the view
     * can indent labels — and a `path` like "Roupas > Camisas".
     *
     * When $onlyWithVariant is true, only types that have a variant_type
     * defined are returned (i.e. only the categories that can actually
     * hold products — the bare roots with variant_type NULL are skipped).
     *
     * @return array<int, array{id:int,name:string,path:string,depth:int,variant_type:?string}>
     */
    private function getProductTypeOptions(bool $onlyWithVariant = false): array
    {
        try {
            $roots = $this->productTypeService->getProductTypes();
        } catch (\Throwable $e) {
            return [];
        }

        $options = [];
        foreach ($roots as $root) {
            $rootArr = is_object($root) ? (array) $root : $root;
            $this->collectTypeOptions($rootArr, '', 0, $options, $onlyWithVariant);
        }

        return $options;
    }

    /**
     * Recursively walks a product type subtree and appends each node
     * (with depth + breadcrumb path) into $options.
     *
     * @param array<string, mixed> $node
     * @param array<int, array<string, mixed>> $options
     */
    private function collectTypeOptions(array $node, string $parentPath, int $depth, array &$options, bool $onlyWithVariant): void
    {
        $id   = (int) ($node['id'] ?? 0);
        $name = (string) ($node['name'] ?? '');
        $variant = $node['variant_type'] ?? null;
        $path = $parentPath === '' ? $name : ($parentPath . ' > ' . $name);

        if (!$onlyWithVariant || !empty($variant)) {
            $options[] = [
                'id'           => $id,
                'name'         => $name,
                'path'         => $path,
                'depth'        => $depth,
                'variant_type' => $variant ? (string) $variant : null,
            ];
        }

        if ($id <= 0) {
            return;
        }

        try {
            $children = $this->productTypeService->getChildProductTypesById($id);
        } catch (\Throwable $e) {
            return;
        }

        foreach ($children['child_product_types'] ?? [] as $child) {
            $childArr = is_object($child) ? (array) $child : $child;
            $this->collectTypeOptions($childArr, $path, $depth + 1, $options, $onlyWithVariant);
        }
    }
}
