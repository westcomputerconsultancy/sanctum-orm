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

namespace Kilip\DoctrineSanctum\Model;

use Doctrine\ORM\Mapping as ORM;
use Kilip\DoctrineSanctum\Contracts\TokenModelInterface;
use Laravel\Sanctum\Contracts\HasAbilities;

trait SanctumUserTrait
{
    /**
     * @var HasAbilities
     */
    protected $accessToken;

    /**
     * @ORM\OneToMany(targetEntity="Kilip\DoctrineSanctum\Contracts\TokenModelInterface", mappedBy="owner")
     *
     * @var TokenModelInterface[]
     */
    protected $tokens;

    /**
     * @param TokenModelInterface $token
     */
    public function addToken(TokenModelInterface $token)
    {
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    public function tokenCan(string $ability)
    {
    }

    public function createToken(string $name, array $abilities=['*'])
    {
    }

    public function currentAccessToken()
    {
    }

    public function withAccessToken($accessToken)
    {
    }
}
