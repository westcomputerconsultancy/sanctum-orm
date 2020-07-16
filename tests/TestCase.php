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

namespace Tests\Kilip\DoctrineSanctum;

use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Illuminate\Support\Facades\Hash;
use Kilip\DoctrineSanctum\Listeners\TargetEntityResolver;
use Kilip\DoctrineSanctum\SanctumORMServiceProvider;
use Laravel\Sanctum\SanctumServiceProvider;
use LaravelDoctrine\Extensions\GedmoExtensionsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Tests\Kilip\DoctrineSanctum\Fixtures\Model\TestTokens;
use Tests\Kilip\DoctrineSanctum\Fixtures\Model\TestUser;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        include_once __DIR__ . '/Fixtures/routes.php';

        $this->artisan('doctrine:schema:create');
    }

    protected function getPackageProviders($app)
    {
        return [
            DoctrineServiceProvider::class,
            GedmoExtensionsServiceProvider::class,
            SanctumServiceProvider::class,
            SanctumORMServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $app['config'];

        $config->set('doctrine.managers.default.paths', [
            __DIR__ . '/Fixtures/Model',
        ]);
        $config->set('doctrine.managers.default.events.listeners', [
            Events::loadClassMetadata => TargetEntityResolver::class
        ]);

        $config->set('auth.providers.users.driver', 'doctrine');
        $config->set('auth.providers.users.model', TestUser::class);
        $config->set('sanctum_orm.doctrine.models.token', TestTokens::class);
        $config->set('sanctum_orm.doctrine.models.user', TestUser::class);
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     *
     * @return object|TestUser|null
     */
    protected function createUser($username = 'test', $password = 'test', $email = 'test@example.com')
    {
        $user = $this->getRepository(TestUser::class)
            ->findOneBy(['username' => $username]);
        if(!$user){
            $manager = $this->getManager(TestUser::class);
            $user = new TestUser();
            $user->setUsername($username)
                ->setEmail($email)
                ->setPassword(Hash::make($password));

            $manager->persist($user);
            $manager->flush();
        }

        return $user;
    }

    /**
     * @param $className
     * @return ObjectRepository
     */
    protected function getRepository($className)
    {
        $manager = $this->getManager($className);
        return $manager->getRepository($className);
    }

    /**
     * @param $className
     * @return ObjectManager
     */
    protected function getManager($className)
    {
        return app()->get('registry')->getManagerForClass($className);
    }
}
