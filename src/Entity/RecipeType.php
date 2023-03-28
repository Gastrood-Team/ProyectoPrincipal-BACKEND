<?php

namespace App\Entity;

use App\Repository\RecipeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

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

    #[ORM\ManyToMany(targetEntity: Recipe::class, mappedBy: 'types')]
    private Collection $recipes;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
            $recipe->addType($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->removeElement($recipe)) {
            $recipe->removeType($this);
        }

        return $this;
    }
}
