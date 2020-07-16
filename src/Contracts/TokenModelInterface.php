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

namespace Kilip\SanctumORM\Contracts;

use Laravel\Sanctum\Contracts\HasAbilities;

interface TokenModelInterface extends HasAbilities
{
    /**
     * @return string|int
     */
    public function getId();

    /**
     * @param \DateTime $date
     *
     * @return static
     */
    public function setLastUsedAt(\DateTime $date);

    /**
     * @return \DateTime
     */
    public function getLastUsedAt();

    /**
     * @param \DateTime $date
     *
     * @return static
     */
    public function setCreatedAt(\DateTime $date);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return SanctumUserInterface
     */
    public function getOwner();

    /**
     * @param SanctumUserInterface $user
     *
     * @return static
     */
    public function setOwner(SanctumUserInterface $user);

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $token
     *
     * @return static
     */
    public function setToken(string $token);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param array $abilities
     *
     * @return static
     */
    public function setAbilities(array $abilities=['*']);

    /**
     * @return array
     */
    public function getAbilities();
}
