# Todo API — Sistema de Gerenciamento de Tarefas

API REST completa construída com **Laravel 13** e **Laravel Sanctum** para gerenciar categorias e tarefas por usuário autenticado.

## Integrantes

| Integrante | Atividades desenvolvidas |
|------------|--------------------------|
| Fernanda Pacheco | Configuração inicial do projeto, autenticação com Laravel Sanctum (registro, login, logout), definição das rotas de API e implementação dos endpoints de categorias e tarefas |
| Lucas Gabriel | Organização da estrutura MVC no Laravel: definição dos models com relacionamentos, criação das migrations e separação de responsabilidades entre controllers, Form Requests e models |
| Maria Beatriz | Implementação da funcionalidade de exportação do histórico de tarefas em PDF, documentação das rotas no README e criação da coleção de testes no Insomnia |

---

## Requisitos

- PHP >= 8.2
- Composer
- Extensões PHP: `ext-xml`, `ext-dom`, `ext-pdo`, `ext-sqlite3` (ou MySQL/PostgreSQL)
- SQLite (padrão) ou MySQL/PostgreSQL

### Instalar extensões faltantes (Ubuntu/Debian)

```bash
sudo apt-get install php8.3-xml php8.3-sqlite3
```

---

## Instalação Passo a Passo

```bash
# 1. Clonar o repositório
git clone https://github.com/cafeef/todo-api.git todo-api
cd todo-api

# 2. Instalar dependências
composer install

# 3. Copiar o arquivo de ambiente
cp .env.example .env

# 4. Gerar a chave da aplicação
php artisan key:generate

# 5. Configurar banco de dados no .env
#    Opção A — SQLite (mais simples):
#    DB_CONNECTION=sqlite
#    (O arquivo database/database.sqlite é criado automaticamente)

#    Opção B — MySQL:
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=todo_api
#    DB_USERNAME=root
#    DB_PASSWORD=sua_senha

# 6. Criar banco SQLite (se usar SQLite)
touch database/database.sqlite

# 7. Executar as migrations
php artisan migrate

# 8. (Opcional) Popular o banco com dados de teste
php artisan db:seed

# 9. Iniciar o servidor de desenvolvimento
php artisan serve
```

A API estará disponível em `http://localhost:8000`.

---

## Como usar o Token Sanctum

Após `POST /api/login` ou `POST /api/register`, você receberá um `token` na resposta. Inclua-o em todas as requisições protegidas no header:

```
Authorization: Bearer SEU_TOKEN_AQUI
```

Exemplo com curl:
```bash
curl -H "Authorization: Bearer 1|abc123..." http://localhost:8000/api/categorias
```

No Insomnia/Postman: selecione **Bearer Token** e cole o valor do token.

---

## Rotas da API

### Autenticação (Públicas)

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/api/register` | Cadastrar novo usuário |
| POST | `/api/login` | Fazer login |
| POST | `/api/logout` | Fazer logout (requer token) |

#### POST /api/register
```json
{
  "name": "João Silva",
  "email": "joao@email.com",
  "password": "senha123456",
  "password_confirmation": "senha123456"
}
```
Resposta `201`:
```json
{
  "status": "success",
  "message": "Usuário cadastrado com sucesso.",
  "data": {
    "user": { "id": 1, "name": "João Silva", "email": "joao@email.com" },
    "token": "1|abc123xyz..."
  }
}
```

#### POST /api/login
```json
{
  "email": "joao@email.com",
  "password": "senha123456"
}
```
Resposta `200`:
```json
{
  "status": "success",
  "message": "Login realizado com sucesso.",
  "data": {
    "user": { "id": 1, "name": "João Silva", "email": "joao@email.com" },
    "token": "2|xyz789..."
  }
}
```

#### POST /api/logout (requer Bearer Token)
Resposta `200`:
```json
{ "status": "success", "message": "Logout realizado com sucesso." }
```

---

### Categorias (Requerem Bearer Token)

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/categorias` | Listar categorias do usuário |
| POST | `/api/categorias` | Criar categoria |
| GET | `/api/categorias/{id}` | Exibir categoria com tarefas |
| PUT | `/api/categorias/{id}` | Atualizar categoria |
| DELETE | `/api/categorias/{id}` | Excluir categoria |

#### POST /api/categorias
```json
{
  "nome": "Trabalho",
  "descricao": "Tarefas do trabalho"
}
```
Resposta `201`:
```json
{
  "status": "success",
  "message": "Categoria criada com sucesso.",
  "data": { "id": 1, "nome": "Trabalho", "descricao": "Tarefas do trabalho", "user_id": 1 }
}
```

