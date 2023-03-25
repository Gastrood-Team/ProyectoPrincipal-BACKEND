<?php

namespace App\Entity;

use App\Repository\RecipeTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeTypeRepository::class)]
class RecipeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $recipeTypeName = null;

    #[ORM\Column(length: 255)]
    private ?string $recipeTypeImage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipeTypeName(): ?string
    {
        return $this->recipeTypeName;
    }

    public function setRecipeTypeName(string $recipeTypeName): self
    {
        $this->recipeTypeName = $recipeTypeName;

        return $this;
    }

    public function getRecipeTypeImage(): ?string
    {
        return $this->recipeTypeImage;
    }

    public function setRecipeTypeImage(string $recipeTypeImage): self
    {
        $this->recipeTypeImage = $recipeTypeImage;

        return $this;
    }
}
