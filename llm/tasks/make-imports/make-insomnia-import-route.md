Você é um especialista em APIs REST, organização de testes e geração de coleções para o Insomnia.

Projeto relacionado no diretório laravel

Sua tarefa é:

1. Analisar rotas Laravel fornecidas no formato:
   Route::method('uri', [Controller::class, 'action']);

2. Agrupar as rotas por domínio com base no último segmento relevante da URL.
   Exemplo:
   - v1/product/{product_id}/price → domínio: PRICE

3. Criar uma estrutura hierárquica de pastas no padrão:
   - DOMÍNIO (uppercase)
     - MÉTODO + URI
       - cenários de teste

4. Para cada rota, gerar cenários mínimos obrigatórios:
   - success (200, 201, etc.)
   - not found (quando aplicável)
   - validation error (para POST/PUT)
   - unauthorized (se fizer sentido)

5. Gerar um JSON válido para importação no Insomnia (Insomnia Import Format v4), contendo:
   - Workspaces
   - Folders (representando domínio e rotas)
   - Requests organizados dentro dos cenários

6. Regras importantes:
   - Cada domínio deve ser uma pasta raiz
   - Cada rota deve ser uma subpasta dentro do domínio
   - Cada cenário deve ser uma request separada
   - Use variáveis de ambiente para parâmetros dinâmicos (ex: {{ product_id }})
   - Definir métodos corretamente (GET, POST, PUT, DELETE)
   - Adicionar exemplos de body para POST/PUT
   - Adicionar exemplos de resposta esperada (como descrição ou metadata)

7. Nomeação:
   - Domínio: UPPERCASE (ex: PRICE)
   - Rotas: "GET v1/product/{product_id}/price"
   - Cenários: "success", "not found", etc.

8. Saída:
   - Retorne apenas o JSON pronto para importação no Insomnia
   - Não explique nada
   - Não inclua texto fora do JSON

Entrada de exemplo:
//User
Route::post('v1/user', [UserController::class, 'insertUser']);
Route::get('v1/user/{id}', [UserController::class, 'getUserById']);
Route::put('v1/user/{id}', [UserController::class, 'putUserById']);
Route::delete('v1/user/{id}', [UserController::class, 'deleteUserById'])->middleware('api.permission:user.delete');
Route::patch('v1/user/{id}', [UserController::class, 'patchUserById']);
//Product Type
Route::get('v1/product/types', [ProductTypeController::class, 'getProductTypes']);
Route::get('v1/product/type/{id}', [ProductTypeController::class, 'getProductTypeById']);
Route::get('v1/product/type/{id}/child', [ProductTypeController::class, 'getChildProductTypesById']);
Route::post('v1/product/type/{id}/child', [ProductTypeController::class, 'createChildProductType']);
Route::patch('v1/product/type/{id}/status', [ProductTypeController::class, 'changeProductTypeActivationStatus']);
Route::delete('v1/product/type/{id}/', [ProductTypeController::class, 'deleteProductTypeById']);

//Product
Route::post('v1/product', [ProductController::class, 'createProduct']);
Route::put('v1/product/{id}', [ProductController::class, 'updateProduct']);
Route::get('v1/product/{id}', [ProductController::class, 'getProductById']);
Route::delete('v1/product/{id}', [ProductController::class, 'deleteProduct']);

//Product Price
Route::get('v1/product/{product_id}/price', [ProductPriceController::class, 'getPrice']);
Route::post('v1/product/{product_id}/price', [ProductPriceController::class, 'addPrice']);
Route::put('v1/product/{product_id}/price', [ProductPriceController::class, 'updatePrice']);
Route::delete('v1/product/{product_id}/price', [ProductPriceController::class, 'deletePrice']);

//Product Variant
Route::get('v1/product/{product_id}/variant/{id}', [ProductVariantController::class, 'getVariant']);
Route::post('v1/product/{product_id}/variant', [ProductVariantController::class, 'addVariant']);
Route::put('v1/product/{product_id}/variant/{id}', [ProductVariantController::class, 'updateVariant']);
Route::delete('v1/product/{product_id}/variant/{id}', [ProductVariantController::class, 'deleteVariant']);

Saída esperada:
JSON completo no formato do Insomnia, com toda a estrutura organizada.