#### PUT /api/categorias/{id}
```json
{
  "nome": "Trabalho Remoto",
  "descricao": "Atualizado"
}
```

#### DELETE /api/categorias/{id}
Resposta `200`:
```json
{ "status": "success", "message": "Categoria excluída com sucesso." }
```

Resposta `422` (quando há tarefas vinculadas):
```json
{
  "status": "error",
  "message": "Não é possível excluir a categoria pois existem tarefas vinculadas a ela. Remova as tarefas primeiro."
}
```

---

### Tarefas (Requerem Bearer Token)

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/tarefas` | Listar tarefas (aceita filtros) |
| POST | `/api/tarefas` | Criar tarefa |
| GET | `/api/tarefas/{id}` | Exibir tarefa |
| PUT | `/api/tarefas/{id}` | Atualizar tarefa |
| DELETE | `/api/tarefas/{id}` | Excluir tarefa |

#### GET /api/tarefas — Filtros disponíveis via query string

```
GET /api/tarefas?status=pendente
GET /api/tarefas?categoria_id=1
GET /api/tarefas?status=em_andamento&categoria_id=2
```

**Valores válidos para `status`:** `pendente`, `em_andamento`, `concluida`

#### POST /api/tarefas
```json
{
  "titulo": "Revisar relatório",
  "descricao": "Revisar o relatório trimestral",
  "status": "pendente",
  "prazo": "2026-12-31",
  "categoria_id": 1
}
```
Resposta `201`:
```json
{
  "status": "success",
  "message": "Tarefa criada com sucesso.",
  "data": {
    "id": 1,
    "titulo": "Revisar relatório",
    "status": "pendente",
    "prazo": "2026-12-31",
    "categoria_id": 1,
    "user_id": 1,
    "categoria": { "id": 1, "nome": "Trabalho" }
  }
}
```

#### PUT /api/tarefas/{id}
```json
{
  "status": "em_andamento",
  "prazo": "2026-01-15"
}
```
---
### Relatórios (Visualização Web)

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/exportar-pdf?token=SEU_TOKEN` | Gera e exibe o histórico de tarefas do usuário logado em formato PDF |

#### GET /exportar-pdf
Esta rota valida o token enviado como parâmetro na URL para autenticar o usuário e retornar o stream do arquivo diretamente no navegador.
---

### Erros comuns

| Código | Situação |
|--------|----------|
| 401 | Token ausente ou inválido |
| 404 | Recurso não encontrado (ou pertence a outro usuário) |
| 422 | Erro de validação ou tentativa de excluir categoria com tarefas |

Exemplo de resposta de validação `422`:
```json
{
  "status": "error",
  "message": "Erro de validação.",
  "errors": {
    "titulo": ["O campo título é obrigatório."],
    "categoria_id": ["A categoria informada não existe ou não pertence a você."]
  }
}
```

---

## Importar Coleção de Testes

O arquivo de coleção está em `docs/insomnia_collection.json`.

**No Insomnia:**
1. Abra o Insomnia
2. Vá em **File → Import**
3. Selecione o arquivo `docs/insomnia_collection.json`
4. A coleção "Todo API" aparecerá com todas as requisições organizadas

**No Postman:**
1. Clique em **Import**
2. Arraste o arquivo `docs/insomnia_collection.json` ou selecione-o
3. O Postman importará as requisições automaticamente

---

## Dados do Seeder

Após rodar `php artisan db:seed`, o banco terá:
- **Usuário:** `teste@exemplo.com` / senha: `senha123456`
- **3 categorias:** Trabalho, Pessoal, Estudos
- **6 tarefas** distribuídas entre as categorias

---

## Limitações e Funcionalidades Futuras

### Limitações atuais
- Paginação não implementada (todas as listagens retornam todos os registros)
- Sem ordenação configurável via query string
- Sem busca por texto no título/descrição das tarefas
- Sem suporte a múltiplos tokens nomeados (ex: "mobile", "web")

### Funcionalidades futuras
- **Paginação** com `?page=` e `?per_page=`
- **Ordenação** com `?sort=prazo&direction=asc`
- **Busca textual** com `?q=relatorio`
- **Notificações por e-mail** quando uma tarefa se aproximar do prazo
- **Subtarefas** (relacionamento recursivo em Tarefa)
- **Compartilhamento de listas** entre usuários
- **Exportação** de tarefas para CSV
- **Refresh token** para renovação automática da sessão
