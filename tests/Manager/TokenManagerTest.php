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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Kilip\SanctumORM\Contracts\SanctumUserInterface;
use Kilip\SanctumORM\Contracts\TokenModelInterface;
use Kilip\SanctumORM\Manager\TokenManager;
use Kilip\SanctumORM\Security\NewAccessToken;
use Laravel\Sanctum\TransientToken;
use PHPUnit\Framework\MockObject\MockObject as PHPUnitMockObject;
use Tests\Kilip\SanctumORM\Fixtures\Model\TestTokens;
use Tests\Kilip\SanctumORM\Fixtures\Model\TestUser;
use Tests\Kilip\SanctumORM\TestCase;

class TokenManagerTest extends TestCase
{
    /**
     * @var ObjectManager|PHPUnitMockObject
     */
    private $omToken;

    /**
     * @var ObjectManager|PHPUnitMockObject
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

    public function testFindTokenWithDelimiter()
    {
        $omToken        = $this->omToken;
        $repository     = $this->createMock(ObjectRepository::class);
        $token          = $this->createMock(TokenModelInterface::class);
        $manager        = $this->tokenManager;
        $plainTextToken = Str::random(80);
        $hashed         = hash('sha256', $plainTextToken);

        $token->expects($this->once())
            ->method('getToken')
            ->willReturn($hashed);

        $omToken->expects($this->any())
            ->method('getRepository')
            ->with(TestTokens::class)
            ->willReturn($repository);

        $repository->expects($this->exactly(2))
            ->method('find')
            ->with('id')
            ->willReturnOnConsecutiveCalls(
                null, $token
            );

        $formattedToken = 'id|'.$plainTextToken;
        $this->assertNull($manager->findToken($formattedToken));
        $this->assertSame($token, $manager->findToken($formattedToken));
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
        $this->assertSame($user, $manager->updateAccessToken($token));
    }

    public function testFindUserByUsernameOrEmail()
    {
        $manager    = $this->tokenManager;
        $omUser     = $this->omUser;
        $repository = $this->createMock(ObjectRepository::class);
        $user       = $this->createMock(SanctumUserInterface::class);

        $omUser->method('getRepository')
            ->with(TestUser::class)
            ->willReturn($repository);

        $repository->expects($this->at(0))
            ->method('findOneBy')
            ->with(['username'=>'username'])
            ->willReturn($user);
        $repository->expects($this->at(1))
            ->method('findOneBy')
            ->with(['username'=>'email'])
            ->willReturn(null);
        $repository->expects($this->at(2))
            ->method('findOneBy')
            ->with(['email'=>'email'])
            ->willReturn($user);
        $repository->expects($this->at(3))
            ->method('findOneBy')
            ->with(['username'=>'foo'])
            ->willReturn(null);
        $repository->expects($this->at(4))
            ->method('findOneBy')
            ->with(['email'=>'foo'])
            ->willReturn(null);

        $this->assertSame($user, $manager->findUserByUsernameOrEmail('username'));
        $this->assertSame($user, $manager->findUserByUsernameOrEmail('email'));
        $this->assertNull($manager->findUserByUsernameOrEmail('foo'));
    }
}
