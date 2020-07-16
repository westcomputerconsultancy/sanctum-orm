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

namespace Tests\Kilip\SanctumORM\Security;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Kilip\SanctumORM\Contracts\SanctumUserInterface;
use Kilip\SanctumORM\Contracts\TokenModelInterface;
use Kilip\SanctumORM\Manager\TokenManagerInterface;
use Kilip\SanctumORM\Security\Guard;
use Tests\Kilip\SanctumORM\TestCase;

class GuardTest extends TestCase
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var TokenManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $tokenManager;

    /**
     * @var AuthFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $authFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authFactory  = $this->createMock(AuthFactory::class);
        $this->tokenManager = $this->createMock(TokenManagerInterface::class);
        $this->guard        = new Guard(
            $this->authFactory,
            $this->tokenManager,
            3600,
            'users'
        );
    }

    public function testInvokeWithTransientToken()
    {
        $authFactory    = $this->authFactory;
        $manager        = $this->tokenManager;
        $guard          = $this->guard;
        $user           = $this->createMock(SanctumUserInterface::class);
        $request        = $this->createMock(Request::class);
        $statefullGuard = $this->createMock(StatefulGuard::class);

        $authFactory->expects($this->once())
            ->method('guard')
            ->with('web')
            ->willReturn($statefullGuard);

        $statefullGuard->expects($this->once())
            ->method('user')
            ->willReturn($user);

        $manager->expects($this->once())
            ->method('createTransientToken')
            ->with($user);

        $guard->__invoke($request);
    }

    public function testInvokeFromRequestBearer()
    {
        $authFactory    = $this->authFactory;
        $manager        = $this->tokenManager;
        $guard          = $this->guard;
        $user           = $this->createMock(SanctumUserInterface::class);
        $token          = $this->createMock(TokenModelInterface::class);
        $request        = $this->createMock(Request::class);
        $statefullGuard = $this->createMock(StatefulGuard::class);

        $authFactory->expects($this->once())
            ->method('guard')
            ->with('web')
            ->willReturn($statefullGuard);

        $statefullGuard->expects($this->once())
            ->method('user')
            ->willReturn(null);

        $request->expects($this->once())
            ->method('bearerToken')
            ->willReturn('some-token');

        $manager->expects($this->once())
            ->method('findToken')
            ->with('some-token')
            ->willReturn($token);

        $token->expects($this->any())
            ->method('getOwner')
            ->willReturn($user);
        $token->expects($this->once())
            ->method('getCreatedAt')
            ->willReturn(new \DateTime());
        $manager->expects($this->once())
            ->method('updateAccessToken')
            ->with($token)
            ->willReturn($user);

        $this->assertNotNull($retVal = $guard($request));
        $this->assertSame($user, $retVal);
    }

    public function testInvokeWithInvalidRequest()
    {
        $guard = $this->guard;
        $request = $this->createMock(Request::class);
        $authFactory = $this->authFactory;
        $statefullGuard = $this->createMock(StatefulGuard::class);
        
        $authFactory->expects($this->once())
            ->method('guard')
            ->with('web')
            ->willReturn($statefullGuard);

        $statefullGuard->expects($this->once())
            ->method('user')
            ->willReturn(null);

        $request
            ->expects($this->once())
            ->method('bearerToken')
            ->willReturn(null);

        $this->assertNull($guard($request));
    }
}
