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

use Kilip\SanctumORM\Contracts\SanctumUserInterface;
use Tests\Kilip\SanctumORM\Fixtures\Model\TestTokens;
use Tests\Kilip\SanctumORM\TestCase;

class TokensTest extends TestCase
{
    /**
     * @param string $name
     * @param mixed  $value
     * @param $defaultValue
     * @param bool $fluent
     * @dataProvider getTestAttributes
     */
    public function testAttributes($name, $value, $defaultValue = null, $fluent = true)
    {
        $ob = new TestTokens();
        $this->verifyAttributes($ob, $name, $value, $defaultValue, $fluent);
    }

    public function getTestAttributes()
    {
        $owner = $this->createMock(SanctumUserInterface::class);

        return [
            ['name', 'some-name'],
            ['token', 'some-token'],
            ['abilities', ['foo'], []],
            ['lastUsedAt', new \DateTime()],
            ['owner', $owner],
        ];
    }

    public function testCanDetermineWhatItCanAndCantDo()
    {
        $token = new TestTokens();

        $this->assertEmpty($token->getAbilities());
        $this->assertFalse($token->can('foo'));

        $token->setAbilities(['foo']);

        $this->assertTrue($token->can('foo'));
        $this->assertFalse($token->can('bar'));
        $this->assertTrue($token->cant('bar'));
        $this->assertFalse($token->cant('foo'));

        $token->setAbilities(['foo', '*']);

        $this->assertTrue($token->can('foo'));
        $this->assertTrue($token->can('bar'));
    }
}
