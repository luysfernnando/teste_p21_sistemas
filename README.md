# Loja Mágica de Tecnologia

Sistema de gestão de clientes e pedidos para a Loja Mágica de Tecnologia.

## Funcionalidades

- Importação de clientes via planilha Excel
- Gestão de clientes e pedidos
- Sistema de comunicação por e-mail
- Integração com lojas parceiras via XML

## Requisitos

- Docker e Docker Compose
- PHP 8.2 ou superior
- MySQL 8
- Servidor web (Nginx)

## Configuração do Ambiente

1. Clone o repositório
2. Execute o comando:
```bash
docker-compose up -d
```

3. O sistema estará disponível em: http://localhost:80

## Estrutura do Banco de Dados

O arquivo SQL com a estrutura do banco de dados está disponível em `database/schema.sql`

## Estrutura do Projeto

```
.
├── src/                    # Código fonte PHP
│   ├── config/            # Configurações
│   ├── controllers/       # Controladores
│   ├── models/           # Modelos
│   ├── services/         # Serviços
│   └── utils/            # Utilitários
├── public/               # Arquivos públicos
│   ├── css/             # Estilos
│   ├── js/              # JavaScript
│   └── index.php        # Ponto de entrada
├── database/            # Scripts SQL
├── uploads/             # Arquivos importados
└── tests/               # Testes unitários
```

## Tecnologias Utilizadas

- PHP 8.2 (sem frameworks)
- HTML, CSS, JavaScript/jQuery
- MySQL 8
- Docker
