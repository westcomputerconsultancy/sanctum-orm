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

namespace Tests\Kilip\SanctumORM;

use Doctrine\Persistence\ObjectManager;
use Kilip\SanctumORM\Manager\TokenManagerInterface;
use Kilip\SanctumORM\SanctumORMServiceProvider;
use Tests\Kilip\SanctumORM\Fixtures\Model\TestUser;

interface TestIlluminateRegistry
{
    public function getManagerForClass($class);
}

class SanctumORMServiceProviderTest extends TestCase
{
    public function testShouldProvideServices()
    {
        $provider = $this->app->getProvider(SanctumORMServiceProvider::class);
        $this->assertContains(TokenManagerInterface::class, $provider->provides());
    }

    public function testDoctrineConfiguration()
    {
        $em     = app()->get('registry')->getManagerForClass(TestUser::class);

        $this->assertInstanceOf(ObjectManager::class, $em);
    }

    /**
     * @param string $configKey
     * @param mixed  $configVal
     * @param string $exceptionMessage
     * @param string $exception
     * @dataProvider getTokenManagerConfigErrorData
     */
    public function testTokenManagerConfigError($configKey, $configVal, $exceptionMessage = '', $exception=\InvalidArgumentException::class)
    {
        /** @var \Illuminate\Config\Repository $config */
        $app    = clone $this->app;
        $config = $this->app['config'];
        $config->set($configKey, $configVal);

        $this->expectException($exception);
        if ('' !== $exceptionMessage) {
            $this->expectExceptionMessageMatches($exceptionMessage);
        }
        $app->get(TokenManagerInterface::class);
    }

    public function getTokenManagerConfigErrorData()
    {
        return [
            ['sanctum_orm.doctrine.models.user', '', '/^You have to configure/'],
            ['sanctum_orm.doctrine.models.token', '', '/^You have to configure/'],
            ['sanctum_orm.doctrine.models.user', 'Foo\\Class', '/^Can not use doctrine orm model/'],
            ['sanctum_orm.doctrine.models.token', 'Foo\\Class', '/^Can not use doctrine orm model/'],
        ];
    }

    public function testTokenManagerErrorOnNullUserManager()
    {
        $registry = \Mockery::mock(TestIlluminateRegistry::class, function ($mock) {
            $manager =\Mockery::mock(ObjectManager::class);
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('getManagerForClass')
                ->andReturns($manager, null);
        });

        $this->expectException(\InvalidArgumentException::class);

        $this->instance('registry', $registry);
        $this->app->get(TokenManagerInterface::class);
    }

    public function testTokenManagerErrorOnNullTokenManager()
    {
        $registry = \Mockery::mock(TestIlluminateRegistry::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('getManagerForClass')
                ->andReturns(null);
        });

        $this->expectException(\InvalidArgumentException::class);

        $this->instance('registry', $registry);
        $this->app->get(TokenManagerInterface::class);
    }
}
