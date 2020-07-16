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

namespace Kilip\DoctrineSanctum\Manager;

use Doctrine\Persistence\ObjectManager;
use Illuminate\Support\Str;
use Kilip\DoctrineSanctum\Contracts\SanctumUserInterface;
use Kilip\DoctrineSanctum\Contracts\TokenModelInterface;
use Kilip\DoctrineSanctum\Security\NewAccessToken;
use Laravel\Sanctum\TransientToken;

class TokenManager implements TokenManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $omToken;

    /**
     * @var ObjectManager
     */
    protected $omUser;

    /**
     * @var string
     */
    protected $tokenModel;

    /**
     * @var string
     */
    protected $userModel;

    public function __construct(
        ObjectManager $tokenManager,
        ObjectManager $userManager,
        string $tokenModel,
        string $userModel
    ) {
        $this->omToken = $tokenManager;
        $this->omUser = $userManager;
        $this->tokenModel = $tokenModel;
        $this->userModel = $userModel;
    }

    /**
     * @param array $criteria
     * @return SanctumUserInterface|object|null
     */
    public function findUserBy(array $criteria)
    {
        return $this->omUser->getRepository($this->userModel)->findOneBy($criteria);
    }

    public function createToken(SanctumUserInterface $user, string $name, array $abilities=['*'])
    {
        /* @var TokenModelInterface $token */

        $plainTextToken = Str::random(80);
        $token = new $this->tokenModel();
        $token->setName($name)
            ->setToken(hash('sha256',$plainTextToken))
            ->setAbilities($abilities)
            ->setOwner($user);
        $this->storeToken($token);

        return new NewAccessToken($token,$token->getId().'|'.$plainTextToken);
    }

    /**
     * {@inheritDoc}
     */
    public function findToken(string $token)
    {
        $repository = $this->omToken->getRepository($this->tokenModel);
        if(false === strpos($token,'|')){
            return $repository->findOneBy(['token' => $token]);
        }

        [$id, $token] = explode('|', $token, 2);

        /* @var TokenModelInterface $instance */
        if($instance = $repository->find($id)){
            return hash_equals($instance->getToken(), hash('sha256',$token)) ? $instance:null;
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function createTransientToken(SanctumUserInterface $user)
    {
        /* @var \Kilip\DoctrineSanctum\Contracts\TokenModelInterface $token */
        $user->withAccessToken(new TransientToken());

        return $user;
    }

    /**
     * @param TokenModelInterface $token
     * @return SanctumUserInterface
     */
    public function updateAccessToken(TokenModelInterface $token)
    {
        $omToken = $this->omToken;

        $token->setLastUsedAt(now());
        $token->getOwner()->withAccessToken($token);

        $this->storeToken($token);
        $this->storeUser($token->getOwner());

        return $token->getOwner();
    }

    public function storeUser(SanctumUserInterface $user, $andFlush=true)
    {
        $om = $this->omUser;

        $om->persist($user);
        if($andFlush){
            $om->flush();
        }
    }

    public function storeToken(TokenModelInterface $token, $andFlush = true)
    {
        $om = $this->omToken;
        $om->persist($token);
        if($andFlush){
            $om->flush();
        }
    }
}
