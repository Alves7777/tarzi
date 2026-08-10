# TARZI

Plataforma **DOOH (Digital Out-of-Home)** para gerenciar anúncios em telas de elevador e postes. Painel Laravel + Filament; player Flutter (`zeivoll-display`) consome a API de layout.

## Stack

| Camada | Tecnologia |
|--------|------------|
| Backend | PHP 8.3+, Laravel 13 |
| Admin | Filament 5 + Filament Shield |
| Frontend build | Vite 8, Tailwind CSS 4 |
| Banco padrão | SQLite (MySQL opcional) |
| Player | Flutter (`zeivoll-display`, repositório separado) |
| Arquitetura | Clean Architecture (`Domain/`, `Application/`, `Http/`, `Filament/`) |

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan shield:generate --all   # obrigatório antes do seed
php artisan db:seed
php artisan storage:link
npm install && npm run build
php artisan serve --host=0.0.0.0 --port=8000
```

**Atalho:** `composer setup` | **Dev:** `composer dev`

> `SignageSeeder` copia mídias demo de `../zeivoll-display/assets/ads` se existir.

## URLs

| Ambiente | URL |
|----------|-----|
| Admin | `http://localhost:8000/admin` |
| API | `http://localhost:8000/api/v1/...` |
| Health | `/up` |

## Layout da tela

Cada tela (`DisplayScreen`) monta um feed com:

| Slot | Conteúdo |
|------|----------|
| `main_carousel` | Carrossel principal (múltiplos itens) |
| `sidebar_1/2/3` | 3 laterais (1 item cada) |
| `footer_1/2` | 2 rodapés (1 item cada) |
| Widgets | Relógio, câmbio USD/EUR, QR Code fixo (PIX, Zeivoll Tune, site) |

Tipos de mídia: **imagem**, **vídeo** (upload) ou **YouTube**.

## Fluxo operacional

```
Cadastrar anunciante → Cadastrar tela (UUID) → Criar anúncio → Posicionar slot → Emitir fatura
```

### Admin (Filament)

1. **Anunciante** — nome, contato, taxa de cadastro (`registration_fee_cents`)
2. **Tela** — UUID, local, intervalo do carrossel, QR fixo (`qr_url`, `qr_label`)
3. **Anúncio** — upload ou YouTube; escolher slot, tela e ordem
4. **Posicionamento** — vincular anúncio ↔ tela ↔ slot, vigência, preço
5. **Cobrança** — planos comerciais e faturas (`draft`, `pending`, `paid`, `overdue`, `cancelled`)

### Player Flutter (`zeivoll-display`)

1. Configurar URL da API e UUID da tela
2. Poll: `GET /api/v1/screens/{uuid}/feed`
3. Renderizar layout + widgets
4. Mídia: `GET /api/v1/media/{path}`

### Integração Zeivoll Tune

Telas podem exibir QR apontando para corrida ativa (`qr_url` → app Tune).

## API

| Método | Rota | Função |
|--------|------|--------|
| GET | `/api/v1/screens/{uuid}/feed` | Feed completo da tela |
| GET | `/api/v1/forex` | Câmbio USD/EUR (cache 15 min) |
| GET | `/api/v1/media/{path}` | Arquivos de `storage/app/public/` |

**Tela demo (seed):** UUID `11111111-2222-3333-4444-555555555555` — Poste Demo, Fortaleza-CE

## Papéis admin

| Papel | Descrição |
|-------|-----------|
| `super_admin` | Acesso total (todas permissões Shield) |
| `panel_user` | Acesso básico ao painel |

## Usuário seed

| Campo | Valor |
|-------|-------|
| Email | `admin@zeivoll.com.br` |
| Senha | `password` |
| Papel | `super_admin` |

## Dados demo (após seed)

- 4 anunciantes (Tarzi, Sertanus, LB Soluções, Zeivoll Tune)
- 11 anúncios (carrossel + laterais + rodapés)
- Plano "Plano Básico" (`slug: basico`)
- 1 fatura pendente (Sertanus, R$ 539,00)

## Grupos do painel

| Grupo | Recursos |
|-------|----------|
| Anúncios | Anunciantes, Telas, Anúncios, Posicionamentos |
| Cobranças | Planos, Faturas |
| Administração | Usuários, Papéis, Activity Log, Logs, Settings, Sessões |

## Arquivos-chave

```
app/Application/Services/DisplayFeedService.php  # Monta feed da tela
app/Application/Services/ForexRateService.php     # Câmbio com fallback
app/Domain/Enums/AdPlacement.php               # Slots do layout
app/Models/Advertiser.php, DisplayScreen.php, Advertisement.php, AdPlacement.php
routes/api.php
config/filament-shield.php
```

## Git

- **Remote:** `git@github.com:Alves7777/tarzi.git`
- **Branch:** `feature/new-admin`
