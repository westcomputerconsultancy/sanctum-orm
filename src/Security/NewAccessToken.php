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

namespace Kilip\DoctrineSanctum\Security;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Kilip\DoctrineSanctum\Contracts\TokenModelInterface;

class NewAccessToken implements Arrayable, Jsonable
{
    /**
     * The access token instance.
     *
     * @var TokenModelInterface
     */
    public $accessToken;

    /**
     * The plain text version of the token.
     *
     * @var string
     */
    public $plainTextToken;

    /**
     * Create a new access token result.
     *
     * @param TokenModelInterface $accessToken
     * @param string              $plainTextToken
     *
     * @return void
     */
    public function __construct(TokenModelInterface $accessToken, string $plainTextToken)
    {
        $this->accessToken    = $accessToken;
        $this->plainTextToken = $plainTextToken;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'accessToken'    => $this->accessToken,
            'plainTextToken' => $this->plainTextToken,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
