<?php

namespace App\Entity;

use App\Repository\RealizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealizationRepository::class)]
class Realization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $period = null;

    #[ORM\Column(length: 255)]
    private ?string $duration = null;

    #[ORM\Column]
    private ?bool $is_published = null;

    /**
     * @var Collection<int, RealizationPhoto>
     */
    #[ORM\OneToMany(targetEntity: RealizationPhoto::class, mappedBy: 'realization')]
    private Collection $photos;

    /**
     * @var Collection<int, RealizationCategories>
     */
    #[ORM\ManyToMany(targetEntity: RealizationCategories::class, inversedBy: 'realizations')]
    private Collection $categories;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $context = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $task = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $answer = null;

    #[ORM\ManyToOne(inversedBy: 'realizations')]
    private ?Customers $customer = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $client_view = null;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    public function getPeriod(): ?\DateTimeImmutable
    {
        return $this->period;
    }

    public function setPeriod(\DateTimeImmutable $period): static
    {
        $this->period = $period;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): static
    {
        $this->duration = $duration;

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

    /**
     * @return Collection<int, RealizationPhoto>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(RealizationPhoto $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setRealization($this);
        }

        return $this;
    }

    public function removePhoto(RealizationPhoto $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getRealization() === $this) {
                $photo->setRealization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RealizationCategories>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(RealizationCategories $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(RealizationCategories $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function getTask(): ?string
    {
        return $this->task;
    }

    public function setTask(string $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getCustomer(): ?Customers
    {
        return $this->customer;
    }

    public function setCustomer(?Customers $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getClientView(): ?string
    {
        return $this->client_view;
    }

    public function setClientView(string $client_view): static
    {
        $this->client_view = $client_view;

        return $this;
    }
}
