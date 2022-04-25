<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
/**
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"title"},
 *     errorPath="title",
 *     message="Ce titre est déjà utilisé."
 * )
 */
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_category;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_user;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $featuredPicture;

    #[ORM\OneToMany(mappedBy: 'Trick', targetEntity: ImageTrick::class)]
    private $Image;

    #[ORM\OneToMany(mappedBy: 'Trick', targetEntity: VideoTrick::class)]
    private $Video;

    #[ORM\OneToMany(
        mappedBy: 'Trick',
        targetEntity: Comment::class, 
        orphanRemoval: true,
    )]
    private $Comments;

    public function __construct()
    {
        $this->Image = new ArrayCollection();
        $this->Video = new ArrayCollection();
        $this->Comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCategory(): ?Category
    {
        return $this->id_category;
    }

    public function setIdCategory(?Category $id_category): self
    {
        $this->id_category = $id_category;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFeaturedPicture(): ?string
    {
        return $this->featuredPicture;
    }

    public function setFeaturedPicture(?string $featuredPicture): self
    {
        $this->featuredPicture = $featuredPicture;

        return $this;
    }

    /**
     * @return Collection<int, ImageTrick>
     */
    public function getImage(): Collection
    {
        return $this->Image;
    }

    public function addImage(ImageTrick $image): self
    {
        if (!$this->Image->contains($image)) {
            $this->Image[] = $image;
            $image->setTrick($this);
        }

        return $this;
    }

    public function removeImage(ImageTrick $image): self
    {
        if ($this->Image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getTrick() === $this) {
                $image->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VideoTrick>
     */
    public function getVideo(): Collection
    {
        return $this->Video;
    }

    public function addVideo(VideoTrick $video): self
    {
        if (!$this->Video->contains($video)) {
            $this->Video[] = $video;
            $video->setTrick($this);
        }

        return $this;
    }

    public function removeVideo(VideoTrick $video): self
    {
        if ($this->Video->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getTrick() === $this) {
                $video->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->Comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->Comments->contains($comment)) {
            $this->Comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->Comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }
}
