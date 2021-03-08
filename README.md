## Descrição

Microsserviço de catálogo de vídeos - backend

## Rodar a aplicação

#### Crie os containers com Docker

```bash
$ docker-compose up
```

#### Accesse no browser

```
http://localhost:8000
```

## Apéndice

Nosso aluno [Yuri Koster](https://github.com/yurikoster1) criou outra opção do repositório organizando melhor os arquivos Docker, se quiserem utilizar basta clonar o branch `more_organized`.

## Comandos:

```php
php artisan make:model --help
```

Essse comando traz as várias opções de criação.<br />
Exemplo:

```
            make:model [options] [--] <name>
php artisan make:model --all Models/Category
php artisan make:model --all Models/Genre
```

<br />
Criar em api.php as Route::group
<br />

```php
php artisan make:seeder --help
php artisan make:seeder CategoriesTableSeeder
php artisan make:seeder GenresTableSeeder
```

```php
php artisan migrate --help
php artisan migrate --seed
```

```php
php artisan migrate:refresh --seed
php artisan migrate:fresh --seed
```

```php
php artisan tinker
\App\Models\Category::all();
\App\Models\Category::find(100);
\App\Models\Genre::all();
\App\Models\Genre::find(100);
```

```php
php artisan route:list
```

```php
php artisan make:request CategoryRequest
php artisan make:request GenreRequest
```

```php
php artisan route:list
```

```php
php artisan tinker
use \App\Models\Category;
Category::find(1)->delete();
Category::find(1);
Category::withTrashed()->find(1);
Category::withTrashed()->get();
Category::onlyTrashed()->get();
Category::onlyTrashed()->find(1)->restore();
Category::find(2)->forceDelete();

use Ramsey\Uuid\Uuid;
Uuid::uuid4();
=> Ramsey\Uuid\Uuid {#4391
     uuid: "dd40d795-b5e4-4315-804a-b2c89ec82a25",
   }
echo Uuid::uuid4();
afc48249-e608-4099-bc7d-f94dea370192⏎


```
