<?php

namespace App\Entity;

use App\Repository\VideoTrickRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoTrickRepository::class)]
class VideoTrick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $link;

    #[ORM\ManyToOne(targetEntity: Trick::class, inversedBy: 'Video')]
    #[ORM\JoinColumn(nullable: false)]
    private $Trick;

    public function __toString()
    {
        return $this->link;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->Trick;
    }

    public function setTrick(?Trick $Trick): self
    {
        $this->Trick = $Trick;

        return $this;
    }
}
