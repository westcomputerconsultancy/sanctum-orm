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

namespace Kilip\DoctrineSanctum\Manager;

use Doctrine\Persistence\ObjectManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Kilip\DoctrineSanctum\Contracts\SanctumUserInterface;
use Kilip\DoctrineSanctum\Contracts\TokenModelInterface;
use Kilip\DoctrineSanctum\Security\NewAccessToken;

interface TokenManagerInterface
{
    /**
     * Creates new token for given user
     *
     * @param SanctumUserInterface $user
     * @param string $name
     * @param array $abilities
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
     * @param SanctumUserInterface|Authenticatable $user
     *
     * @return SanctumUserInterface
     */
    public function createTransientToken(SanctumUserInterface $user);

    /**
     * @param TokenModelInterface $token
     * @return SanctumUserInterface
     */
    public function updateAccessToken(TokenModelInterface $token);

    /**
     * @param array $criteria
     * @return SanctumUserInterface|null
     */
    public function findUserBy(array $criteria);
}
