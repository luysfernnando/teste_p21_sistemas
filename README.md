# Ambiente de Desenvolvimento PHP

Este é um ambiente de desenvolvimento PHP utilizando Docker, com os seguintes serviços:
- PHP 8.3 FPM
- Nginx
- MySQL 8

## Requisitos
- Docker
- Docker Compose

## Como usar

1. Clone este repositório
2. No terminal, execute:
```bash
docker-compose up -d
```

3. Os serviços estarão disponíveis em:
- Aplicação: http://localhost
- MySQL: localhost:3306
  - Database: app_db
  - Usuário: app_user
  - Senha: app_pass

## Estrutura
- Os arquivos do projeto devem ser colocados na raiz do diretório
- A configuração do Nginx está em `docker/nginx/default.conf`
- As configurações do Docker estão em `docker-compose.yml` e `Dockerfile`
