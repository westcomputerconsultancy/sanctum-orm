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

return [
    'doctrine' => [
        'models' => [
            'token' => null,
            'user'  => config('auth.providers.users.model'),
        ],
        'connection' => config('database.default'),
    ],
    'managers' => [
        'token' => 'sanctum_orm.token_manager',
    ],
];
