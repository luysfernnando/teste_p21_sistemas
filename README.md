# Sistema de Gestão P21 Sistemas

Sistema de gestão desenvolvido em PHP para gerenciamento de clientes, produtos, pedidos e comunicações.

## 📋 Índice

- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Módulos](#módulos)
- [Funcionalidades Especiais](#funcionalidades-especiais)
- [Endpoints da API](#endpoints-da-api)
- [Comandos Úteis](#comandos-úteis)

## 🔧 Requisitos

- Docker
- Docker Compose
- Git

## 🚀 Instalação

1. Clone o repositório:
```bash
git clone [URL_DO_REPOSITORIO]
cd teste_p21_sistemas
```

2. Inicie os containers:
```bash
docker-compose up -d
```

3. Execute as migrações do banco de dados:
```bash
# Verificar status das migrações
docker-compose exec php php bin/migrate status

# Executar migrações pendentes
docker-compose exec php php bin/migrate migrate

# Caso necessário, reverter última migração
docker-compose exec php php bin/migrate rollback
```

4. Acesse o sistema em: http://localhost

## 📦 Módulos

### 👥 Clientes
- Cadastro e gestão de clientes
- Importação em massa via arquivo Excel
- Campos personalizados para histórico de pedidos
- Integração com módulo de comunicação para envio de e-mails

### 📦 Produtos
- Cadastro e gestão de produtos
- Controle de estoque
- Preços e descrições
- Integração com módulo de pedidos

### 🛍️ Pedidos
- Criação e gestão de pedidos
- Importação via XML
- Histórico de status
- Notificações automáticas por e-mail
- Integração com clientes e produtos

### 📨 Comunicados
- Templates de e-mail personalizáveis
- Variáveis dinâmicas nos templates
- Histórico de e-mails enviados
- Configuração de template padrão para atualizações de status
- Envio em massa para clientes selecionados

### 🔄 Integrações
- Endpoint REST para recebimento de pedidos via XML
- Validação de XML contra schema XSD
- Histórico de integrações
- Reprocessamento de integrações falhas

## 🌟 Funcionalidades Especiais

### Importação de Clientes (Excel)
- Suporte a arquivos .xls e .xlsx
- Mapeamento automático de colunas
- Tratamento de dados duplicados
- Validação de e-mails
- Log de importação com sucessos e erros

### Importação de Pedidos (XML)
- Importação manual via interface
- Recebimento automático via API
- Validação contra schema XSD
- Processamento assíncrono
- Log detalhado de erros

### Sistema de E-mails
- Templates personalizáveis
- Variáveis dinâmicas:
  - {nome_cliente}
  - {numero_pedido}
  - {status_pedido}
- Preview de templates
- Histórico de envios
- Integração com MailHog para testes

## 🔌 Endpoints da API

### Recebimento de Pedidos
```
POST /integracao/receber
Content-Type: application/xml
X-Partner-Name: [NOME_DO_PARCEIRO]

<?xml version="1.0" encoding="UTF-8"?>
<pedidos>
    <pedido>
        <id_loja>001</id_loja>
        <nome_loja>Torre de Cristal</nome_loja>
        <localizacao>Planeta Zirak</localizacao>
        <produto>Cristais Místicos</produto>
        <quantidade>50</quantidade>
    </pedido>
</pedidos>
```

## 🛠️ Comandos Úteis

### Migrações
```bash
# Ver status das migrações
docker-compose exec php php bin/migrate status

# Executar migrações pendentes
docker-compose exec php php bin/migrate migrate

# Reverter última migração
docker-compose exec php php bin/migrate rollback
```

### Docker
```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f

# Acessar container PHP
docker-compose exec php bash
```

### Serviços

- **Aplicação**: http://localhost
- **MailHog** (servidor de e-mails para teste): http://localhost:8025
- **MySQL**: localhost:3306
  - Database: p21_sistemas
  - User: p21_user
  - Password: p21_pass

## 📝 Notas Adicionais

- O sistema utiliza o MailHog para interceptar e-mails em ambiente de desenvolvimento
- Todos os e-mails enviados podem ser visualizados na interface do MailHog
- As senhas e configurações sensíveis devem ser alteradas em ambiente de produção
- Recomenda-se fazer backup do banco de dados antes de executar migrações
