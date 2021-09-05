<p align="center">
  <a href="http://nestjs.com/" target="blank"><img src="http://maratona.fullcycle.com.br/public/img/logo-maratona.png"/></a>
</p>

## Descrição

Microsserviço de catálogo

## Rodar a aplicação

#### Crie os containers com Docker

```bash
$ docker-compose up -d
```

#### Instale as dependências

Primeiramente é necessário se conectar ao container:

```bash
docker-compose exec app bash
```

Agora podemos instalar as dependências:

```bash
composer install
```

> Deste momento em diante já podemos ter acesso ao `php artisan`

#### Accesse no browser

```
http://localhost:8000
```

## Apéndice

Nosso aluno [Yuri Koster](https://github.com/yurikoster1) criou outra opção do repositório organizando melhor os arquivos Docker, se quiserem utilizar basta clonar o branch ```more_organized```.
