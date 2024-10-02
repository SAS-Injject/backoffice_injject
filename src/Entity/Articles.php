<?php

namespace App\Entity;

use App\Repository\ArticlesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
class Articles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $writen_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $modified_at = null;

    #[ORM\Column]
    private ?bool $is_important = null;

    #[ORM\Column]
    private ?int $seen = null;

    #[ORM\Column]
    private ?bool $is_published = null;

    #[ORM\Column(nullable:true)]
    private ?\DateTimeImmutable $published_at = null;

    /**
     * @var Collection<int, ArticlesCategories>
     */
    #[ORM\ManyToMany(targetEntity: ArticlesCategories::class, inversedBy: 'articles')]
    private Collection $categories;

    /**
     * @var Collection<int, Users>
     */
    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'articles')]
    private Collection $authors;

    #[ORM\Column(length: 255)]
    private ?string $thumbnail_name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $thumbnail_file = null;

    #[ORM\Column(length: 255)]
    private ?string $thumbnail_alt = null;

    #[ORM\Column(length: 255)]
    private ?string $thumbnail_legend = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $summary = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->authors = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getWritenAt(): ?\DateTimeImmutable
    {
        return $this->writen_at;
    }

    public function setWritenAt(\DateTimeImmutable $writen_at): static
    {
        $this->writen_at = $writen_at;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modified_at;
    }

    public function setModifiedAt(\DateTimeImmutable $modified_at): static
    {
        $this->modified_at = $modified_at;

        return $this;
    }

    public function isImportant(): ?bool
    {
        return $this->is_important;
    }

    public function setIsImportant(bool $is_important): static
    {
        $this->is_important = $is_important;

        return $this;
    }

    public function getSeen(): ?int
    {
        return $this->seen;
    }

    public function setSeen(int $seen): static
    {
        $this->seen = $seen;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished(bool $is_published): static
    {
        $this->is_published = $is_published;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->published_at;
    }

    public function setPublishedAt(\DateTimeImmutable $published_at): static
    {
        $this->published_at = $published_at;

        return $this;
    }

    /**
     * @return Collection<int, ArticlesCategories>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(ArticlesCategories $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(ArticlesCategories $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Users $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Users $author): static
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function getThumbnailName(): ?string
    {
        return $this->thumbnail_name;
    }

    public function setThumbnailName(string $thumbnail_name): static
    {
        $this->thumbnail_name = $thumbnail_name;

        return $this;
    }

    public function getThumbnailFile(): ?string
    {
        return $this->thumbnail_file;
    }

    public function setThumbnailFile(string $thumbnail_file): static
    {
        $this->thumbnail_file = $thumbnail_file;

        return $this;
    }

    public function getThumbnailAlt(): ?string
    {
        return $this->thumbnail_alt;
    }

    public function setThumbnailAlt(string $thumbnail_alt): static
    {
        $this->thumbnail_alt = $thumbnail_alt;

        return $this;
    }

    public function getThumbnailLegend(): ?string
    {
        return $this->thumbnail_legend;
    }

    public function setThumbnailLegend(string $thumbnail_legend): static
    {
        $this->thumbnail_legend = $thumbnail_legend;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }
}
