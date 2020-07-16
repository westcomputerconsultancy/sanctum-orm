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

use Kilip\SanctumORM\Manager\TokenManagerInterface;
use Tests\Kilip\SanctumORM\TestCase;

class SecurityTest extends TestCase
{
    public function testLogin()
    {
        $user     = $this->createUser();
        $response = $this->post('/api/token', [
            'email'    => 'test@example.com',
            'password' => 'test',
            'device'   => 'phpunit',
        ]);

        $json = $response->json();
        $response->assertOk();
        $this->assertNotNull($token = $json['plainTextToken']);

        $response = $this->get('/api/user', [
            'Authorization' => 'Bearer '.$token,
        ]);
        $json = $response->json();
        $response->assertOk();
        $this->assertNotCount(0, $json);
        $this->assertSame($user->toArray(), $json);
    }

    public function testLoginWithExpiredToken()
    {
        /** @var \Kilip\SanctumORM\Manager\TokenManagerInterface $tokenManager */
        $user         = $this->createUser();
        $tokenManager = app()->get(TokenManagerInterface::class);

        $token            = $tokenManager->createToken($user, 'phpunit');
        $currentUserToken = $token->accessToken;
        $currentUserToken->setCreatedAt(new \DateTime('-365 days'));
        $tokenManager->storeToken($currentUserToken);

        $response = $this->get('/api/user', [
            'Authorization' => 'Bearer '.$token->plainTextToken,
        ]);
        $response->assertStatus(302);
    }
}
