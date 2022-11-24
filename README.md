[![Deployment Production](https://github.com/ivalrivall/fortamil/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/ivalrivall/fortamil/actions/workflows/main.yml)

## Installment

```bash
- cp .env.example .env
- composer install
- php artisan key:generate
- php artisan migrate
- php artisan db:seed
- (one time only) php artisan db:seed --class=WilayahSeeder
- php artisan optimize:clear
```
