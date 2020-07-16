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

namespace Tests\Kilip\SanctumORM\Fixtures\Model;

use Doctrine\ORM\Mapping as ORM;
use Kilip\SanctumORM\Model\Tokens;

/**
 * Class TestTokens.
 *
 * @ORM\Entity
 */
class TestTokens extends Tokens
{
}
