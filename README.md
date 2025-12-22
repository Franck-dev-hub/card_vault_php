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

## Notes
You can access the running project files in Docker with:
```bash
docker compose exec php bash
```
