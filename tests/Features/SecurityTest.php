<?php


namespace Tests\Kilip\DoctrineSanctum\Features;


use Tests\Kilip\DoctrineSanctum\TestCase;

class SecurityTest extends TestCase
{
    public function testLogin()
    {
        $user = $this->createUser();
        $response = $this->post('/api/login',[
            'email' => 'test@example.com',
            'password' => 'test',
            'device' => 'phpunit'
        ]);

        $json = $response->json();
        $response->assertOk();
        $this->assertNotNull($token = $json['token']);

        $this->withToken($token);
        $response = $this->withToken($token)->get('/api/user');
        $json = $response->json();

        $response->assertOk();
    }
}