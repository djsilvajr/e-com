<?php

namespace Tests\Unit\Product;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Services\Product\GetProductById;
use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\BusinessRuleException;

class GetProductByIdTest extends TestCase
{
    private $productRepo;
    private GetProductById $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepo = Mockery::mock(ProductInterface::class);
        $this->useCase = new GetProductById($this->productRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_should_return_data_when_valid_input()
    {
        $id = 1;
        $product = [
            'id' => $id,
            'name' => 'Test Product',
            'deleted_at' => null,
        ];

        $this->productRepo
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($product);

        $result = $this->useCase->execute($id);

        $this->assertIsArray($result);
        $this->assertEquals($id, $result['id']);
        $this->assertEquals('Test Product', $result['name']);
    }

    public function test_should_throw_exception_when_not_found()
    {
        $id = 1;

        $this->productRepo
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        $this->expectException(ResourceNotFoundException::class);

        $this->useCase->execute($id);
    }

    public function test_should_throw_business_rule_when_product_deleted()
    {
        $id = 1;
        $product = [
            'id' => $id,
            'name' => 'Deleted Product',
            'deleted_at' => '2026-03-01',
        ];

        $this->productRepo
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($product);

        $this->expectException(BusinessRuleException::class);

        $this->useCase->execute($id);
    }

    public function test_should_throw_error_when_interface_returns_invalid_data()
    {
        $id = 1;
        $product = (object) ['id' => $id, 'name' => 'Obj Product', 'deleted_at' => null];

        $this->productRepo
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($product);

        $this->expectException(\Error::class);

        $this->useCase->execute($id);
    }
}
