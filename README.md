# Seguros — sistema para corretora de seguros

Projeto em **PHP 8.3 + Laravel** para gerenciar clientes e apólices de uma corretora, com API REST, painel web e aviso automático de vencimento por e-mail e WhatsApp.

Montei este projeto para praticar o que se usa no dia a dia de um time de desenvolvimento PHP: Laravel/MVC, MySQL, testes automatizados, Docker, CI, integração de APIs e cuidados com dados pessoais (LGPD).

## O que tem

| Área | O que foi feito |
|------|-----------------|
| Back-end | Laravel 13, PHP 8.3, Eloquent, Form Requests, API Resources, Services |
| Front-end | Blade + Tailwind CSS (Vite), layout responsivo |
| Banco de dados | MySQL (produção/Docker) e SQLite (dev/testes), migrations, factories e seeders |
| API REST | CRUD de `clientes` e `apolices`, paginação, filtros, autenticação por chave (`X-API-Key`) |
| Integrações | WhatsApp (API REST via `Http` client) e e-mail (Mailable), com registro de sucesso/falha |
| Tarefa agendada | `apolices:avisar-vencimento` roda todo dia às 09:00 e avisa quem vence em N dias |
| Testes | PHPUnit: unitários, API, painel e integrações (com `Http::fake` e `Mail::fake`) |
| DevOps | Dockerfile multi-stage, `docker-compose` (app + MySQL + scheduler), GitHub Actions (Pint + testes + build) |
| LGPD | CPF criptografado no banco, máscara na API/painel, ver [LGPD.md](LGPD.md) |

## Como rodar

### Local (SQLite)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Painel em `http://localhost:8000` (usuário/senha do `ADMIN_EMAIL` / `ADMIN_PASSWORD` do `.env`, via HTTP Basic).

### Docker (MySQL)

```bash
cp .env.example .env
# gere uma chave: php artisan key:generate --show  -> cole em APP_KEY no .env
docker compose up --build
```

Sobem três serviços: `app` (Apache + PHP), `db` (MySQL 8.4) e `scheduler` (tarefas agendadas).

## API

Todas as rotas exigem o cabeçalho `X-API-Key` com o valor de `SEGUROS_API_KEY`.

```bash
curl -X POST http://localhost:8000/api/clientes \
  -H "X-API-Key: troque-esta-chave" -H "Accept: application/json" \
  -d nome="Maria Souza" -d cpf="529.982.247-25" -d email="maria@exemplo.com" -d telefone="(51) 99999-0000"
```

| Método | Rota | Descrição |
|--------|------|-----------|
| GET / POST | `/api/clientes` | lista (paginada) / cria |
| GET / PUT / DELETE | `/api/clientes/{id}` | mostra / atualiza / remove |
| GET / POST | `/api/apolices` | lista (filtros `status`, `ramo`, `cliente_id`) / cria |
| GET / PUT / DELETE | `/api/apolices/{id}` | mostra / atualiza / remove |

## Aviso de vencimento

```bash
php artisan apolices:avisar-vencimento          # usa SEGUROS_DIAS_AVISO (30)
php artisan apolices:avisar-vencimento --dias=7
```

Para cada apólice ativa que vence na data-alvo, envia e-mail e (se o cliente tiver telefone) WhatsApp. Cada canal é registrado na tabela `notificacoes`; uma falha em um canal não impede o outro, e o mesmo aviso não é enviado duas vezes. O WhatsApp é configurado por `WHATSAPP_API_URL` e `WHATSAPP_API_TOKEN`.

## Testes

```bash
php artisan test
./vendor/bin/pint --test   # estilo de código
```

## Decisões de projeto

- **Services para integrações**: `WhatsAppClient` e `NotificadorVencimento` isolam a regra de negócio dos controllers/comandos e facilitam testar com fakes.
- **Hash do CPF**: o CPF é salvo criptografado (`encrypted` cast); um HMAC (`cpf_hash`) permite checar duplicidade sem descriptografar nada.
- **Chave de API simples**: para a API, um middleware com `hash_equals` foi suficiente. Em produção, o próximo passo seria Laravel Sanctum com tokens por usuário.
- **Testes não dependem de rede**: WhatsApp e e-mail são simulados com `Http::fake()` e `Mail::fake()`.
