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

namespace Kilip\SanctumORM\Model;

use Doctrine\ORM\Mapping as ORM;
use Kilip\SanctumORM\Contracts\TokenModelInterface;

trait SanctumUserTrait
{
    /**
     * @var TokenModelInterface
     */
    protected $accessToken;

    /**
     * @ORM\OneToMany(targetEntity="Kilip\SanctumORM\Contracts\TokenModelInterface", mappedBy="owner")
     *
     * @var TokenModelInterface[]
     */
    protected $tokens;

    /**
     * {@inheritdoc}
     */
    public function addToken(TokenModelInterface $token)
    {
        $token->setOwner($this);

        return $this;
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    public function tokenCan(string $ability)
    {
        return $this->accessToken ? $this->accessToken->can($ability) : false;
    }

    public function currentAccessToken()
    {
        return $this->accessToken;
    }

    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }
}
