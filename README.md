# Testes com Laravel 8

Este projeto foi criado com base **[nesta vídeo aula](https://www.youtube.com/watch?v=f3tD-K796xo&list=PL7ScB28KYHhH35NubnZfP-9vegvOfUOoH&index=6)** do canal **[Beer and Code](https://www.youtube.com/channel/UCtz8hxpbicBe8REEdEtAYWA)** no Youtube, ele mostra como realizar testes (Feature, Unit e  Browser) utilizando o Laravel.

Para facilitar consultas futuras, serão listados a seguir os passos seguidos no decorrer de todo conteúdo do vídeo (Com algumas pequenas adaptações adaptando a versão 8 do Laravel).

O projeto foi desenvolvido utilizando o [Framework Laravel](https://laravel.com) na versão 8

## Instalação do projeto

Apesar de existirem outras formas de instalação, para este projeto será utilizado o Composer.
- Criar um novo projeto utilizando o composer, digitando o comando abaixo no terminal: 
```
composer create-project --prefer-dist laravel/laravel laravel-test
```

## Configurações

### Arquivo `phpunit.xml`:

- Abrir o arquivo `phpunit.xml`, localizado na raiz do projeto, nele contém as configurações dos testes;
- Verificar se a configuração do TELESCOPE_ENABLED está com o valor false:
```xml
<server name="TELESCOPE_ENABLED" value="false"/>
```
- Descomentar a configuração DB_CONNECTION:
```xml
<server name="DB_CONNECTION" value="sqlite"/>
```
- Descomentar a configuração DB_DATABASE (Cria um banco de dados apenas na memória RAM para não utilizar o banco físico):
```xml
<server name="DB_DATABASE" value=":memory:"/>
```

### Arquivo `.env`:

- Indicar o endereço correto em que o projeto será executado em APP_URL, ajustar caso for executado em um endereço ou porta diferente, como no exemplo abaixo onde será utilizada a porta 8000 ao utilizar o <code>artisan serve</code>:
```
APP_URL=http://localhost:8000
```

### Instalação do kit Laravel Breeze

O kit Laravel Breeze cria todas as rotas, views e migrates necessários para autendicação de usuários;

- Instalar o pacote Laravel Breeze:
```
composer require laravel/breeze --dev
```
- Publicar as visualizações de autenticação, rotas, controladores e outros recursos para seu aplicativo:
```
php artisan breeze:install
```
- Compilar seus ativos para que o arquivo CSS do seu aplicativo esteja disponível:
```
npm install
npm run dev
```

## Execução do phpunit

- O phpunit pode ser executado de duas formas, a partir do diretório `vendor/bin/phpunit`:
```php
vendor\bin\phpunit
```
 Ou utilizando o artisan:
```php
php artisan test
```

## Excluir os arquivos de exemplo de testes

- Apagar todos os arquivos das pastas `tests/Feature` e `tests/Unit`;

## Criação dos testes

### Teste Feature

- Criar o teste via terminal, após a execução do comando será criado um arquivo na pasta `tests/Feature`:
```
php artisan make:test CustomerTest
```

- Executar um teste utilizando o comando:
```
php artisan test
```

- Se tudo estiver ok até aqui, a execução do comando acima vai gerar a saída abaixo:
```
PHPUnit 9.5.5 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.331, Memory: 20.00 MB

OK (1 test, 1 assertion)
```

- Por padrão o Laravel traz um teste de exemplo chamado test_example, vamos substituir este teste criando um para verificar se apenas usuários logados pode ver uma lista de clientes:
```php
public function only_logged_in_users_can_see_customers_list()
    {
        //Caso exista a tentativa de acesso a rota /customers
        //sem que o usuário esteja logado, ele será redirecionado
        //para rota /login
        $response = $this->get('/customers')
            ->assertRedirect('/login');
    }
```

- Criar uma base se teste no Mysql, no nosso caso a base se chamará laravel-test;
- Alterar o arquivo `.env` informando o nome da base de dados que vamos utilizar:
```
DB_DATABASE=laravel-test
```

- Limpar o cache da configuração
```
php artisan config:cache
```

- Criar as tabelas com base nas migrations:
```
php artisan migrate
```

- Neste momento ao realizar um teste com o comando <code>php artisan test</code> verificamos que o teste não passará, indicando que não há um usuário autenticado:
```
Warning: TTY mode is not supported on Windows platform.

   FAIL  Tests\Feature\CustomerTest
  ⨯ only logged in users can see customers list

  ---

  • Tests\Feature\CustomerTest > only logged in users can see customers list
  Response status code [404] is not a redirect status code.
  Failed asserting that false is true.

  at C:\www\laravel-teste\tests\Feature\CustomerTest.php:22
     18▕         //Caso exista a tentativa de acesso a rota /curstomers
     19▕         //sem que o usuário esteja logado, ele será redirecionado
     20▕         //para rota /login
     21▕         $response = $this->get('/customers')
  ➜  22▕             ->assertRedirect('/login');
     23▕     }
     24▕ }
     25▕

  1   C:\www\laravel-teste\vendor\phpunit\phpunit\phpunit:61
      PHPUnit\TextUI\Command::main()


  Tests:  1 failed
  Time:   1.78s
```

- Criar um Controller Customer:
```
php artisan make:controller CustomerController --resource
```

- Criar rotas no arquivo `web.php` na pasta `routes` apontando para o Controller `CustumerController`, o método resource já cria todas as rotas necessárias para o CRUD:
```php
Route::resource('/customers', CustomerController::class)->middleware(['auth']);
```
- Ao executar os testes novamente ele irá passar, pois desta vez as rotas foram criadas, permitindo o redirecionamento:
```
Warning: TTY mode is not supported on Windows platform.

   PASS  Tests\Feature\CustomerTest
  ✓ only logged in users can see customers list

  Tests:  1 passed
  Time:   0.28s
```

#### Filtrar/Executar métodos específico

- Dentro de uma classe de testes podem existir vários métodos de testes, para executar um específico é necessário utilizar o parâmetro <code>--filter</code> seguido do nome do método:
```
php artisan test --filter test_only_logged_in_users_can_see_customers_list
```
