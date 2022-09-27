<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvitationRepository::class)
 */
class Invitation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $send_by;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $send_to;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sender_status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invited_status;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSendBy(): ?string
    {
        return $this->send_by;
    }

    public function setSendBy(string $send_by): self
    {
        $this->send_by = $send_by;

        return $this;
    }

    public function getSendTo(): ?string
    {
        return $this->send_to;
    }

    public function setSendTo(string $send_to): self
    {
        $this->send_to = $send_to;

        return $this;
    }

    public function getSenderStatus(): ?string
    {
        return $this->sender_status;
    }

    public function setSenderStatus(string $sender_status): self
    {
        $this->sender_status = $sender_status;

        return $this;
    }

    public function getInvitedStatus(): ?string
    {
        return $this->invited_status;
    }

    public function setInvitedStatus(?string $invited_status): self
    {
        $this->invited_status = $invited_status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
