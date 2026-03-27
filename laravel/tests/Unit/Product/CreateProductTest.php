<?php

namespace Tests\Unit\Product;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Services\Product\CreateProduct;
use App\Repository\Contracts\ProductInterface;
use App\Repository\Contracts\ProductTypeInterface;
use App\Services\ProductType\Rules\ProductTypeMustNotBeDeleted;
use App\Services\Product\Rules\AvailableAtFutureOrTodayRule;
use App\Services\ProductType\Rules\ProductTypeNeedAParentRule;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\BusinessRuleException;
use Carbon\Carbon;

class CreateProductTest extends TestCase
{
    private $productRepo;
    private $productTypeRepo;
    private $productTypeMustNotBeDeleted;
    private $availableAtRule;
    private $productTypeNeedAParent;
    private CreateProduct $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepo = Mockery::mock(ProductInterface::class);
        $this->productTypeRepo = Mockery::mock(ProductTypeInterface::class);
        $this->productTypeMustNotBeDeleted = new ProductTypeMustNotBeDeleted();
        $this->availableAtRule = new AvailableAtFutureOrTodayRule();
        $this->productTypeNeedAParent = new ProductTypeNeedAParentRule();

        $this->useCase = new CreateProduct(
            $this->productRepo,
            $this->productTypeRepo,
            $this->productTypeMustNotBeDeleted,
            $this->availableAtRule,
            $this->productTypeNeedAParent
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_should_return_data_when_valid_input()
    {
        $data = [
            'product_type_id' => 36,
            'name' => 'Camiseta Básica Algodão',
            'sku' => 'CB-ALG-0003a',
            'available_at' => Carbon::now(),
            'active' => 1,
        ];

        $productTypeList = [['id' => 36, 'deleted' => 0, 'parent_id' => 1]];
        $productTypeFirst = $productTypeList[0];

        $this->productTypeRepo
            ->shouldReceive('findProductTypeById')
            ->once()
            ->with(36)
            ->andReturn($productTypeList);

        $this->productRepo
            ->shouldReceive('findProductBySku')
            ->once()
            ->with('CB-ALG-0003a')
            ->andReturn(null);

        // using real rule implementations; no mocks for rules

        $this->productRepo
            ->shouldReceive('createProduct')
            ->once()
            ->with(Mockery::on(function ($arg) use ($data) {
                return is_array($arg) && ($arg['name'] ?? null) === $data['name'];
            }))
            ->andReturn(['id' => 123, 'name' => $data['name'], 'active' => 1]);

        $result = $this->useCase->execute($data);

        $this->assertIsArray($result);
        $this->assertEquals(123, $result['id']);
        $this->assertEquals('Camiseta Básica Algodão', $result['name']);
        $this->assertEquals(1, $result['active']);
    }

    public function test_should_throw_exception_when_product_type_not_found()
    {
        $this->productTypeRepo
            ->shouldReceive('findProductTypeById')
            ->once()
            ->with(36)
            ->andReturn([]);

        $this->expectException(ResourceNotFoundException::class);

        $this->useCase->execute(['product_type_id' => 36]);
    }

    public function test_should_throw_business_rule_when_duplicate_sku()
    {
        $data = ['product_type_id' => 36, 'sku' => 'CB-ALG-0003a'];

        $productTypeList = [['id' => 36, 'deleted' => 0, 'parent_id' => 1]];

        $this->productTypeRepo
            ->shouldReceive('findProductTypeById')
            ->once()
            ->with(36)
            ->andReturn($productTypeList);

        $this->productRepo
            ->shouldReceive('findProductBySku')
            ->once()
            ->with('CB-ALG-0003a')
            ->andReturn(['id' => 999]);

        $this->expectException(BusinessRuleException::class);

        $this->useCase->execute($data);
    }

    public function test_should_throw_exception_when_available_at_rule_fails()
    {
        $data = ['product_type_id' => 36, 'available_at' => 'invalid-date'];

        $productTypeList = [['id' => 36, 'deleted' => 0, 'parent_id' => 1]];
        $productTypeFirst = $productTypeList[0];

        $this->productTypeRepo
            ->shouldReceive('findProductTypeById')
            ->once()
            ->with(36)
            ->andReturn($productTypeList);

        $this->productRepo
            ->shouldReceive('findProductBySku')
            ->never();

        // using real rule implementations; availableAtRule will throw on invalid date

        $this->expectException(BusinessRuleException::class);

        $this->useCase->execute($data);
    }

    public function test_should_handle_object_returned_by_repository()
    {
        $data = [
            'product_type_id' => 36,
            'name' => 'Object Product',
            'sku' => null,
            'available_at' => null,
        ];

        $productTypeList = [['id' => 36, 'deleted' => 0, 'parent_id' => 1]];
        $productTypeFirst = $productTypeList[0];

        $this->productTypeRepo
            ->shouldReceive('findProductTypeById')
            ->once()
            ->with(36)
            ->andReturn($productTypeList);

        $this->productRepo
            ->shouldReceive('findProductBySku')
            ->never();

        // rules are real instances; no mock expectations

        $created = ['id' => 999, 'name' => 'Object Product', 'active' => 0];

        $this->productRepo
            ->shouldReceive('createProduct')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturn($created);

        $result = $this->useCase->execute($data);

        $this->assertIsArray($result);
        $this->assertEquals(999, $result['id']);
        $this->assertEquals('Object Product', $result['name']);
        $this->assertEquals(0, $result['active']);
    }
}
