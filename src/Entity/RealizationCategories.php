<?php

namespace App\Entity;

use App\Repository\RealizationCategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealizationCategoriesRepository::class)]
class RealizationCategories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $account = null;

    /**
     * @var Collection<int, Realization>
     */
    #[ORM\ManyToMany(targetEntity: Realization::class, mappedBy: 'categories')]
    private Collection $realizations;

    public function __construct()
    {
        $this->realizations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(string $account): static
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Collection<int, Realization>
     */
    public function getRealizations(): Collection
    {
        return $this->realizations;
    }

    public function addRealization(Realization $realization): static
    {
        if (!$this->realizations->contains($realization)) {
            $this->realizations->add($realization);
            $realization->addCategory($this);
        }

        return $this;
    }

    public function removeRealization(Realization $realization): static
    {
        if ($this->realizations->removeElement($realization)) {
            $realization->removeCategory($this);
        }

        return $this;
    }
}
