<?php

namespace Tests\Unit\ProductType;

use App\Exceptions\ResourceNotFoundException;
use Tests\TestCase;
use Mockery;
use App\Repository\Contracts\ProductTypeInterface;
use App\Services\ProductType\ChangeProductTypeActivationStatus;
use App\Services\ProductType\Rules\ProductTypeMustNotBeDeleted;
use Carbon\Carbon;

class ChangeProductTypeActivationStatusTest extends TestCase
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

    private function requestMock(int $id, string $status = 'TRUE') : array
    {
        return array(
            'id' => $id,
            'status' => $status,
        );
    }

    public function test_change_status_on_success(): void
    {
        $id = 1;
        $request = $this->requestMock($id, 'TRUE');
        $mockData = $this->productTypeMock($id);

        $productTypeRepositoryMock = Mockery::mock(ProductTypeInterface::class);
        $productTypeRepositoryMock->shouldReceive('findProductTypeById')->once()->with($id)->andReturn($mockData);
        $productTypeRepositoryMock->shouldReceive('updateProductTypeStatus')->once()->with($id, true)->andReturn(true);
        $this->app->instance(ProductTypeInterface::class, $productTypeRepositoryMock);

        $productTypeMustNotBeDeletedMock = Mockery::mock(ProductTypeMustNotBeDeleted::class);
        $productTypeMustNotBeDeletedMock->shouldReceive('validate')->once()->with($mockData[0])->andReturnNull();
        $this->app->instance(ProductTypeMustNotBeDeleted::class, $productTypeMustNotBeDeletedMock);

        $service = $this->app->make(ChangeProductTypeActivationStatus::class);
        $result = $service->execute($request);

        $this->assertTrue($result);
    }

    public function test_change_status_throws_resource_not_found_when_id_does_not_exist(): void
    {
        $id = 999;
        $request = $this->requestMock($id, 'TRUE');

        $productTypeRepositoryMock = Mockery::mock(ProductTypeInterface::class);
        $productTypeRepositoryMock->shouldReceive('findProductTypeById')->once()->with($id)->andReturn([]);
        $this->app->instance(ProductTypeInterface::class, $productTypeRepositoryMock);

        $service = $this->app->make(ChangeProductTypeActivationStatus::class);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Parent id not found');
        
        $service->execute($request);
    }

    public function test_change_status_throws_exception_when_product_is_deleted(): void
    {
        $id = 1;
        $request = $this->requestMock($id, 'TRUE');
        $mockData = $this->productTypeMock($id, true, Carbon::now()->format('Y-m-d'));

        $productTypeRepositoryMock = Mockery::mock(ProductTypeInterface::class);
        $productTypeRepositoryMock->shouldReceive('findProductTypeById')->once()->with($id)->andReturn($mockData);
        $this->app->instance(ProductTypeInterface::class, $productTypeRepositoryMock);

        // Aqui testamos a integração com a Rule real ou mockamos a Rule para lançar a exceção
        $productTypeMustNotBeDeletedMock = Mockery::mock(ProductTypeMustNotBeDeleted::class);
        $productTypeMustNotBeDeletedMock->shouldReceive('validate')
            ->once()
            ->with($mockData[0])
            ->andThrow(new ResourceNotFoundException('Product type is deleted'));
        $this->app->instance(ProductTypeMustNotBeDeleted::class, $productTypeMustNotBeDeletedMock);

        $service = $this->app->make(ChangeProductTypeActivationStatus::class);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Product type is deleted');

        $service->execute($request);
    }

    public function test_change_status_to_false_on_success(): void
    {
        $id = 1;
        $request = $this->requestMock($id, 'FALSE');
        $mockData = $this->productTypeMock($id, true);

        $productTypeRepositoryMock = Mockery::mock(ProductTypeInterface::class);
        $productTypeRepositoryMock->shouldReceive('findProductTypeById')->once()->with($id)->andReturn($mockData);
        $productTypeRepositoryMock->shouldReceive('updateProductTypeStatus')->once()->with($id, false)->andReturn(true);
        $this->app->instance(ProductTypeInterface::class, $productTypeRepositoryMock);

        $productTypeMustNotBeDeletedMock = Mockery::mock(ProductTypeMustNotBeDeleted::class);
        $productTypeMustNotBeDeletedMock->shouldReceive('validate')->once()->with($mockData[0])->andReturnNull();
        $this->app->instance(ProductTypeMustNotBeDeleted::class, $productTypeMustNotBeDeletedMock);

        $service = $this->app->make(ChangeProductTypeActivationStatus::class);
        $result = $service->execute($request);

        $this->assertTrue($result);
    }
}
