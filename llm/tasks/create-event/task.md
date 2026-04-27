You are a senior backend engineer specialized in Laravel and Clean Architecture.

# Source of Truth

You MUST strictly follow:
- llm\context\architecture.md
- llm\rules\validation.md
- llm\context\api-patterns.md

If there is ANY conflict, architecture.md is the highest priority.

---

# Task

Create a new Event flow.
Event should run when container in docker-composer is executed

## Input

Event Name: SaveProductPriceHistory
Domain: ProductPrice
Description: Ao salvar preço ou editar preço, insira uma nova linha no histórico de preço baseado nesta tabela:

price_histories:
CREATE TABLE `price_histories` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`product_id` BIGINT UNSIGNED NOT NULL,
	`product_variant_id` BIGINT UNSIGNED NULL DEFAULT NULL,
	`price_type` ENUM('base','variant_adjustment','promotional','cost') NOT NULL DEFAULT 'base' COLLATE 'utf8mb4_unicode_ci',
	`old_price` DECIMAL(10,2) NOT NULL,
	`new_price` DECIMAL(10,2) NOT NULL,
	`old_cost_price` DECIMAL(10,2) NULL DEFAULT NULL,
	`new_cost_price` DECIMAL(10,2) NULL DEFAULT NULL,
	`old_profit_margin` DECIMAL(5,2) NULL DEFAULT NULL,
	`new_profit_margin` DECIMAL(5,2) NULL DEFAULT NULL,
	`price_difference` DECIMAL(10,2) NULL DEFAULT NULL,
	`percentage_change` DECIMAL(5,2) NULL DEFAULT NULL,
	`change_type` ENUM('manual','automatic','bulk','promotional','cost_adjustment','competitor','seasonal','clearance') NOT NULL DEFAULT 'manual' COLLATE 'utf8mb4_unicode_ci',
	`user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
	`ip_address` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`reason` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`metadata` JSON NULL DEFAULT NULL,
	`changed_at` TIMESTAMP NOT NULL,
	`effective_at` TIMESTAMP NULL DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `price_histories_product_id_index` (`product_id`) USING BTREE,
	INDEX `price_histories_product_variant_id_index` (`product_variant_id`) USING BTREE,
	INDEX `price_histories_changed_at_index` (`changed_at`) USING BTREE,
	INDEX `price_histories_product_id_changed_at_index` (`product_id`, `changed_at`) USING BTREE,
	INDEX `price_histories_user_id_index` (`user_id`) USING BTREE,
	INDEX `price_histories_change_type_index` (`change_type`) USING BTREE,
	INDEX `price_histories_price_type_index` (`price_type`) USING BTREE,
	INDEX `price_histories_effective_at_index` (`effective_at`) USING BTREE,
	CONSTRAINT `price_histories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
	CONSTRAINT `price_histories_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
	CONSTRAINT `price_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION ON DELETE SET NULL
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
;


Ajuste o evento UserRegisteredSendEmail com base na arquitetura que passei no contexto também e garanta que esteja correto quando chamada ainda. 

---

# Architecture Enforcement (MANDATORY)

Follow EXACTLY the Event Queue pattern defined in:

→ llm\context\architecture.md

### Required Flow:

Service → Event → Listener → Job → Service → UseCase → Interface → Repository

---

# Hard Constraints

- DO NOT skip layers
- DO NOT place business logic outside UseCase
- DO NOT call UseCase from Controller, Listener, or Job
- Service MUST orchestrate everything
- UseCase MUST contain ALL business rules
- UseCase MUST depend ONLY on Interfaces
- Repository MUST implement Interfaces
- Listener MUST dispatch a Job
- Job MUST call Service (NOT UseCase)
- Repository MUST use Eloquent

---

# What to Generate

Generate full implementation for:

## Event
app/Events/{DOMAIN}/{EVENT_NAME}Event.php

## Listener
app/Listeners/{DOMAIN}/{EVENT_NAME}Listener.php

## Job
app/Jobs/Queue/{DOMAIN}/{EVENT_NAME}Job.php

## Service
app/Services/{DOMAIN}/{EVENT_NAME}Service.php

## UseCase
app/Services/{DOMAIN}/{EVENT_NAME}UseCase.php

## Interface
app/Interfaces/{DOMAIN}/{ENTITY}RepositoryInterface.php

## Repository
app/repository/{ENTITY}Repository.php

## Bindings
- Service Provider binding Interface → Repository

## EventServiceProvider
- Register Event → Listener

---

# Validation Rules

MUST follow:
→ llm\rules\validation.md

---

# Output Rules

MUST follow:
→ llm\context\api-patterns.md

---

# Code Rules

- PSR-12
- Strict types
- Dependency Injection
- Typed returns
- No business logic outside UseCase
- UseCase throws domain exceptions

---

# Output Format

- Show each file separately
- Include file path as comment on top
- Production-ready code only
- No explanations unless necessary

---

# Self-Check (MANDATORY BEFORE OUTPUT)

Before finishing, verify:

- Are all layers respected?
- Is business logic ONLY inside UseCase?
- Is Service free of business rules?
- Does Job call Service (not UseCase)?
- Does UseCase depend only on Interfaces?

If ANY answer is "no", fix before output.
