# E-com

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![Octane](https://img.shields.io/badge/Octane-Enabled-FF2D20?logo=laravel&logoColor=white)
![Swoole](https://img.shields.io/badge/Swoole-Ready-4479A1?logo=php&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white)
![Tests](https://img.shields.io/badge/Tests-PHPUnit-3776AB?logo=php&logoColor=white)
![JWT](https://img.shields.io/badge/Auth-JWT-000000?logo=jsonwebtokens&logoColor=white)
![Redis](https://img.shields.io/badge/Cache-Redis-DC382D?logo=redis&logoColor=white)
![Swagger](https://img.shields.io/badge/API-Swagger-85EA2D?logo=swagger&logoColor=black)
![Jaeger](https://img.shields.io/badge/Tracing-Jaeger%20%2F%20OpenTelemetry-00A1C9?logo=opentelemetry&logoColor=white)


- ✅ **Arquitetura em camadas** (Controllers, Services, Models, Requests)
- ✅ **Autenticação dual** - Session (web) e JWT (API) com guards separados
- ✅ **Separação de rotas por camada** - Camadas de web e API para organização de projeto
- ✅ **Alta performance** com Laravel Octane + Swoole
- ✅ **Testes unitários e de integração** com Mockery e PHPUnit
- ✅ **Cache distribuído** com Redis
- ✅ **Documentação automática** com Swagger/OpenAPI
- ✅ **Observabilidade** com OpenTelemetry (Jaeger)
- ✅ **Dependency Injection** Service Providers
- ✅ **Validações customizadas** Exception Handling
- ✅ **Ambiente dockerizado** pronto para uso
- ✅ **Rate Limit** Já implementado com exemplo básico.
- ✅ **Regras de perfil** Exemplo com rota DELETE de users na API.

---

## 🗂️ Estrutura do repositório

```
/
├─ docker-compose.yml        # Orquestração dos serviços
├─ README.md                 # Este arquivo
├─ docker/                   # Configuração do otel-collector + dockerfile
└─ laravel/                  # Projeto Laravel #1
   ├─ app/
   ├─ bootstrap/
   ├─ config/
   ├─ database/
   ├─ routes/
   ├─ storage/               # Documentação swagger
   ├─ tests
   ├─ composer.json
   └─ ...
```

---

## 🛠️ Pré-requisitos

- [Docker](https://www.docker.com/)  
- [Docker Compose](https://docs.docker.com/compose/) (v2 recomendado: `docker compose`)  

---

## ⚙️ Configuração inicial

Preencha o COMPOSER_AUTH no docker-compose.yml com um Fine-grained personal access tokens

Na primeira vez que subir o projeto, configure o `.env`:

(OBS): ⚠️ O container de jobs provavelmente vai falhar por não ter banco criado

```bash
cd laravel
cp .env.example .env
```

Depois suba os containers:

```bash
docker compose up -d
```

---

## 🔑 Geração de chaves

Acesse o container da aplicação:

```bash
docker exec -it laravel12-skeleton bash
```

E rode:

```bash
# APP_KEY do Laravel
php artisan key:generate

# JWT_SECRET (se estiver usando JWT Auth)
php artisan jwt:secret
```

---

## 🗄️ Criação do banco de dados

> O banco é criado vazio pelo container MySQL.
> Base **laravel** deve ser criada. Base criada inicialmente no formato **utf8mb4_general_ci**. 
> **As tabelas e dados iniciais são gerados pelo Laravel** via migrations e seeders.

Ainda dentro do container `laravel12-skeleton`, execute:

```bash
# Criar tabelas
php artisan migrate

# Popular o banco com dados de seeders
php artisan db:seed
```

Ou, para recriar do zero já com seeds:

```bash
php artisan migrate:fresh --seed
```

Ao reiniciar containers os serviços que não subiram vão funcionar.

---

## ▶️ Acessando a aplicação

- API Laravel com swoole rodando: [http://localhost:8020/api](http://localhost:8020/api)  
- Swagger: [http://localhost:8020/api/documentation](http://localhost:8020/api/documentation)
- WEB Laravel com artisan serv rodando: [http://localhost:8080](http://localhost:8080)  
- MySQL: `localhost:3306` (usuário root, sem senha)
- Redis: Pode acessar pelo container mesmo assim como na configuração do docker compose
- Jaeger: [http://localhost:16686/search](http://localhost:16686/search)
- queue/scheduler: Devem ser acessados pelo container, pode ver com ``` docker ps ```


---

## 🧩 Comandos úteis

Dentro do container `laravel12-skeleton`:

```bash
# Instalar dependências
composer install

# Limpar caches
php artisan cache:clear
php artisan config:clear

# Rodar servidor embutido (já configurado no docker-compose)
php artisan octane:start --server=swoole --host=0.0.0.0 --port=9000

#Carregar workers novamente.
php artisan octane:reload 
```


## ▶️ Desenvolvendo com SWOOLE
> O Swoole executa aplicações PHP em um **runtime persistente escrito em C**, mantendo o código carregado em memória e evitando o bootstrap do Laravel a cada requisição.
> Isso traz ganhos significativos de performance, porém exige atenção durante o desenvolvimento, pois alterações no código **não são recarregadas automaticamente** por padrão.
> Se estiver em ambiente de desenvolvimento e precisar refletir alterações no código lembre:

### 1.

Entre no container com:

```bash
docker exec -it laravel12-skeleton bash
```

### 2.
Recarregue os workers do octane.

```bash
php artisan octane:reload
```

---

## ✅ Checklist rápido

- [ ] Clonar o repo  
- [ ] `cp laravel/.env.example laravel/.env`  
- [ ] Subir containers com `docker compose up -d`  
- [ ] Acessar container `docker exec -it laravel12-skeleton bash`  
- [ ] Gerar `APP_KEY` e `JWT_SECRET`  
- [ ] Rodar `php artisan migrate --seed`  
- [ ] Testar em [http://localhost:8080](http://localhost:8080) ou tentar acessar o banco localmente. 

Pronto 🎉 Sua aplicação Laravel de auto desempenho estará rodando com banco de dados populado!

---

## 🔒 Checklist de Segurança para Produção

Este skeleton usa configurações simplificadas para desenvolvimento. **Antes de deployar em produção**, certifique-se de:

- [ ] Mover todas as credenciais para variáveis de ambiente (`.env`)
- [ ] Configurar senha forte para o usuário root do MySQL
- [ ] Alterar a senha do Redis (`REDIS_PASSWORD` no `.env`)
- [ ] Configurar `APP_DEBUG=false` no `.env`
- [ ] Gerar chaves fortes (`APP_KEY` e `JWT_SECRET`)
- [ ] Configurar HTTPS/TLS
- [ ] Revisar permissões de arquivos e diretórios
- [ ] Configurar CORS adequadamente
- [ ] Implementar rate limiting nas rotas de API
- [ ] Revisar e atualizar dependências (`composer update`)
- [ ] Configurar backups automáticos do banco de dados
- [ ] Implementar logs de auditoria
- [ ] Remover ou proteger a rota `/api/documentation` do Swagger

---


## ❌ Quando NÃO usar Octane/Swoole

Não use Octane se:

- [ ] Seu projeto é CRUD simples
- [ ] Não há carga concorrente
- [ ] Equipe não entende processo vivo

## 🔧 Arquitetura de execução

- Web (Blade): request-response tradicional
- API: processo persistente (Octane/Swoole)

```
O projeto não tem exemplos com swoole, apenas roda com ele. O projeto foi feito pensando em aplicações que mantem o processo vivo, então não teremos nenhuma função guardando valor dentro do projeto de maneira que afete o resto do sistema e acabe acontecendo um erro fantasma de memory leak. Nem tudo é necessário usar swoole, sinta-se a vontade para usar como um sistema laravel comum, também funciona bem e está estruturado para os 2 casos, apenas lembre de mudar a execução no docker-compose se quiser, se este for o seu caso, não precisa separar por processo a api do front-end.

Estado permitido:

- dependências
- configurações imutáveis
- cache externo (Redis)

```

## ⚙️ .ENV

### 1. Importante mudar região para ver scheduler funcionando.
```
APP_TIMEZONE=UTC
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
```

### 2. Já está alinhado com o docker-compose, lembre apenas de criar o bd
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Aqui é onde guarda tudo referente a Session, Jobs e cache (O redis é usado do mesmo jeito que foi feito na Service RedisService)
```
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

CACHE_STORE=database
CACHE_DRIVER=database
```

### 4. Config do redis
```
REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=bananinha123
REDIS_PORT=6379
REDIS_DB=0
```

### 5. Config do Email

OBS: MAIL_PASSWORD=null - este campo não é a senha do seu email, é uma senha especifica para poder usar o smtp.
```
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="seu@email.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 6. Observabilidade

Se quiser ligar o Jaeger  mude OTEL_ENABLED para true
```
OTEL_SERVICE_NAME=laravel-app
OTEL_TRACES_EXPORTER=otlp
OTEL_EXPORTER_OTLP_ENDPOINT=http://otel-collector:4318
OTEL_EXPORTER_OTLP_PROTOCOL=http/protobuf
OTEL_PHP_AUTOLOAD_ENABLED=true
OTEL_ENABLED=false
```

## 📄 License

MIT License — veja [LICENSE](LICENSE) para mais detalhes.
