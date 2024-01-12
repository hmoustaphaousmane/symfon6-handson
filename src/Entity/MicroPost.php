<?php

namespace App\Entity;

use App\Repository\MicroPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MicroPostRepository::class)]
class MicroPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 5, max:255, minMessage: 'Title is too short, 5 characters minimum')]
    private string $title;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 5, max:500)]
    private string $text;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $created;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $ccomments;

    public function __construct()
    {
        $this->ccomments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getCcomments(): Collection
    {
        return $this->ccomments;
    }

    public function addCcomment(Comment $ccomment): static
    {
        if (!$this->ccomments->contains($ccomment)) {
            $this->ccomments->add($ccomment);
            $ccomment->setPost($this);
        }

        return $this;
    }

    public function removeCcomment(Comment $ccomment): static
    {
        if ($this->ccomments->removeElement($ccomment)) {
            // set the owning side to null (unless already changed)
            if ($ccomment->getPost() === $this) {
                $ccomment->setPost(null);
            }
        }

        return $this;
    }
}
