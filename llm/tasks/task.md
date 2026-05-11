## Context (READ FIRST — MANDATORY)

Before writing a single line of code, the LLM MUST read and follow:

- `llm/context/frontend-patterns.md` — flow, layers, Blade rules, UI library, auth, **`web.permission` middleware**
- `llm/context/architecture.md` — layered architecture (Controller → Service → UseCase → Repository)
- `llm/context/api-patterns.md` — only relevant for cross-checking; this task does NOT touch the API
- `llm/rules/validation.md` — FormRequest validation rules


## Taks

Preciso de uma pagina de admin/types para a administração de variantes dentro do sistema.
Existem alguns tipos pré definidos no sistema que o usuario vai poder definir ao incluir um produto.
Na tela de admin existe a opção Variantes, quero que remova ela e coloque "Tipos de produtos" e em baixo esteja ao invés de Gerenciar variantes do produto, seja "Gerenciar tipos de produto".
A ideia da página é listar os principais tipos, com estes tipos o usuario vai selecionar o tipo pai que ira listar os filhos e assim por diante. Ao selecionar o tipo pai, vai existir a listagem dos filhos e uma opção de adicionar filho que vai trazer um modal para adicionar o filho. Os filhos também podem ter filhos, então vai seguir a mesma lógica.

## Atenção

- Esta tela não vai incluir produtos, apenas vai trabalhar com os tipos
- já existe um backend escrito para os tipos, pode usar os usecases para completar a tarefa e não repitir código


## Adicionais

Os tipos e as possibilidades que são oferecidas estão nestes endpoints

//Product Type
Route::get('v1/product/types', [ProductTypeController::class, 'getProductTypes']);
Route::get('v1/product/type/{id}', [ProductTypeController::class, 'getProductTypeById']);
Route::get('v1/product/type/{id}/child', [ProductTypeController::class, 'getChildProductTypesById']);
Route::post('v1/product/type/{id}/child', [ProductTypeController::class, 'createChildProductType']);
Route::patch('v1/product/type/{id}/status', [ProductTypeController::class, 'changeProductTypeActivationStatus']);
Route::delete('v1/product/type/{id}/', [ProductTypeController::class, 'deleteProductTypeById']);


Existem tipos pré definidos no sistema e são já colocados no inicio do sistema e estão no arquivo de migration do product_types. Os tipos são estes

$table->enum('variant_type', [
    'clothing',      // Roupas/Calçados
    'electronics',   // Eletrônicos
    'furniture',     // Móveis
    'books',         // Livros (sem variantes)
    'simple'         // Produtos simples (sem variantes)
])->nullable(); // Null = categoria pai sem produtos diretos


A ideia é que atributos a mais poderão ser adicionados em tipos especificos, estes atributos estão nas migrations que trabalham com:

clothing_variant_attributes
electronics_variant_attributes
furniture_variant_attributes
books_variant_attributes

O simple vai ser uma saida para o usuario adicionar tipos diferentes sem depender do sistema para funções especificas que poderão ser trabalhadas detalhadamente no sistema.
