<?php

namespace App\Http\Controllers\Web\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminProductTypeRequest;
use App\Http\Requests\Admin\ChangeAdminProductTypeStatusRequest;
use App\Services\ProductTypeService;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\InvalidParametersException;
use App\Exceptions\PersistenceErrorException;

class AdminProductTypeController extends Controller
{
    public function __construct(
        private ProductTypeService $productTypeService,
    ) {}

    /**
     * GET /admin/product/types
     * Lists root product types.
     */
    public function index()
    {
        $productTypes = [];

        try {
            $productTypes = $this->productTypeService->getProductTypes();
        } catch (ResourceNotFoundException $e) {
            $productTypes = [];
        }

        return view('admin.productType.index', [
            'productTypes' => $productTypes,
            'variantTypes' => $this->variantTypeOptions(),
        ]);
    }

    /**
     * GET /admin/product/types/{id}
     * Shows a single product type and its children.
     */
    public function show(int $id)
    {
        try {
            $data = $this->productTypeService->getChildProductTypesById($id);
        } catch (ResourceNotFoundException $e) {
            return redirect()
                ->route('admin.types.index')
                ->with('error', 'Tipo de produto não encontrado.');
        }

        $productType = $data['product_type'] ?? [];
        $effectiveVariantType = $this->resolveEffectiveVariantType($productType);

        return view('admin.productType.show', [
            'productType'          => $productType,
            'childProductTypes'    => $data['child_product_types'] ?? [],
            'variantTypes'         => $this->variantTypeOptions(),
            'effectiveVariantType' => $effectiveVariantType,
        ]);
    }

    /**
     * POST /admin/product/types/{id}/child
     * Creates a child product type under the given parent.
     *
     * variant_type is ALWAYS derived from the parent — the request value is
     * ignored to avoid bugs where a child ends up with a variant that does
     * not match the parent's domain.
     */
    public function store(int $id, CreateAdminProductTypeRequest $request)
    {
        try {
            $parent = $this->productTypeService->getProductTypeById($id);
        } catch (ResourceNotFoundException $e) {
            return redirect()
                ->route('admin.types.index')
                ->with('error', $e->getMessage());
        }

        $parentVariantType = $this->resolveEffectiveVariantType($parent);

        if ($parentVariantType === '') {
            return redirect()
                ->route('admin.types.show', ['id' => $id])
                ->withInput()
                ->with('error', 'O tipo pai não possui variante definida. Defina a variante do tipo pai antes de criar sub-tipos.');
        }

        $payload = [
            'id'           => $id,
            'name'         => (string) $request->input('name', ''),
            'slug'         => (string) $request->input('slug', ''),
            'description'  => (string) $request->input('description', ''),
            'variant_type' => $parentVariantType,
        ];

        try {
            $this->productTypeService->createChildProductType($payload);
        } catch (ResourceNotFoundException $e) {
            return redirect()
                ->route('admin.types.show', ['id' => $id])
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (InvalidParametersException $e) {
            return redirect()
                ->route('admin.types.show', ['id' => $id])
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (PersistenceErrorException $e) {
            return redirect()
                ->route('admin.types.show', ['id' => $id])
                ->withInput()
                ->with('error', 'Não foi possível salvar o tipo de produto.');
        }

        return redirect()
            ->route('admin.types.show', ['id' => $id])
            ->with('success', 'Tipo de produto criado com sucesso.');
    }

    /**
     * PATCH /admin/product/types/{id}/status
     * Toggles the activation status of a product type.
     */
    public function toggleStatus(int $id, ChangeAdminProductTypeStatusRequest $request)
    {
        $status = strtoupper((string) $request->input('status', ''));

        try {
            $this->productTypeService->changeProductTypeActivationStatus([
                'id'     => $id,
                'status' => $status,
            ]);
        } catch (ResourceNotFoundException $e) {
            return redirect()
                ->route('admin.types.index')
                ->with('error', $e->getMessage());
        } catch (InvalidParametersException $e) {
            return redirect()
                ->route('admin.types.index')
                ->with('error', $e->getMessage());
        } catch (PersistenceErrorException $e) {
            return redirect()
                ->route('admin.types.index')
                ->with('error', 'Não foi possível alterar o status do tipo de produto.');
        }

        return redirect()
            ->back()
            ->with('success', 'Status do tipo de produto atualizado.');
    }

    /**
     * DELETE /admin/product/types/{id}
     * Soft-deletes a product type.
     */
    public function destroy(int $id, Request $request)
    {
        try {
            $this->productTypeService->deleteProductTypeById(['id' => $id]);
        } catch (ResourceNotFoundException $e) {
            return redirect()
                ->route('admin.types.index')
                ->with('error', $e->getMessage());
        } catch (InvalidParametersException $e) {
            return redirect()
                ->route('admin.types.index')
                ->with('error', $e->getMessage());
        } catch (PersistenceErrorException $e) {
            return redirect()
                ->route('admin.types.index')
                ->with('error', 'Não foi possível remover o tipo de produto.');
        }

        return redirect()
            ->route('admin.types.index')
            ->with('success', 'Tipo de produto removido.');
    }

    /**
     * Available variant types displayed in the admin forms.
     */
    private function variantTypeOptions(): array
    {
        return [
            'clothing'    => 'Roupas/Calçados',
            'electronics' => 'Eletrônicos',
            'furniture'   => 'Móveis',
            'books'       => 'Livros',
            'simple'      => 'Simples (sem variantes)',
        ];
    }

    /**
     * Resolves the variant_type for a product type, falling back to a fixed
     * mapping for the 4 pre-defined root categories when the DB column is
     * still NULL (e.g. before the backfill migration runs).
     *
     * @param array<string, mixed> $productType
     */
    private function resolveEffectiveVariantType(array $productType): string
    {
        $variant = (string) ($productType['variant_type'] ?? '');
        if ($variant !== '') {
            return $variant;
        }

        $slug = (string) ($productType['slug'] ?? '');
        return $this->rootSlugToVariantFallback()[$slug] ?? '';
    }

    /**
     * Hard-coded mapping for the pre-defined root product types whose
     * variant_type is implicit by domain.
     *
     * @return array<string, string>
     */
    private function rootSlugToVariantFallback(): array
    {
        return [
            'roupas'      => 'clothing',
            'eletronicos' => 'electronics',
            'moveis'      => 'furniture',
            'livros'      => 'books',
        ];
    }
}
