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

namespace Tests\Kilip\DoctrineSanctum\Fixtures\Model;

use Doctrine\ORM\Mapping as ORM;
use Kilip\DoctrineSanctum\Contracts\SanctumUserInterface;
use Kilip\DoctrineSanctum\Model\SanctumUserTrait;
use LaravelDoctrine\ORM\Auth\Authenticatable as AuthenticatableTrait;

/**
 * Class TestUser.
 *
 * @ORM\Entity
 */
class TestUser implements SanctumUserInterface
{
    use AuthenticatableTrait;
    use SanctumUserTrait;

    /**
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id
     *
     * @var string
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $email;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return TestUser
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return TestUser
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return TestUser
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}