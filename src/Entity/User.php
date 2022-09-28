<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=Invitation::class, mappedBy="sender", fetch="EAGER")
     */
    private $sendInvitations;

    /**
     * @ORM\OneToMany(targetEntity=Invitation::class, mappedBy="invited", fetch="EAGER")
     */
    private $receivedInvitations;

    public function __construct()
    {
        $this->sendInvitations = new ArrayCollection();
        $this->receivedInvitations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getSendInvitations(): Collection
    {
        return $this->sendInvitations;
    }

    public function addSendInvitation(Invitation $sendInvitation): self
    {
        if (!$this->sendInvitations->contains($sendInvitation)) {
            $this->sendInvitations[] = $sendInvitation;
            $sendInvitation->setSender($this);
        }

        return $this;
    }

    public function removeSendInvitation(Invitation $sendInvitation): self
    {
        if ($this->sendInvitations->removeElement($sendInvitation)) {
            // set the owning side to null (unless already changed)
            if ($sendInvitation->getSender() === $this) {
                $sendInvitation->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getReceivedInvitations(): Collection
    {
        return $this->receivedInvitations;
    }

    public function addReceivedInvitation(Invitation $receivedInvitation): self
    {
        if (!$this->receivedInvitations->contains($receivedInvitation)) {
            $this->receivedInvitations[] = $receivedInvitation;
            $receivedInvitation->setInvited($this);
        }

        return $this;
    }

    public function removeReceivedInvitation(Invitation $receivedInvitation): self
    {
        if ($this->receivedInvitations->removeElement($receivedInvitation)) {
            // set the owning side to null (unless already changed)
            if ($receivedInvitation->getInvited() === $this) {
                $receivedInvitation->setInvited(null);
            }
        }

        return $this;
    }


}
