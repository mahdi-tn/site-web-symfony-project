<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\ORM\Mapping as ORM;
// Assert for validation
use Symfony\Component\Validator\Constraints as Assert;
// Collection for OneToMany
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Products
{
    #[ORM\Id]
    #[ORM\Column(length: 10)]
    #[Assert\Length(
        min: 4,
        max: 10,
        minMessage: 'Your p_ref must be at least {{ limit }} characters long',
        maxMessage: 'Your p_ref cannot be longer than {{ limit }} characters',
    )]
    private ?string $p_ref = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 4,
        max: 100,
        minMessage: 'Your p_name must be at least {{ limit }} characters long',
        maxMessage: 'Your p_name cannot be longer than {{ limit }} characters',
    )]
    private ?string $p_name = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 0,
        max: 9999,
        notInRangeMessage: 'p_stock must be between {{ min }} and {{ max }} to SUBMIT',
    )]
    private ?int $p_stock = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1.001,
        max: 999999.999,
        notInRangeMessage: 'p_price must be between {{ min }} and {{ max }} to SUBMIT',
    )]
    private ?float $p_price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $p_img = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your p_category must be at least {{ limit }} characters long',
        maxMessage: 'Your p_category cannot be longer than {{ limit }} characters',
    )]
    private ?string $p_category = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your p_color must be at least {{ limit }} characters long',
        maxMessage: 'Your p_color cannot be longer than {{ limit }} characters',
    )]
    private ?string $p_color = null;

    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: "product", cascade: ["remove"])]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->p_img = "noLink"; // Default value
        $this->p_category = "no category was set"; // Default value
        $this->p_color = "no color was set"; // Default value
    }

    #[ORM\PrePersist]
    public function generatePRef()
    {
        // Get the current date and time in the format YYYYMMDDHHMMSS
        $currentDateTime = date('YmdHis');
        $this->setPRef('prod' . $currentDateTime);
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getPRef(): ?string
    {
        return $this->p_ref;
    }

    public function setPRef(string $p_ref): static
    {
        $this->p_ref = $p_ref;
        return $this;
    }

    public function getPName(): ?string
    {
        return $this->p_name;
    }

    public function setPName(string $p_name): static
    {
        $this->p_name = $p_name;

        return $this;
    }

    public function getPStock(): ?int
    {
        return $this->p_stock;
    }

    public function setPStock(int $p_stock): static
    {
        $this->p_stock = $p_stock;

        return $this;
    }

    public function getPPrice(): ?float
    {
        return $this->p_price;
    }

    public function setPPrice(float $p_price): static
    {
        $this->p_price = $p_price;

        return $this;
    }

    public function getPImg(): ?string
    {
        return $this->p_img;
    }

    public function setPImg(?string $p_img): static
    {
        $this->p_img = $p_img;

        return $this;
    }

    public function getPCategory(): ?string
    {
        return $this->p_category;
    }

    public function setPCategory(string $p_category): static
    {
        $this->p_category = $p_category;

        return $this;
    }

    public function getPColor(): ?string
    {
        return $this->p_color;
    }

    public function setPColor(string $p_color): static
    {
        $this->p_color = $p_color;

        return $this;
    }
}
