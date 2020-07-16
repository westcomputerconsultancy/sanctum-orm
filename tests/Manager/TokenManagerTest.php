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

namespace Tests\Kilip\SanctumORM\Manager;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Kilip\SanctumORM\Contracts\SanctumUserInterface;
use Kilip\SanctumORM\Contracts\TokenModelInterface;
use Kilip\SanctumORM\Manager\TokenManager;
use Kilip\SanctumORM\Security\NewAccessToken;
use Laravel\Sanctum\TransientToken;
use Tests\Kilip\SanctumORM\Fixtures\Model\TestTokens;
use Tests\Kilip\SanctumORM\Fixtures\Model\TestUser;
use Tests\Kilip\SanctumORM\TestCase;

class TokenManagerTest extends TestCase
{
    /**
     * @var ObjectManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $omToken;

    /**
     * @var ObjectManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $omUser;

    private $tokenManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->omToken = $this->createMock(ObjectManager::class);
        $this->omUser  = $this->createMock(ObjectManager::class);

        $this->tokenManager = new TokenManager(
            $this->omToken,
            $this->omUser,
            TestTokens::class,
            TestUser::class
        );
    }

    public function testCreateToken()
    {
        $omToken = $this->omToken;
        $manager = $this->tokenManager;
        $user    = $this->createMock(SanctumUserInterface::class);

        $omToken->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(TokenModelInterface::class));
        $omToken->expects($this->once())
            ->method('flush');

        $this->assertInstanceOf(NewAccessToken::class, $manager->createToken($user, 'test'));
    }

    public function testFindToken()
    {
        $omToken    = $this->omToken;
        $repository = $this->createMock(ObjectRepository::class);
        $token      = $this->createMock(TokenModelInterface::class);
        $manager    = $this->tokenManager;

        $omToken->expects($this->once())
            ->method('getRepository')
            ->with(TestTokens::class)
            ->willReturn($repository);

        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(['token' => 'some-token'])
            ->willReturn($token);

        $this->assertSame($token, $manager->findToken('some-token'));
    }

    public function testCreateTransientToken()
    {
        $manager = $this->tokenManager;
        $user    = $this->createMock(SanctumUserInterface::class);

        $user->expects($this->once())
            ->method('withAccessToken')
            ->with($this->isInstanceOf(TransientToken::class));

        $this->assertSame($user, $manager->createTransientToken($user));
    }

    public function testUpdateAccessToken()
    {
        $omToken = $this->omToken;
        $omUser  = $this->omUser;
        $manager = $this->tokenManager;
        $user    = $this->createMock(SanctumUserInterface::class);
        $token   = $this->createMock(TokenModelInterface::class);

        $token->method('getOwner')
            ->willReturn($user);
        $token->expects($this->once())
            ->method('setLastUsedAt')
            ->with($this->isInstanceOf(\DateTime::class));

        $user->expects($this->once())
            ->method('withAccessToken')
            ->with($token);

        $omToken->expects($this->once())
            ->method('persist')
            ->with($token);
        $omUser->expects($this->once())
            ->method('persist')
            ->with($user);

        $this->assertSame($user, $manager->updateAccessToken($token));
    }
}
