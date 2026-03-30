<?php

namespace Tests\Unit\Product;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Services\Product\UpdateProduct;
use App\Repository\Contracts\ProductInterface;
use App\Repository\Contracts\ProductTypeInterface;
use App\Services\ProductType\Rules\ProductTypeMustNotBeDeleted;
use App\Services\Product\Rules\ProductMustNotBeDeleted;
use App\Services\Product\Rules\AvailableAtFutureOrTodayRule;
use App\Services\ProductType\Rules\ProductTypeNeedAParentRule;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\BusinessRuleException;

class UpdateProductTest extends TestCase
{
    private $productRepo;
    private $productTypeRepo;
    private UpdateProduct $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepo = Mockery::mock(ProductInterface::class);
        $this->productTypeRepo = Mockery::mock(ProductTypeInterface::class);

        // Use real rule implementations (rules are domain logic)
        $productTypeMustNotBeDeleted = new ProductTypeMustNotBeDeleted();
        $productMustNotBeDeleted = new ProductMustNotBeDeleted();
        $availableAtRule = new AvailableAtFutureOrTodayRule();
        $productTypeNeedAParent = new ProductTypeNeedAParentRule();

        $this->useCase = new UpdateProduct(
            $this->productRepo,
            $this->productTypeRepo,
            $productTypeMustNotBeDeleted,
            $productMustNotBeDeleted,
            $availableAtRule,
            $productTypeNeedAParent
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_should_return_data_when_valid_input()
    {
        $id = 1;
        $data = [
            'product_type_id' => 36,
            'name' => 'Camiseta Básica Algodão',
            'sku' => 'CB-ALG-0003',
            'available_at' => '2030-01-01T00:00:00Z',
            'active' => 1,
        ];

        $product = ['id' => $id, 'deleted_at' => null];
        $productTypeList = [['id' => 36, 'deleted_at' => null, 'parent_id' => 1]];

        $this->productRepo
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($product);

        $this->productRepo
            ->shouldReceive('findProductBySku')
            ->once()
            ->with('CB-ALG-0003')
            ->andReturn(null);

        $this->productTypeRepo
            ->shouldReceive('findProductTypeById')
            ->once()
            ->with(36)
            ->andReturn($productTypeList);

        $updated = ['id' => $id, 'name' => $data['name'], 'active' => 1];

        $this->productRepo
            ->shouldReceive('updateProduct')
            ->once()
            ->with($id, Mockery::on(function ($arg) use ($data) {
                return is_array($arg) && ($arg['name'] ?? null) === $data['name'];
            }))
            ->andReturn($updated);

        $result = $this->useCase->execute($id, $data);

        $this->assertIsArray($result);
        $this->assertEquals($id, $result['id']);
        $this->assertEquals($data['name'], $result['name']);
        $this->assertEquals(1, $result['active']);
    }

    public function test_should_throw_exception_when_product_not_found()
    {
        $id = 1;
        $data = ['product_type_id' => 36];

        $this->productRepo
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        $this->expectException(ResourceNotFoundException::class);

        $this->useCase->execute($id, $data);
    }

    public function test_should_throw_business_rule_when_sku_belongs_to_other_product()
    {
        $id = 1;
        $data = ['product_type_id' => 36, 'sku' => 'CB-ALG-0003'];

        $product = ['id' => $id, 'deleted_at' => null];
        $existing = ['id' => 999, 'sku' => 'CB-ALG-0003'];
        $productTypeList = [['id' => 36, 'deleted_at' => null, 'parent_id' => 1]];

        $this->productRepo->shouldReceive('findById')->once()->with($id)->andReturn($product);
        $this->productRepo->shouldReceive('findProductBySku')->once()->with('CB-ALG-0003')->andReturn($existing);

        // product type lookup not reached because SKU conflict throws earlier

        $this->expectException(BusinessRuleException::class);

        $this->useCase->execute($id, $data);
    }

    public function test_should_throw_business_rule_when_product_is_deleted()
    {
        $id = 1;
        $data = ['product_type_id' => 36];

        $product = ['id' => $id, 'deleted_at' => '2026-03-01'];

        $this->productRepo
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($product);

        $this->expectException(BusinessRuleException::class);

        $this->useCase->execute($id, $data);
    }

    public function test_should_throw_exception_when_product_type_not_found()
    {
        $id = 1;
        $data = ['product_type_id' => 36];

        $product = ['id' => $id, 'deleted_at' => null];

        $this->productRepo->shouldReceive('findById')->once()->with($id)->andReturn($product);
        $this->productRepo->shouldReceive('findProductBySku')->never();

        $this->productTypeRepo
            ->shouldReceive('findProductTypeById')
            ->once()
            ->with(36)
            ->andReturn([]);

        $this->expectException(ResourceNotFoundException::class);

        $this->useCase->execute($id, $data);
    }

    public function test_should_throw_error_when_interface_returns_invalid_data()
    {
        $id = 1;
        $data = ['product_type_id' => 36, 'sku' => 'CB-ALG-0003'];

        $product = ['id' => $id, 'deleted_at' => null];
        $invalidExisting = (object) ['id' => 999];

        $this->productRepo->shouldReceive('findById')->once()->with($id)->andReturn($product);
        $this->productRepo->shouldReceive('findProductBySku')->once()->with('CB-ALG-0003')->andReturn($invalidExisting);

        // product type lookup not reached because invalid interface return causes error earlier

        $this->expectException(\Error::class);

        $this->useCase->execute($id, $data);
    }
}
