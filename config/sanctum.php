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
    'orm' => [
        'manager_name' => 'default',
        'models' => [
            'token' => null,
            'user'  => config('auth.providers.users.model'),
        ],
        'services' => [
            'token' => 'sanctum.orm.token_manager',
        ]
    ],
];
