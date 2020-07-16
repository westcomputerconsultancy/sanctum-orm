<?php

/*
 * This file is part of the Sanctum ORM project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Kilip\SanctumORM\Features;

use Tests\Kilip\SanctumORM\TestCase;

class SecurityTest extends TestCase
{
    public function testLogin()
    {
        $user     = $this->createUser();
        $response = $this->post('/api/login', [
            'email'    => 'test@example.com',
            'password' => 'test',
            'device'   => 'phpunit',
        ]);

        $json = $response->json();
        $response->assertOk();
        $this->assertNotNull($token = $json['token']);

        $this->withToken($token);
        $response = $this->withToken($token)->get('/api/user');
        $json     = $response->json();

        $response->assertOk();
    }
}
