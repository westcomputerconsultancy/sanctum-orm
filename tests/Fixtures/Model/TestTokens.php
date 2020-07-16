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
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Kilip\SanctumORM\Model\Tokens;

/**
 * Class TestTokens.
 *
 * @ORM\Entity
 */
class TestTokens extends Tokens implements Jsonable, Arrayable
{
    public function toArray()
    {
        return [
            'name'      => $this->getName(),
            'token'     => $this->getToken(),
            'abilities' => $this->getAbilities(),
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
