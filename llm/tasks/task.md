## Context (READ FIRST — MANDATORY)

Before writing a single line of code, the LLM MUST read and follow:

- `llm/context/frontend-patterns.md` — flow, layers, Blade rules, UI library, auth, **`web.permission` middleware**
- `llm/context/architecture.md` — layered architecture (Controller → Service → UseCase → Repository)
- `llm/context/api-patterns.md` — only relevant for cross-checking; this task does NOT touch the API
- `llm/rules/validation.md` — FormRequest validation rules


## Taks

Preciso de uma pagina de admin/products para a administração de produtos dentro do sistema.
A tela vai se iniciar com uma tabela de busca de produto, na primeira vez que entrar na tela não será retornado nenhum dado, o usuário vai precisar pesquisar através do tipo do produto (roupas, eletronicos, etc) e do seu nome se quiser, no qual é preciso de pelo menos 3 digitos para buscar pois é feito com like. A pesquisa vai ser obrigatório ter um tipo selecionado.

A tela vai permitir deletar, editar e criar um novo produto. A tela de edição e criação vai ser separado da tela de listagem. Nesta tela vai ter interações com a tabela de products e vai ter uma opção que possibilita o usuario adicionar variantes do produto caso já esteja cadastrado abrindo outra tela ligada pelo id do produto que vai interagir com a tela de product_variants onde teremos uma listagem das variantes do produto. A nessa tela teremos interações para selecionar uma variante para editar que nos levara para a tela da edição da variante ou adicionar uma nova. Variantes poderão ser excluidas.


## Atenção

- já existe um backend escrito para estes cenários, pode usar os usecases para completar a tarefa e não repitir código
- não existe um endpoint para listar todas as variantes de um produto, esta é a unica rota que deverá ser criado.
    Route::get('v1/product/{product_id}/variant/', [ProductVariantController::class, 'getVariant']);

## Adicionais

Endpoints que poderão ser utilizados como ajuda

//Product Type
Route::get('v1/product/types', [ProductTypeController::class, 'getProductTypes']);

//Product
Route::post('v1/product', [ProductController::class, 'createProduct']);
Route::get('v1/product', [ProductController::class, 'getProduct']);
Route::put('v1/product/{id}', [ProductController::class, 'updateProduct']);
Route::get('v1/product/{id}', [ProductController::class, 'getProductById']);
Route::delete('v1/product/{id}', [ProductController::class, 'deleteProduct']);

//Product Variant
Route::get('v1/product/{product_id}/variant/{id}', [ProductVariantController::class, 'getVariant']);
Route::post('v1/product/{product_id}/variant', [ProductVariantController::class, 'addVariant']);
Route::put('v1/product/{product_id}/variant/{id}', [ProductVariantController::class, 'updateVariant']);
Route::delete('v1/product/{product_id}/variant/{id}', [ProductVariantController::class, 'deleteVariant']);
