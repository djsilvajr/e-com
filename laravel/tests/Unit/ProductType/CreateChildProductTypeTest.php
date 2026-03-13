<?php

namespace Tests\Unit\ProductType;

use App\Exceptions\InvalidParametersException;
use App\Exceptions\ResourceNotFoundException;
use Tests\TestCase;
use Mockery;
use App\Repository\Contracts\ProductTypeInterface;
use App\Services\ProductType\CreateChildProductType;
use App\Http\Requests\CreateChildProductType as CreateChildProductTypeRequest;
use Carbon\Carbon;

class CreateChildProductTypeTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function noIssuesClothingVariantTypeMock() : array
    {
        return array(
            array(
                'id' => 1,
                'name' => 'Roupas',
                'slug' => 'roupas',
                'description' => 'camisas masculinas e femininas',
                'parent_id' => NULL,
                'variant_type' => NULL,
                'order' => 1,
                'icon' => NULL,
                'image_url' => NULL,
                'active' => 1,
                'created_at' => Carbon::now()->format('Y-m-d'),
                'updated_at' => Carbon::now()->format('Y-m-d'),
            ),
        );
    }

    private function noIssuesReturnInsertVariantType() : array
    {
        return array(
            "name" => "Meias",
            "slug"=> "meias",
            "description" => "Pares de meias masculinas e femininas",
            "parent_id" => 1,
            "variant_type" => "clothing",
            "active" => false,
            "updated_at" => "2026-03-10T16:21:43.000000Z",
            "created_at" => "2026-03-10T16:21:43.000000Z",
            "id" => 33
        );
    }

    private function noIssueRequestInsertVariantType() : array
    {
        return array(
            'name' => 'Meias',
            'slug' => 'meias',
            'description' => 'Pares de meias masculinas e femininas',
            'variant_type' => 'clothing',
            'id' => 1,
        );
    }

    public function test_insert_on_success(): void
    {

        $parent_id = 1;

        $request = $this->noIssueRequestInsertVariantType();

        $productTypeRepositoryMock = Mockery::mock(ProductTypeInterface::class);
        $productTypeRepositoryMock->shouldReceive('findProductTypeById')->once()->with($parent_id)->andReturn($this->noIssuesClothingVariantTypeMock());
        $productTypeRepositoryMock->shouldReceive('findChildProductTypesById')->once()->with($parent_id)->andReturn();
        $productTypeRepositoryMock->shouldReceive('insertVariantType')->once()->with(
            $request['name'],
            $request['slug'],
            $request['description'],
            $parent_id,
            $request['variant_type']
        )->andReturn($this->noIssuesReturnInsertVariantType());

        $this->app->instance(ProductTypeInterface::class, $productTypeRepositoryMock);

        $service = $this->app->make(CreateChildProductType::class);
        $result = $service->execute($request);

        $this->assertIsArray($result);
        $this->assertEquals(33, $result['id']);
        $this->assertEquals(false, $result['active']);
        $this->assertEquals($request['variant_type'], $result['variant_type']);
        $this->assertEquals($request['id'], $result['parent_id']);
    }

    public function test_product_type_not_found() : void
    {
        $request = $this->noIssueRequestInsertVariantType();
        $request['variant_type'] = 'error';

        $service = $this->app->make(CreateChildProductType::class);

        $this->expectException(InvalidParametersException::class);
        $service->execute($request);
    }

    public function test_execute_throws_resource_not_found_when_parent_id_does_not_exist(): void
    {
        $parent_id = 999;
        $request = $this->noIssueRequestInsertVariantType();
        $request['id'] = $parent_id;

        $productTypeRepositoryMock = Mockery::mock(ProductTypeInterface::class);
        $productTypeRepositoryMock->shouldReceive('findProductTypeById')->once()->with($parent_id)->andReturn([]);
        $this->app->instance(ProductTypeInterface::class, $productTypeRepositoryMock);

        $service = $this->app->make(CreateChildProductType::class);

        $this->expectException(ResourceNotFoundException::class);
        $service->execute($request);
    }

    public function test_validate_throws_invalid_parameters_when_id_is_not_positive_int(): void
    {
        $credentials = $this->noIssueRequestInsertVariantType();
        $credentials['id'] = 0;

        $this->expectException(InvalidParametersException::class);
        CreateChildProductTypeRequest::validate($credentials);
    }

    public function test_validate_throws_invalid_parameters_when_name_is_empty(): void
    {
        $credentials = $this->noIssueRequestInsertVariantType();
        $credentials['name'] = '';

        $this->expectException(InvalidParametersException::class);
        CreateChildProductTypeRequest::validate($credentials);
    }

    public function test_validate_throws_invalid_parameters_when_name_exceeds_max_length(): void
    {
        $credentials = $this->noIssueRequestInsertVariantType();
        $credentials['name'] = str_repeat('a', 256);

        $this->expectException(InvalidParametersException::class);
        CreateChildProductTypeRequest::validate($credentials);
    }

    public function test_validate_throws_invalid_parameters_when_slug_is_empty(): void
    {
        $credentials = $this->noIssueRequestInsertVariantType();
        $credentials['slug'] = '';

        $this->expectException(InvalidParametersException::class);
        CreateChildProductTypeRequest::validate($credentials);
    }

    public function test_validate_throws_invalid_parameters_when_slug_exceeds_max_length(): void
    {
        $credentials = $this->noIssueRequestInsertVariantType();
        $credentials['slug'] = str_repeat('a', 256);

        $this->expectException(InvalidParametersException::class);
        CreateChildProductTypeRequest::validate($credentials);
    }
}
