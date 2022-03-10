<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Index(columns: ['title', 'description'], flags: ['fulltext'])]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'array', nullable: true)]
    private $urls = [];

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(min: 5, max: 255)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $coverFilename;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    private $price;

    #[ORM\ManyToOne(targetEntity: Tag::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private $tag;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'articles')]
    private $reservedBy;

    #[ORM\Column(type: 'text', nullable: true)]
    private $reservedText;

    #[ORM\Column(type: 'string', length: 255)]
    private $status = 'draft';

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $reservedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrls(): ?array
    {
        return $this->urls;
    }

    public function setUrls(?array $urls): self
    {
        $this->urls = $urls;

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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCoverFilename(): ?string
    {
        return $this->coverFilename;
    }

    public function setCoverFilename(?string $coverFilename): self
    {
        $this->coverFilename = $coverFilename;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getReservedBy(): ?User
    {
        return $this->reservedBy;
    }

    public function setReservedBy(?User $reservedBy): self
    {
        $this->reservedBy = $reservedBy;

        return $this;
    }

    public function getReservedText(): ?string
    {
        return $this->reservedText;
    }

    public function setReservedText(?string $reservedText): self
    {
        $this->reservedText = $reservedText;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getReservedAt(): ?DateTimeImmutable
    {
        return $this->reservedAt;
    }

    public function setReservedAt(?DateTimeImmutable $reservedAt): self
    {
        $this->reservedAt = $reservedAt;

        return $this;
    }
}
