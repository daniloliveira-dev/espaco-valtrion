# Valtrix — gestão hospitalar

Aplicação monolítica com Laravel 13 e Vue 3, organizada por funcionalidades. Inclui cadastro, login com sessão protegida por CSRF e um dashboard responsivo de pacientes e exames.

> Os pacientes e exames do seeder são fictícios. Esta base ainda não implementa autorização por papéis/permissões; não use dados clínicos reais até configurar e auditar essas regras.

## Requisitos

- Docker Desktop ou Docker Engine com Docker Compose
- PHP 8.3 ou superior e Composer (para instalar dependências e o Sail)

## Executar com Laravel Sail

```bash
composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
```

Inicie o Vite:

```bash
./vendor/bin/sail npm run dev -- --host=0.0.0.0
```

Acesse `http://localhost`. O Sail inicia PHP 8.3, MySQL, Redis e Mailpit. A interface do Mailpit fica em `http://localhost:8025`. Para encerrar os containers, execute `./vendor/bin/sail down`.

Os comandos também estão disponíveis pelos scripts Composer: `composer setup` prepara o ambiente Sail e `composer dev` inicia os serviços e o Vite.

## Funcionalidades

- **Registro:** nome, e-mail único e senha com confirmação e mínimo de 8 caracteres.
- **Login:** validação das credenciais, opção “manter conectado” e rotação do ID da sessão.
- **Dashboard:** totais de pacientes e exames, exames pendentes, exames concluídos no mês e lista dos 8 exames recentes, carregados do MySQL pela API autenticada.
- **Pacientes:** busca e filtros por nome, exame e data da consulta; prontuário com informações do paciente, médico, consultas, exames, resultados e documentos anexados.
- **Perfil:** visualização do nome e do e-mail da conta pelo menu das iniciais no cabeçalho.
- **Logout:** encerra a sessão, invalida os dados de sessão e renova o token CSRF.
- **Interface:** Vue 3, Vue Router, Pinia, Axios e VeeValidate; layouts responsivos em português.

## Organização

### Laravel

```text
routes/
├── api.php                     # Endpoints JSON consumidos pelo Vue
└── web.php                     # Fallback que entrega a SPA do Vue
```

Os endpoints funcionais ficam em `routes/api.php` e recebem o prefixo `/api` do Laravel. Eles usam o middleware `web` para manter a sessão e a proteção CSRF da SPA; `routes/web.php` mantém somente a rota de fallback que serve a aplicação Vue.

```text
app/
├── DTOs/Auth/                  # Dados de entrada e resposta
├── Http/Controllers/           # Adaptadores HTTP
├── Http/Requests/Auth/         # Validação de entrada
├── Http/Resources/             # Respostas JSON da API
├── Interfaces/                 # Contratos de repositório
├── Repositories/               # Persistência Eloquent
├── Services/                   # Regras reutilizáveis de autenticação
└── UseCases/                   # Operações de autenticação, pacientes e dashboard
```

Os dados ficam nas tabelas `patients`, `physicians`, `consultations`, `exams` e `exam_documents`. `migrate --seed` cria médicos fictícios, 33 pacientes de demonstração, consultas, exames, resumos sintéticos e documentos de texto privados (laudos, atestados e anexos) para exames concluídos. Os resultados e arquivos seed são marcados como fictícios e não têm valor clínico ou legal. O seeder usa identificadores estáveis e pode ser reexecutado sem duplicar registros.

As consultas entre esses dados usam IDs explícitos (`consultations.patient_id`, `exams.patient_id` e `exam_documents.exam_id`) e joins/filtros por coluna. Os Models não definem relacionamentos Eloquent (`hasMany`, `belongsTo` etc.). As constraints de chave estrangeira no banco permanecem para manter a integridade dos IDs; `exams.consultation_id` preserva o agrupamento dos exames por consulta.

Os fluxos de autenticação, perfil, dashboard e pacientes são consumidos pelo Vue via Axios; o Laravel responde em JSON e persiste os dados de pacientes no MySQL. As rotas JSON usam a sessão `web` do Laravel, cookies `HttpOnly` e proteção CSRF, pois a SPA é servida pelo mesmo domínio. Nenhum token de autenticação é guardado no `localStorage`.

### Vue

```text
resources/js/
├── app/                        # Inicialização e Vue Router
├── layouts/                    # Layouts de autenticação e painel
├── modules/
│   ├── auth/                   # Páginas, serviço, store e schemas
│   ├── dashboard/              # Página e serviço do painel
│   ├── patients/               # Páginas, componentes, composables e serviço
│   └── users/                  # Páginas do usuário
└── shared/                     # API HTTP, componentes e utilitários reutilizáveis
```

## Rotas

- `GET /login` e `GET /register`: páginas públicas.
- `POST /api/auth/login` e `POST /api/auth/register`: inicia uma sessão.
- `GET /api/auth/user`: retorna o usuário atual (401 quando não autenticado).
- `POST /api/auth/logout`: encerra uma sessão autenticada.
- `GET /api/dashboard`: dados do painel, exige autenticação.
- `GET /api/patients?name=&exam=&date=&page=`: busca paginada de pacientes, exige autenticação.
- `GET /api/patients/{id}`: dados cadastrais, médico responsável e histórico de consultas/exames com resumo do resultado, exige autenticação.
- `GET /api/patients/{patient}/exams/{exam}/documents/{document}/content`: visualiza em fluxo privado um documento vinculado ao exame e paciente, exige autenticação.

A API aplica os filtros no banco MySQL. O filtro `date` corresponde à data da consulta e pode ser combinado com `name` e `exam`.

## Testes e build

```bash
./vendor/bin/sail artisan test
./vendor/bin/sail npm run build
```

Para produção, use HTTPS, configure cookies seguros, restrinja o cadastro conforme as regras da instituição e implemente autorização por papéis antes de adicionar dados clínicos. O dashboard atual é apenas um protótipo, não um sistema clínico.