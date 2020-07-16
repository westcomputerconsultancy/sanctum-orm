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

namespace Kilip\SanctumORM\Manager;

use Illuminate\Contracts\Auth\Authenticatable;
use Kilip\SanctumORM\Contracts\SanctumUserInterface;
use Kilip\SanctumORM\Contracts\TokenModelInterface;
use Kilip\SanctumORM\Security\NewAccessToken;

interface TokenManagerInterface
{
    /**
     * Creates new token for given user.
     *
     * @param SanctumUserInterface $user
     * @param string               $name
     * @param array                $abilities
     *
     * @return NewAccessToken
     */
    public function createToken(SanctumUserInterface $user, string $name, array $abilities=['*']);

    /**
     * @param string $token
     *
     * @return TokenModelInterface|null
     */
    public function findToken(string $token);

    /**
     * @param Authenticatable|SanctumUserInterface $user
     *
     * @return SanctumUserInterface
     */
    public function createTransientToken($user);

    /**
     * @param TokenModelInterface $token
     *
     * @return SanctumUserInterface|Authenticatable
     */
    public function updateAccessToken(TokenModelInterface $token);

    /**
     * @param array $criteria
     *
     * @return SanctumUserInterface|null
     */
    public function findUserBy(array $criteria);
}
