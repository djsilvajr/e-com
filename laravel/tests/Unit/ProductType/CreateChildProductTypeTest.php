<?php

namespace Tests\Unit\ProductType;

use Tests\TestCase;
use Mockery;
use App\Repository\Contracts\ProductTypeInterface;
use App\Services\ProductType\CreateChildProductType;
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
}
