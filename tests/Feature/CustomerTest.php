<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_only_logged_in_users_can_see_customers_list()
    {
        //Caso exista a tentativa de acesso a rota /curstomers
        //sem que o usuário esteja logado, ele será direcionado
        //para rota /login
        $response = $this->get('/customers')
            ->assertRedirect('/login');
    }
}
