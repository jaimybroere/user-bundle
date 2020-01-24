<?php

declare(strict_types=1);

/*
 * This file is part of the user bundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\UserBundle\Security\Traits;

use ConnectHolland\UserBundle\Entity\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait OwnableEntityTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="ConnectHolland\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var ConnectHolland\UserBundle\Entity\User|null
     */
    protected $owner;

    public function getOwner(): ?User
    {
        return $this->user;
    }

    public function setOwner(?UserInterface $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getOwners(): Collection
    {
        $owners = new ArrayCollection();
        if ($this->owner !== null) {
            $owners->add($this->owner);
        }

        return $owners;
    }

    public function addOwner(UserInterface $owner): void
    {
        $this->setOwner($owner);
    }
}