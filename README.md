# Valtrix — gestão hospitalar

Aplicação monolítica com Laravel 13 e Vue 3, organizada por funcionalidades. Inclui cadastro, login com sessão protegida por CSRF e um dashboard responsivo com dados explicitamente demonstrativos.

> O painel usa dados fictícios apenas para demonstrar a interface. Não armazene nem use dados reais de pacientes nesta base inicial.

## Requisitos

- PHP 8.3 ou superior, extensão `mbstring` e Composer
- Node.js 20.19+ ou 22.12+ e npm
- Extensão PDO do banco escolhido. Para a configuração padrão SQLite, habilite `pdo_sqlite` e `sqlite3`.

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
php artisan migrate
```

Inicie o servidor Laravel e o servidor Vite em terminais separados:

```bash
php artisan serve
npm run dev
```

Acesse `http://localhost:8000`. A sessão usa armazenamento em arquivo por padrão; o SQLite é usado como banco local inicial. Para outro banco, ajuste as variáveis `DB_*` no `.env`.

## Funcionalidades

- **Registro:** nome, e-mail único e senha com confirmação e mínimo de 8 caracteres.
- **Login:** validação das credenciais, opção “manter conectado” e rotação do ID da sessão.
- **Dashboard:** indicadores, gráfico semanal e agenda de exemplo, acessíveis apenas com sessão autenticada.
- **Logout:** encerra a sessão, invalida os dados de sessão e renova o token CSRF.
- **Interface:** Vue 3, Vue Router, Pinia, Axios e VeeValidate; layouts responsivos em português.

## Organização

### Laravel

```text
app/
├── DTOs/Auth/                  # Dados de entrada e resposta
├── Http/Controllers/           # Adaptadores HTTP
├── Http/Requests/Auth/         # Validação de entrada
├── Interfaces/                 # Contratos de repositório
├── Repositories/               # Persistência Eloquent
├── Services/                   # Regras reutilizáveis de autenticação
└── UseCases/                   # Operações de autenticação e dashboard
```

As rotas JSON usam a sessão `web` do Laravel, cookies `HttpOnly` e proteção CSRF, pois a SPA é servida pelo mesmo domínio. Nenhum token de autenticação é guardado no `localStorage`.

### Vue

```text
resources/js/
├── app/                        # Inicialização e Vue Router
├── layouts/                    # Layouts de autenticação e painel
├── modules/
│   ├── auth/                   # Páginas, serviço, store e schemas
│   └── dashboard/              # Página e serviço do painel
└── shared/                     # API HTTP e componentes comuns
```

## Rotas

- `GET /login` e `GET /register`: páginas públicas.
- `POST /api/auth/login` e `POST /api/auth/register`: inicia uma sessão.
- `GET /api/auth/user`: retorna o usuário atual (401 quando não autenticado).
- `POST /api/auth/logout`: encerra uma sessão autenticada.
- `GET /api/dashboard`: dados do painel, exige autenticação.

## Testes e build

```bash
php artisan test
npm run build
```

Para produção, use HTTPS, configure cookies seguros, restrinja o cadastro conforme as regras da instituição e implemente autorização por papéis antes de adicionar dados clínicos. O dashboard atual é apenas um protótipo, não um sistema clínico.