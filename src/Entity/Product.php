<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity(fields: ['sku'], message: 'Товар с таким SKU уже существует')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Название товара обязательно для заполнения.')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Название должно содержать минимум {{ limit }} символа.',
        maxMessage: 'Название не может быть длиннее {{ limit }} символов.'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'SKU обязателен для заполнения.')]
    #[Assert\Length(
        max: 64,
        maxMessage: 'SKU не может быть длиннее {{ limit }} символов.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Z]{2,5}-\d{3,6}$/',
        message: 'SKU должен быть в формате ABC-123 (латинские буквы, дефис, цифры).'
    )]
    private ?string $sku = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Цена обязательна для заполнения.')]
    #[Assert\Positive(message: 'Цена должна быть положительным числом.')]
    private ?string $price = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isArchived = false;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Assert\NotNull(message: 'Выберите категорию для товара.')]
    private ?Category $category = null;

    /**
     * @var Collection<int, Operation>
     */
    #[ORM\OneToMany(targetEntity: Operation::class, mappedBy: 'product')]
    private Collection $operations;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
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

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): static
    {
        if (!$this->operations->contains($operation)) {
            $this->operations->add($operation);
            $operation->setProduct($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): static
    {
        if ($this->operations->removeElement($operation)) {
            if ($operation->getProduct() === $this) {
                $operation->setProduct(null);
            }
        }

        return $this;
    }
}