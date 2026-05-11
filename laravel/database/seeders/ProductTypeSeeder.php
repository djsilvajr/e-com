<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            // CATEGORIA RAIZ: ROUPAS
            [
                'id' => 1,
                'name' => 'Roupas',
                'slug' => 'roupas',
                'description' => 'Todas as peças de vestuário',
                'parent_id' => null,
                'variant_type' => 'clothing',
                'order' => 1,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Camisas',
                'slug' => 'camisas',
                'description' => 'Camisas masculinas e femininas',
                'parent_id' => 1,
                'variant_type' => 'clothing',
                'order' => 1,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Camisas Esportivas',
                'slug' => 'camisas-esportivas',
                'description' => 'Camisas para prática esportiva',
                'parent_id' => 2,
                'variant_type' => 'clothing',
                'order' => 1,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Calças',
                'slug' => 'calcas',
                'description' => 'Calças de todos os estilos',
                'parent_id' => 1,
                'variant_type' => 'clothing',
                'order' => 2,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // CATEGORIA RAIZ: ELETRÔNICOS
            [
                'id' => 10,
                'name' => 'Eletrônicos',
                'slug' => 'eletronicos',
                'description' => 'Produtos eletrônicos e tecnologia',
                'parent_id' => null,
                'variant_type' => 'electronics',
                'order' => 2,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'name' => 'Notebooks',
                'slug' => 'notebooks',
                'description' => 'Notebooks e laptops',
                'parent_id' => 10,
                'variant_type' => 'electronics',
                'order' => 1,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 12,
                'name' => 'Smartphones',
                'slug' => 'smartphones',
                'description' => 'Celulares e smartphones',
                'parent_id' => 10,
                'variant_type' => 'electronics',
                'order' => 2,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // CATEGORIA RAIZ: MÓVEIS
            [
                'id' => 20,
                'name' => 'Móveis',
                'slug' => 'moveis',
                'description' => 'Móveis para casa e escritório',
                'parent_id' => null,
                'variant_type' => 'furniture',
                'order' => 3,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 21,
                'name' => 'Sofás',
                'slug' => 'sofas',
                'description' => 'Sofás e poltronas',
                'parent_id' => 20,
                'variant_type' => 'furniture',
                'order' => 1,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 22,
                'name' => 'Mesas',
                'slug' => 'mesas',
                'description' => 'Mesas para sala e escritório',
                'parent_id' => 20,
                'variant_type' => 'furniture',
                'order' => 2,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // CATEGORIA RAIZ: LIVROS
            [
                'id' => 30,
                'name' => 'Livros',
                'slug' => 'livros',
                'description' => 'Livros e publicações',
                'parent_id' => null,
                'variant_type' => 'books',
                'order' => 4,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 31,
                'name' => 'Ficção',
                'slug' => 'ficcao',
                'description' => 'Livros de ficção e fantasia',
                'parent_id' => 30,
                'variant_type' => 'books',
                'order' => 1,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 32,
                'name' => 'Técnicos',
                'slug' => 'tecnicos',
                'description' => 'Livros técnicos e educacionais',
                'parent_id' => 30,
                'variant_type' => 'books',
                'order' => 2,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('product_types')->insert($categories);

        $this->command->info('✅ Categorias criadas com sucesso!');
    }
}