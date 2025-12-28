# Card Vault - Symfony
## Description
This project is a personal project as a clone of "card vault".  
The purpose is to learn PHP & Symfony.

## Requirements
- Docker

## Installation
Launch the project with Docker:
```bash
docker compose up --build
```

## How to use
Open the web app at this address :
```
http://localhost:8000/
```

## Routes
- `/` - Home (redirects to dashboard)
- `/dashboard` - Dashboard page
- `/stats` - Statistics page
- `/scan` - Scan page
- `/vault` - Vault page
- `/search` - Search page
- `/roadmap` - Roadmap page
- `/register` - Register page
- `/login` - Login page
- `/logout` - Logout route

## Notes
You can access the running project files in Docker with :
```bash
docker compose exec php bash
```
You can reset Redis cache with this command :
```bash
docker exec -it card-vault-php-php-1 php bin/console cache:clear
```
you can access to postgres GUI data here :
```bash
http://localhost:5050
```
Or access in TUI with :
```bash
docker-compose exec postgres psql -U postgres -d card_vault_php
SELECT id, email, password FROM "user";
```
To access mails go to :
```bash
http://localhost:8025/
```
All together for admin purpose :
```bash
firefox http://localhost:8000/dashboard
firefox http://localhost:5050/
firefox http://localhost:8025/
```
