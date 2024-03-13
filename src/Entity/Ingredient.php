<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
#UniqueEntity qui nous permet de pas pouvoir push deux fois le même ingrédients en BDD
#[UniqueEntity('name')]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    //Assert pour Gérer le Minimum et le Maximum possible.
    #[Assert\Length(min: 2, max: 50)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column]
    //Assert pour que le Prix soit forcément positif
    #[Assert\Range(
        minMessage: "Le prix ne peut pas être infèrieur a 0",
        maxMessage: "Le prix ne peut pas être supèrieur à 200",

        max: 200)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?float $price = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $createdAt ;

    //Constructeur de DateTimeImmutable lors d'un CreatedAt
    public  function __construct()
    {

        $this->createdAt = new \DateTimeImmutable();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    public function __toString()
    {
        return $this->name;
    }
}
