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

namespace Tests\Kilip\SanctumORM;

use Doctrine\Persistence\ObjectManager;
use Tests\Kilip\SanctumORM\Fixtures\Model\TestUser;

class SanctumORMServiceProviderTest extends TestCase
{
    public function testDoctrineConfiguration()
    {
        $em     = app()->get('registry')->getManagerForClass(TestUser::class);

        $this->assertInstanceOf(ObjectManager::class, $em);
    }
}
