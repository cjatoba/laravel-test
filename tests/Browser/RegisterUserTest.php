<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterUserTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_check_if_root_site_is_correct()
    {
        $this->browse(function (Browser $browser) {
            //Navega até a rota /
            $browser->visit('/')
                    //Verifica se existe o texto Laravel
                    ->assertSee('Laravel');
        });
    }

    public function test_check_if_login_function_is_working(){
        $this->browse(function (Browser $browser) {
            //Navega até a rota /login
            $browser->visit('/login')
                //Digita no campo e-mail o valor teste@mail.com.br
                ->type('email', 'testes@mail.com')
                //Digita no campo password o valor password
                ->type('password', 'password')
                //Clica no botão Login
                ->press('LOG IN')
                //Verifica se caiu na rota /home
                ->assertPathIs('/dashboard');
        });
    }

    public function test_check_if_register_function_is_working(){
        $this->browse(function (Browser $browser) {
            //Navega até a rota /login
            $browser->visit('/register')
                //Digita no campo name o valor User Test
                ->type('name', 'User Test3')
                //Digita no campo e-mail o valor teste@mail.com.br
                ->type('email', 'teste3@mail.com.br')
                //Digita no campo password o valor password
                ->type('password', 'password')
                //Digita no campo password_confirmation o valor password
                ->type('password_confirmation', 'password')
                //Clica no botão Register
                ->press('REGISTER')
                //Verifica se caiu na rota /home
                ->assertPathIs('/dashboard')
                //Verifica se encontra o texto Dashboard
                ->assertSee('Dashboard');
        });
    }
}
