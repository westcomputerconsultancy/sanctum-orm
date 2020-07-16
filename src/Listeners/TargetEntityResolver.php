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

namespace Kilip\SanctumORM\Listeners;

use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Kilip\SanctumORM\Contracts\SanctumUserInterface;
use Kilip\SanctumORM\Contracts\TokenModelInterface;

class TargetEntityResolver extends ResolveTargetEntityListener
{
    public function __construct()
    {
        $tokensModel = config('sanctum_orm.doctrine.models.token');
        $userModel   = config('sanctum_orm.doctrine.models.user');
        $this->addResolveTargetEntity(TokenModelInterface::class, $tokensModel, []);
        $this->addResolveTargetEntity(SanctumUserInterface::class, $userModel, []);
    }
}
