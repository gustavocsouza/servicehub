# ServiceHub

Aplicação web para gestão de ordens de serviço, construída como desafio técnico.

---

## Stack

- **PHP 8.3** + **Laravel 12**
- **Inertia.js 2** + **Vue 3** (Composition API)
- **MySQL** via **Laravel Sail** (Docker)
- **Tailwind CSS**
- **Pest** para testes
- Fila assíncrona com driver `database`

---

## Domínio

```
Company (1) ──── (N) Project (1) ──── (N) Ticket (1) ──── (1) TicketDetail
                                           │
User (1) ──────────────────────────── (N) ┘
User (1) ──── (1) UserProfile
```

- **Company → Project**: uma empresa possui vários projetos.
- **Project → Ticket**: um projeto possui vários tickets.
- **Ticket → TicketDetail**: cada ticket possui exatamente um detalhe técnico (1:1, garantido por constraint `unique` na FK).
- **User → Ticket**: um usuário é responsável por vários tickets.
- **User → UserProfile**: cada usuário possui exatamente um perfil com dados adicionais (1:1).

---

## Funcionalidades

- Autenticação completa (registro, login, logout) via Laravel Fortify.
- Listagem, criação e exclusão de tickets.
- Upload opcional de anexo (JSON ou TXT) ao criar um ticket.
- Processamento assíncrono via fila: um Job lê o anexo, enriquece o `TicketDetail` com prioridade, categoria e metadados, e notifica o responsável.
- Notificações gravadas no banco (canal `database`), com página dedicada para visualização e marcação como lida.
- Testes automatizados com Pest cobrindo relacionamentos, rotas e o Job.

---

## Pré-requisitos

- Docker e Docker Compose instalados.
- [Laravel Sail](https://laravel.com/docs/sail) (incluído nas dependências do projeto).

---

## Como rodar

**1. Clone o repositório**

```bash
git clone <url-do-repositorio>
cd servicehub
```

**2. Instale as dependências PHP**

```bash
docker run --rm -v $(pwd):/app composer install --no-interaction
```

**3. Configure o ambiente**

```bash
cp .env.example .env
```

Confirme que as variáveis de banco e fila estão assim no `.env`:

```ini
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=servicehub
DB_USERNAME=sail
DB_PASSWORD=password

QUEUE_CONNECTION=database
```

**4. Suba os containers**

```bash
./vendor/bin/sail up -d
```

**5. Gere a chave da aplicação**

```bash
./vendor/bin/sail artisan key:generate
```

**6. Rode as migrations e popule o banco**

```bash
./vendor/bin/sail artisan migrate --seed
```

**7. Instale as dependências front-end e compile**

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

**8. Suba o worker da fila** (terminal separado)

```bash
./vendor/bin/sail artisan queue:work
```

A aplicação estará disponível em **http://localhost**.

---

## Usuário de teste

Criado pelo seeder:

| Campo | Valor |
|---|---|
| E-mail | dev@teste.com |
| Senha | password |

---

## Como testar o fluxo do anexo

1. Crie um arquivo `teste.json` com o conteúdo abaixo:
   ```json
   { "priority": "high", "category": "infraestrutura", "responsavel": "Equipe A" }
   ```
2. Faça login e acesse **Tickets → Novo Ticket**.
3. Preencha o formulário e anexe o `teste.json`.
4. Após enviar, o ticket aparece com status `processing`.
5. O worker da fila processa o job em background.
6. Recarregue a listagem: o status muda para `done` e a prioridade para `high`.
7. Acesse **Notificações** para ver a notificação gerada.

---

## Testes

```bash
./vendor/bin/sail artisan test
```

Cobertura:

- Relacionamentos 1:1 (`Ticket → TicketDetail`, `User → UserProfile`) e 1:N (`Company → Project → Ticket`).
- Rotas protegidas por autenticação.
- Criação de ticket via `POST /tickets`.
- Exclusão de ticket via `DELETE /tickets/{id}`.
- Disparo do Job quando há anexo (`Queue::fake`).
- Processamento do Job: enriquecimento do `TicketDetail` e notificação do responsável (`Notification::fake`).

---

## Decisões de arquitetura

**Fila com driver `database`**
Escolhido por não exigir infraestrutura adicional (Redis, etc.). A tabela `jobs` já vem na migration padrão do Laravel 12. Para produção, trocar para Redis é uma mudança de uma linha no `.env`.

**Notificação com canal `database`**
Evita dependência de configuração SMTP no ambiente de avaliação. As notificações ficam na tabela `notifications` e são acessíveis via `$user->notifications`. Adicionar o canal `mail` exige apenas incluir `'mail'` no array `via()` da notification.

**Constraint `unique` na FK para os relacionamentos 1:1**
O relacionamento 1:1 entre `Ticket` e `TicketDetail` (e entre `User` e `UserProfile`) é garantido no nível do banco via `->unique()` na chave estrangeira, não apenas no Eloquent. Isso impede duplicatas mesmo com acesso direto ao banco.

**`cascadeOnDelete` nas FKs**
Garante integridade referencial: ao excluir um ticket, o `TicketDetail` associado é removido automaticamente pelo banco, sem lógica adicional no código.

**Starter kit Vue oficial (Laravel 12)**
Usado no lugar do Breeze (removido do instalador no Laravel 12). Entrega autenticação completa via Fortify, Inertia 2, Vue 3 e Tailwind sem configuração manual.
