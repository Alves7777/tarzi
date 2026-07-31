# Zeivoll Signage

Admin Laravel + Filament para gerenciar anuncios em telas de elevador (DOOH). O app Flutter (`zeivoll-display`) consome a API de layout.

## Requisitos

- PHP 8.3+
- Composer
- SQLite (padrao) ou MySQL

## Instalacao

```bash
cd ~/Documents/zeivoll-signage
composer install
cp .env.example .env   # se necessario
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve --host=0.0.0.0 --port=8000
```

Admin Filament: http://localhost:8000/admin

- **Email:** admin@zeivoll.com.br
- **Senha:** password

## API

```
GET /api/v1/screens/{uuid}/feed
```

Tela demo (seed): `11111111-2222-3333-4444-555555555555`

Resposta inclui carrossel principal, 3 laterais, 2 rodape, hora e cambio USD/EUR.

## Estrutura (Clean Architecture)

```
app/
  Domain/          # Enums e regras de negocio
  Application/     # Services, DTOs
  Http/            # Controllers API
  Filament/        # Admin UI
  Models/          # Eloquent
```

## Cobrancas

- **Anunciantes:** taxa de cadastro (`registration_fee_cents`)
- **Posicionamentos:** preco por slot (`price_cents`)
- **Planos:** mensalidade + preco por slot
- **Faturas:** controle de pagamentos por anunciante

## Flutter

No `zeivoll-display`, escolha modo **Elevador** nas configuracoes e informe:

- URL da API (ex: `http://localhost:8000` ou IP do WSL)
- UUID da tela
