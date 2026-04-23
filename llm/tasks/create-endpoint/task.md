# Task: Create Endpoint

##context
Read and follow API patterns in llm/context/api-patterns.md
Read and follow API architecture in llm/context/architecture.md

## Project DIR
- laravel

## Endpoint
GET v1/product/{product_id}/variant/{id}
POST v1/product/{product_id}/variant/
PUT  v1/product/{product_id}/variant/{id}
DELETE v1/product/{product_id}/variant/{id}

## Controller
ProductVariantController

## Request
GET v1/product/{product_id}/variant/{id} - GetProductVariantRequest
POST v1/product/{product_id}/variant/ - AddProductVariantRequest
PUT  v1/product/{product_id}/variant/{id} - UpdateProductVariantRequest
DELETE v1/product/{product_id}/variant/{id} - DeleteProductVariantRequest

## Validation
Use rules defined in /llm/rules/validation.md


POST
Rules:
- product_id: required | int
- sku: required | unique | max:255 | string
- name: required | string | max:255
- barcode: required | string | max:255 | unique
- variant_type: ENUM 'clothing','electronics','furniture','simple' (already exists an enum variant type class to the product)
- price_adjustment: DECIMAL(10,2)
- stock: int 
- reserved_stock: int
- min_stock: int
- weight: DECIMAL(8,2)
- dimensions: Json | can be null but only saves on database like {"altura": 90, "largura": 220, "profundidade": 95}


PUT 
- id: int 
- product_id: required | int
- sku: required | unique | max:255 | string
- name: required | string | max:255
- barcode: required | string | max:255 | unique
- variant_type: ENUM 'clothing','electronics','furniture','simple' (already exists an enum variant type class to the product)
- price_adjustment: DECIMAL(10,2)
- stock: int 
- reserved_stock: int
- min_stock: int
- weight: DECIMAL(8,2)
- dimensions: Json | can be null but only saves on database like {"altura": 90, "largura": 220, "profundidade": 95}
- order: int (based on product_id)
- active: tinyint (0 or 1)


created_at and updated_at exists.

## Business Rules
GET RULES
product_id must be valid and exist in database
id from variant must be valid and exist in database

POST RULES
product_id must be valid and exist in database
sku needs to be unique verify in variant/products table
barcode needs to be unique verify in variant/products table
variant_type should be valid based on enum
dimensions when sended aways will save this json pattern in database {"altura": {value_x}, "largura": {value_y}, "profundidade": {value_z}}

PUT
product_id must be valid and exist in database
sku needs to be unique verify in variant/products table
barcode needs to be unique verify in variant/products table
variant_type should be valid based on enum
dimensions when sended aways will save this json pattern in database {"altura": {value_x}, "largura": {value_y}, "profundidade": {value_z}}
id from variant must be valid and exist in database
order should be unique based on product_id, if send a duplicated one the order must be changed from both variants affected

DELETE
id from variant must be valid and exist in database


## Flow
{{FLOW}}

1. Create Route in routes/api.php
2. Call Controller
3. Validate request
4. Call PriceService
5. PriceService calls UseCase (method get, update, create or delete. Use inject dependency pattern just like others domain in project)
6. UseCase calls repository
7. Return result

Rules:
- Must implement all steps in order
- Must NOT skip or invent steps

## Architecture (CRITICAL)

Follow architecture.md strictly.

Key rules:

- Controller MUST call Service
- Service MUST call UseCase
- UseCase MUST contain ALL business logic
- UseCase MUST NOT return HTTP responses
- Repository MUST NOT contain business logic
- Validation MUST be handled only by FormRequest

DO NOT:
- Skip layers
- Put business logic in Controller or Service
- Access Repository from Controller or Service
- Call other domain

## Expected Output (STRICT)

Return ONLY:

- Controller
- Request (FormRequest)
- UseCase
- Service
- Repository (if needed)

Rules:
- All files must be complete
- No explanations
- No comments outside code
- Code must be production-ready



the table we are working on:
product_variants:


CREATE TABLE `product_variants` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`product_id` BIGINT UNSIGNED NOT NULL,
	`sku` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`barcode` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`variant_type` ENUM('clothing','electronics','furniture','simple') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`price_adjustment` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
	`stock` INT NOT NULL DEFAULT '0',
	`reserved_stock` INT NOT NULL DEFAULT '0',
	`min_stock` INT NOT NULL DEFAULT '0',
	`weight` DECIMAL(8,2) NULL DEFAULT NULL,
	`dimensions` JSON NULL DEFAULT NULL,
	`image_url` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`order` INT NOT NULL DEFAULT '0',
	`active` TINYINT(1) NOT NULL DEFAULT '1',
	`is_default` TINYINT(1) NOT NULL DEFAULT '0',
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	`deleted_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `product_variants_sku_unique` (`sku`) USING BTREE,
	UNIQUE INDEX `product_variants_barcode_unique` (`barcode`) USING BTREE,
	INDEX `product_variants_product_id_index` (`product_id`) USING BTREE,
	INDEX `product_variants_sku_index` (`sku`) USING BTREE,
	INDEX `product_variants_barcode_index` (`barcode`) USING BTREE,
	INDEX `product_variants_variant_type_index` (`variant_type`) USING BTREE,
	INDEX `product_variants_product_id_variant_type_index` (`product_id`, `variant_type`) USING BTREE,
	INDEX `product_variants_product_id_active_index` (`product_id`, `active`) USING BTREE,
	INDEX `product_variants_active_stock_index` (`active`, `stock`) USING BTREE,
	INDEX `product_variants_product_id_is_default_index` (`product_id`, `is_default`) USING BTREE,
	CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
AUTO_INCREMENT=22
;

product:

CREATE TABLE `products` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`product_type_id` BIGINT UNSIGNED NOT NULL,
	`name` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`sku` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`description` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`short_description` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`brand` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`model` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`attributes` JSON NULL DEFAULT NULL,
	`avg_weight` DECIMAL(8,2) NULL DEFAULT NULL,
	`avg_dimensions` JSON NULL DEFAULT NULL,
	`total_stock` INT NOT NULL DEFAULT '0',
	`min_stock` INT NOT NULL DEFAULT '0',
	`meta_title` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`meta_description` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`meta_keywords` JSON NULL DEFAULT NULL,
	`active` TINYINT(1) NOT NULL DEFAULT '1',
	`is_featured` TINYINT(1) NOT NULL DEFAULT '0',
	`is_new` TINYINT(1) NOT NULL DEFAULT '0',
	`has_variants` TINYINT(1) NOT NULL DEFAULT '1',
	`available_at` TIMESTAMP NULL DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	`deleted_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `products_sku_unique` (`sku`) USING BTREE,
	INDEX `products_product_type_id_index` (`product_type_id`) USING BTREE,
	INDEX `products_sku_index` (`sku`) USING BTREE,
	INDEX `products_brand_index` (`brand`) USING BTREE,
	INDEX `products_active_is_featured_index` (`active`, `is_featured`) USING BTREE,
	INDEX `products_active_product_type_id_index` (`active`, `product_type_id`) USING BTREE,
	INDEX `products_available_at_index` (`available_at`) USING BTREE,
	CONSTRAINT `products_product_type_id_foreign` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
AUTO_INCREMENT=9
;
