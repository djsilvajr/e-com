<?php

namespace Tests\Unit\ProductType;

use App\Exceptions\ResourceNotFoundException;
use Tests\TestCase;
use Mockery;
use App\Repository\Contracts\ProductTypeInterface;
use App\Services\ProductType\DeleteProductTypeById;
use App\Services\ProductType\Rules\ProductTypeMustNotBeDeleted;
use Carbon\Carbon;

class DeleteProductTypeByIdTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function productTypeMock(int $id, bool $active = true, ?string $deletedAt = null) : array
    {
        return array(
            array(
                'id' => $id,
                'name' => 'Roupas',
                'slug' => 'roupas',
                'description' => 'camisas masculinas e femininas',
                'parent_id' => NULL,
                'variant_type' => NULL,
                'order' => 1,
                'icon' => NULL,
                'image_url' => NULL,
                'active' => $active ? 1 : 0,
                'created_at' => Carbon::now()->format('Y-m-d'),
                'updated_at' => Carbon::now()->format('Y-m-d'),
                'deleted_at' => $deletedAt,
            ),
        );
    }

    private function requestMock(int $id) : array
    {
        return array(
            'id' => $id,
        );
    }

    public function test_delete_product_type_on_success(): void
    {
        $id = 1;
        $request = $this->requestMock($id);
        $mockData = $this->productTypeMock($id);

        $productTypeRepositoryMock = Mockery::mock(ProductTypeInterface::class);
        $productTypeRepositoryMock->shouldReceive('findProductTypeById')->once()->with($id)->andReturn($mockData);
        $productTypeRepositoryMock->shouldReceive('deleteProductTypeById')->once()->with($id)->andReturn(true);
        $this->app->instance(ProductTypeInterface::class, $productTypeRepositoryMock);

        $productTypeMustNotBeDeletedMock = Mockery::mock(ProductTypeMustNotBeDeleted::class);
        $productTypeMustNotBeDeletedMock->shouldReceive('validate')->once()->with($mockData[0])->andReturnNull();
        $this->app->instance(ProductTypeMustNotBeDeleted::class, $productTypeMustNotBeDeletedMock);

        $service = $this->app->make(DeleteProductTypeById::class);
        $result = $service->execute($request);

        $this->assertTrue($result);
    }

    public function test_delete_throws_resource_not_found_when_id_does_not_exist(): void
    {
        $id = 999;
        $request = $this->requestMock($id);

        $productTypeRepositoryMock = Mockery::mock(ProductTypeInterface::class);
        $productTypeRepositoryMock->shouldReceive('findProductTypeById')->once()->with($id)->andReturn([]);
        $this->app->instance(ProductTypeInterface::class, $productTypeRepositoryMock);

        $service = $this->app->make(DeleteProductTypeById::class);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Parent id not found');
        
        $service->execute($request);
    }

    public function test_delete_throws_exception_when_product_is_already_deleted(): void
    {
        $id = 1;
        $request = $this->requestMock($id);
        $mockData = $this->productTypeMock($id, false, Carbon::now()->format('Y-m-d H:i:s'));

        $productTypeRepositoryMock = Mockery::mock(ProductTypeInterface::class);
        $productTypeRepositoryMock->shouldReceive('findProductTypeById')->once()->with($id)->andReturn($mockData);
        $this->app->instance(ProductTypeInterface::class, $productTypeRepositoryMock);

        $productTypeMustNotBeDeletedMock = Mockery::mock(ProductTypeMustNotBeDeleted::class);
        $productTypeMustNotBeDeletedMock->shouldReceive('validate')
            ->once()
            ->with($mockData[0])
            ->andThrow(new ResourceNotFoundException('Product type is deleted'));
        $this->app->instance(ProductTypeMustNotBeDeleted::class, $productTypeMustNotBeDeletedMock);

        $service = $this->app->make(DeleteProductTypeById::class);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Product type is deleted');

        $service->execute($request);
    }
}
