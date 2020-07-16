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

namespace Tests\Kilip\SanctumORM\Model;

use Kilip\SanctumORM\Contracts\TokenModelInterface;
use Tests\Kilip\SanctumORM\Fixtures\Model\TestUser;
use Tests\Kilip\SanctumORM\TestCase;

class SanctumUserTraitTest extends TestCase
{
    public function testTokenCan()
    {
        $accessToken = $this->createMock(TokenModelInterface::class);
        $ob          = new TestUser();

        $accessToken->expects($this->once())
            ->method('can')
            ->with('create-user')
            ->willReturn(true);

        $this->assertFalse($ob->tokenCan('create-user'));

        $ob->withAccessToken($accessToken);
        $this->assertTrue($ob->tokenCan('create-user'));

        $this->assertSame($accessToken, $ob->currentAccessToken());
    }
}
