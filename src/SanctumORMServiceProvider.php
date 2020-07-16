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

namespace Kilip\SanctumORM;

use Doctrine\ORM\Events;
use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Kilip\SanctumORM\Listeners\TargetEntityResolver;
use Kilip\SanctumORM\Manager\TokenManager;
use Kilip\SanctumORM\Manager\TokenManagerInterface;
use Kilip\SanctumORM\Security\Guard;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use LaravelDoctrine\Extensions\Timestamps\TimestampableExtension;
use LaravelDoctrine\ORM\IlluminateRegistry;

class SanctumORMServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/sanctum_orm.php' => config_path('sanctum_orm.php'),
        ]);

        $this->configureManager();
        $this->configureGuard();
        $this->configureMiddleware();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sanctum_orm.php', 'sanctum_orm');
        $this->configureDoctrine();
    }

    private function configureDoctrine()
    {
        config([
            'doctrine.managers.sanctum_orm' => [
                'connection' => config('sanctum_orm.doctrine.connection'),
                'dev'        => config('app.debug', false),
                'type'       => 'annotations',
                'paths'      => [
                    __DIR__.'/Model',
                ],
                'proxies' => [
                    'namespace'     => false,
                    'path'          => storage_path('proxies'),
                    'auto_generate' => false,
                ],
                'events' => [
                    'listeners' => [
                        Events::loadClassMetadata => TargetEntityResolver::class,
                    ],
                ],
            ],
            'doctrine.extensions' => array_merge([
                TimestampableExtension::class,
            ], config('doctrine.extensions')),
        ]);
    }

    private function configureManager()
    {
        $this->app->singleton(TokenManagerInterface::class, function (Application $app) {
            return $this->createTokenManager();
        });
        $this->app->alias(TokenManagerInterface::class, 'sanctum_orm.managers.token');
    }

    public function provides()
    {
        return [
            TokenManager::class,
        ];
    }

    /**
     * Configure the Sanctum authentication guard.
     *
     * @return void
     */
    protected function configureGuard()
    {
        Auth::resolved(function ($auth) {
            $auth->extend('sanctum', function ($app, $name, array $config) use ($auth) {
                return tap($this->createGuard($auth, $config), function ($guard) {
                    $this->app->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    /**
     * Register the guard.
     *
     * @param AuthFactory $auth
     * @param array       $config
     *
     * @return RequestGuard
     */
    protected function createGuard($auth, $config)
    {
        return new RequestGuard(
            new Guard(
                $auth,
                $this->app->get(TokenManagerInterface::class),
                (int) config('sanctum.expiration', 0),
                $config['provider']),
            $this->app['request'],
            $auth->createUserProvider()
        );
    }

    /**
     * @throws BindingResolutionException
     */
    protected function configureMiddleware()
    {
        $kernel = $this->app->make(Kernel::class);

        $kernel->prependToMiddlewarePriority(EnsureFrontendRequestsAreStateful::class);
    }

    private function createTokenManager()
    {
        /** @var IlluminateRegistry $registry */
        $registry   = $this->app->get('registry');
        $tokenModel = (string) config('sanctum_orm.doctrine.models.token');
        $userModel  = (string) config('sanctum_orm.doctrine.models.user');

        if (empty($tokenModel)) {
            throw new \InvalidArgumentException('You have to configure "sanctum_orm.doctrine.models.token"');
        }
        if (!class_exists($tokenModel)) {
            throw new \InvalidArgumentException(sprintf('Can not use doctrine orm model "%s", class not exist.', $tokenModel));
        }

        if (empty($userModel)) {
            throw new \InvalidArgumentException('You have to configure "sanctum_orm.doctrine.models.user');
        }
        if (!class_exists($userModel)) {
            throw new \InvalidArgumentException(sprintf('Can not use doctrine orm model "%s", class not exist.', $userModel));
        }

        $tokenManager = $registry->getManagerForClass($tokenModel);
        if (null === $tokenManager) {
            throw new \InvalidArgumentException(sprintf('Can not find valid Entity Manager for "%s" class.', $tokenModel));
        }

        $userManager = $registry->getManagerForClass($userModel);
        if (null === $userManager) {
            throw new \InvalidArgumentException(sprintf('Can not find valid Entity Manager for "%s" class.', $userModel));
        }

        return new TokenManager($tokenManager, $userManager, $tokenModel, $userModel);
    }
}
