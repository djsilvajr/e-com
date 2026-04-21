# Task: Create Endpoint

##context
Read and follow API patterns in llm/context/api-patterns.md
Read and follow API architecture in llm/context/architecture.md

## Project DIR
- laravel

## Endpoint
GET v1/product/{product_id}/price/
POST v1/product/{product_id}/price/
PUT  v1/product/{product_id}/price/
DELETE v1/product/{product_id}/price/

## Controller
ProductPriceController

## Request
GET v1/product/{product_id}/price/ - GetPriceRequest
POST v1/product/{product_id}/price/ - AddPriceRequest
PUT  v1/product/{product_id}/price/ - UpdatePriceRequest
DELETE v1/product/{product_id}/price/ - DeletePriceRequest

## Validation
Use rules defined in /llm/rules/validation.md

Rules:
- field: required | string | max:255
- product_id: required | int
- best_price: DECIMAL(10,2)
- cost_price: DECIMAL(10,2)
- proft_margin: DECIMAL(10,2)
- promotional_price: DECIMAL(10,2)
- promotional_starts_at: TIMESTAMP
- promotional_ends_at: TIMESTAMP
- currency: BRL pattern
- tax_rate: DECIMAL(5,2)

created_at and updated_at exists.

## Business Rules
GET RULES
product_id must be valid and exist in database

POST RULES
product_id must be valid and exist in database
Currency needs to be BRL
if price already exists to the product can not create other (create price rule)

PUT
product_id must be valid and exist in database
Currency needs to be BRL

DELETE
product_id must be valid and exist in database

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
price:

CREATE TABLE `prices` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`product_id` BIGINT UNSIGNED NOT NULL,
	`base_price` DECIMAL(10,2) NOT NULL,
	`cost_price` DECIMAL(10,2) NULL DEFAULT NULL,
	`profit_margin` DECIMAL(5,2) NULL DEFAULT NULL,
	`promotional_price` DECIMAL(10,2) NULL DEFAULT NULL,
	`promotional_starts_at` TIMESTAMP NULL DEFAULT NULL,
	`promotional_ends_at` TIMESTAMP NULL DEFAULT NULL,
	`compare_at_price` DECIMAL(10,2) NULL DEFAULT NULL,
	`currency` VARCHAR(3) NOT NULL DEFAULT 'BRL' COLLATE 'utf8mb4_unicode_ci',
	`tax_rate` DECIMAL(5,2) NULL DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `prices_product_id_unique` (`product_id`) USING BTREE,
	INDEX `prices_product_id_index` (`product_id`) USING BTREE,
	INDEX `prices_promotional_ends_at_index` (`promotional_ends_at`) USING BTREE,
	INDEX `prices_base_price_promotional_price_index` (`base_price`, `promotional_price`) USING BTREE,
	CONSTRAINT `prices_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
AUTO_INCREMENT=9
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


