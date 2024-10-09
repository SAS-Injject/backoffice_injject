<?php

namespace App\Entity;

use App\Repository\CustomersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomersRepository::class)]
class Customers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $logo_name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $logo_file = null;

    #[ORM\Column(length: 255)]
    private ?string $logo_alt = null;

    #[ORM\Column(length: 255)]
    private ?string $logo_legend = null;

    /**
     * @var Collection<int, Realization>
     */
    #[ORM\OneToMany(targetEntity: Realization::class, mappedBy: 'customer')]
    private Collection $realizations;

    public function __construct()
    {
        $this->realizations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLogoName(): ?string
    {
        return $this->logo_name;
    }

    public function setLogoName(string $logo_name): static
    {
        $this->logo_name = $logo_name;

        return $this;
    }

    public function getLogoFile(): ?string
    {
        return $this->logo_file;
    }

    public function setLogoFile(string $logo_file): static
    {
        $this->logo_file = $logo_file;

        return $this;
    }

    public function getLogoAlt(): ?string
    {
        return $this->logo_alt;
    }

    public function setLogoAlt(string $logo_alt): static
    {
        $this->logo_alt = $logo_alt;

        return $this;
    }

    public function getLogoLegend(): ?string
    {
        return $this->logo_legend;
    }

    public function setLogoLegend(string $logo_legend): static
    {
        $this->logo_legend = $logo_legend;

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
            $realization->setCustomer($this);
        }

        return $this;
    }

    public function removeRealization(Realization $realization): static
    {
        if ($this->realizations->removeElement($realization)) {
            // set the owning side to null (unless already changed)
            if ($realization->getCustomer() === $this) {
                $realization->setCustomer(null);
            }
        }

        return $this;
    }
}
