# Testes com Laravel 8 (Unit, Feature e Browser)

Este projeto foi criado com base **[nesta vídeo aula](https://www.youtube.com/watch?v=f3tD-K796xo&list=PL7ScB28KYHhH35NubnZfP-9vegvOfUOoH&index=6)** do canal **[Beer and Code](https://www.youtube.com/channel/UCtz8hxpbicBe8REEdEtAYWA)** no Youtube, ele mostra como realizar testes (Feature, Unit e  Browser) utilizando o Laravel.

Para facilitar consultas futuras, serão listados a seguir os passos seguidos no decorrer de todo conteúdo do vídeo (Com algumas pequenas adaptações para versão 8 do Laravel).

- [Documentação do Laravel 8](https://laravel.com/docs/8.x)

<h2>Índice</h2>

- <a href='#instalacao'>Instalação</a>
- <a href='#configuracoes'>Configurações</a>
- <a href='#execucaounit'>Execução do PHP Unit</a>
- <a href='#testes'>Testes</a>
    - <a href='#featuretest'>Feature Test</a>
    - <a href='#unittest'>Unit Test</a>
    - <a href='#browsertest'>Browser Test</a>
- <a href='#boaspraticas'>Boas Práticas</a>

<h2 id='instalacao'>Instalação do projeto</h2>

Apesar de existirem outras formas de instalação, para este projeto será utilizado o Composer.
- Criar um novo projeto utilizando o composer, digitando o comando abaixo no terminal: 
```
composer create-project --prefer-dist laravel/laravel laravel-test
```

<h2 id='configuracoes'>Configurações</h2>

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

- Indicar o endereço correto em que o projeto será executado em APP_URL, ajustar caso for executado em um endereço ou porta diferente, como no exemplo abaixo onde será utilizada a porta 8000 ao utilizar o `artisan serve`:
```
APP_URL=http://localhost:8000
```

### Instalação do kit Laravel Breeze

O kit Laravel Breeze cria todas as rotas, views e migrates necessários para autenticação de usuários;

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

<h2 id='execucaounit'>Execução do phpunit</h2>

- O phpunit pode ser executado de duas formas, a partir do diretório `vendor/bin/phpunit`:
```php
vendor\bin\phpunit
```
 Ou utilizando o artisan:
```php
php artisan test
```

<h2 id='testes'>Testes</h2>

<h3 id='featuretest'>Feature test</h3>
##### (Para testar uma parte maior do código, incluindo vários objetos que interagem entre si ou até mesmo uma solicitação HTTP completa para um endpoint JSON)

- Apagar todos os arquivos das pastas `tests/Feature` e `tests/Unit`;

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

- Criar as tabelas com base nas [migrations](https://laravel.com/docs/8.x/migrations#introduction):
```
php artisan migrate
```

- Neste momento ao realizar um teste com o comando `php artisan test` verificamos que o teste não passará, indicando que não há um usuário autenticado:
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

- Criar um Controller para execução dos nossos testes personalizados, neste caso com o nome Customer mas pode ser outro nome:
```
php artisan make:controller CustomerController --resource
```

- Criar rotas no arquivo `web.php` na pasta `routes` apontando para o Controller `CustumerController`, o método resource já cria todas as rotas necessárias para o CRUD:
```php
Route::resource('/customers', CustomerController::class)->middleware(['auth']);
```
- Ao executar os testes novamente ele irá passar, pois desta vez as rotas foram criadas, e existe um middleware para proteger a lista de clientes:
```
Warning: TTY mode is not supported on Windows platform.

   PASS  Tests\Feature\CustomerTest
  ✓ only logged in users can see customers list

  Tests:  1 passed
  Time:   0.28s
```

#### Filtrar/Executar métodos específico

- Dentro de uma classe de testes podem existir vários métodos de testes, para executar um específico é necessário utilizar o parâmetro `--filter` seguido do nome do método:
```
php artisan test --filter test_only_logged_in_users_can_see_customers_list
```

<h3 id='unittest'>Unit Test</h3> 
##### (Voltado para partes pequenas e isoladas do código, como campos de um model)

- Criar uma nova classe de teste com o parâmetros `--unit`:
```
php artisan make:test UserTest --unit
```

- Criar um método para checar se as colunas do model Users contém os campos name, email e password:
```php
public function test_check_if_user_colums_is_correct()
    {
        $user = new User;

        $expected = [
            'name',
            'email',
            'password'
        ];

        $arrayCompared = array_diff($expected, $user->getFillable());

        $this->assertEquals(0, count($arrayCompared));
    }
```

<h3 id='browsertest'>Browser Test</h3>
##### (Testar envio de formulários, botões e outras ações)

- Instalar o pacote Dusk:
```
composer require --dev laravel/dusk
```

- Instalar o Dusk:
```
php artisan dusk:install
```

- Criar um novo teste de browser para o cadastro de usuários, a classe será criada na pasta `tests/Browser`:
```
php artisan dusk:make RegisterUserTest
```

- Apagar a classe de teste de exemplo com o nome ExampleTest.php na pasta `tests/Browser`

- Alterar o nome do método de teste:
```php
public function test_check_if_root_site_is_correct()
    {
        //Acessa a rota / do browser e verifica se exista o texto Laravel
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Laravel');
        });
    }
```

- Rodar os testes com o comando abaixo:
```
php artisan dusk
```

- Para realizar testes utilizando a base de dados real, comentar as linhas abaixo no arquivo `phpunit.xml`:
```xml
<!-- <server name="DB_CONNECTION" value="sqlite"/>
        <server name="DB_DATABASE" value=":memory:"/> -->
```

- Limpar o cache de configuração:
```
php artisan config:cache
```

- Criar um novo método de teste para testar  se a funcionalidade de login do usuário está funcionando:
```php
public function test_check_if_login_function_is_working(){
        $this->browse(function (Browser $browser) {
            //Navega até a rota /login
            $browser->visit('/login')
                //Digita no campo e-mail o valor teste@mail.com.br
                ->type('email', 'teste@mail.com.br')
                //Digita no campo password o valor password
                ->type('password', 'password')
                //Clica no botão Login
                ->press('Login')
                //Verifica se caiu na rota /home
                ->assertPathIs('/home');
        });
    }
```

- Criar um método para testar se a funcionalidade de registro está funcionando:
```php
public function test_check_if_register_function_is_working(){
        $this->browse(function (Browser $browser) {
            //Navega até a rota /login
            $browser->visit('/register')
                //Digita no campo name o valor User Test
                ->type('name', 'User Test2')
                //Digita no campo e-mail o valor teste@mail.com.br
                ->type('email', 'teste2@mail.com.br')
                //Digita no campo password o valor password
                ->type('password', 'password')
                //Digita no campo password_confirmation o valor password
                ->type('password_confirmation', 'password')
                //Clica no botão Register
                ->press('Register')
                //Verifica se caiu na rota /home
                ->assertPathIs('/home')
                //Verifica se encontra o texto Dashboard
                ->assertSee('Dashboard');
        });
    }
```

- Executar todos os testes já criados:
```
php artisan dusk
```

- Executar um método de teste em específico:
```
php artisan dusk --filter test_check_if_register_function_is_working
```

<h2 id='boaspraticas'>Boas práticas</h2>

- O padrão de nome dos métodos de teste é UnitOfWork_StateUnderTest_ExpectedBehavior traduzindo UnidadeDeTrabalho_EstadoEmTeste_ComportamentoEsperado
