<?php


namespace Kilip\DoctrineSanctum\Model;

use Doctrine\DBAL\Schema\Column;
use Doctrine\ORM\Mapping as ORM;
use Kilip\DoctrineSanctum\Contracts\SanctumUserInterface;

/**
 * Class Tokens
 *
 * @ORM\MappedSuperclass()
 *
 * @package Kilip\DoctrineSanctum\Model
 */
abstract class Tokens implements SanctumUserInterface
{

    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string",unique=true)
     *
     * @var string
     */
    protected $token;

    /**
     * @ORM\Column(type="array",nullable=true)
     *
     * @var array
     */
    protected $abilities = [];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $lastUsedAt;

    /**
     * @var string
     */
    protected $owner;
}