<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\Column(length: 255)]
    // private ?string $username = null;

    // #[ORM\Column(length: 255)]
    // private ?string $firstName = null;

    // #[ORM\Column(length: 255)]
    // private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private ?Profile $profile = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Recipe::class, orphanRemoval: true)]
    private Collection $recipes;

    #[ORM\ManyToMany(targetEntity: Recipe::class, inversedBy: 'usersFavorite')]
    #[ORM\JoinTable(name:'favorite_recipes')]
    private Collection $favoriteRecipes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $posts;

    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'usersFavorite')]
    #[ORM\JoinTable(name:'favorite_posts')]
    private Collection $favoritePosts;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->favoriteRecipes = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->favoritePosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // public function getUsername(): ?string
    // {
    //     return $this->username;
    // }

    // public function setUsername(string $username): self
    // {
    //     $this->username = $username;

    //     return $this;
    // }

    // public function getFirstName(): ?string
    // {
    //     return $this->firstName;
    // }

    // public function setFirstName(string $firstName): self
    // {
    //     $this->firstName = $firstName;

    //     return $this;
    // }

    // public function getLastName(): ?string
    // {
    //     return $this->lastName;
    // }

    // public function setLastName(string $lastName): self
    // {
    //     $this->lastName = $lastName;

    //     return $this;
    // }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile): self
    {
        $this->profile = $profile;

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
            $recipe->setUser($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getUser() === $this) {
                $recipe->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getFavoriteRecipes(): Collection
    {
        return $this->favoriteRecipes;
    }

    public function addFavoriteRecipe(Recipe $favoriteRecipe): self
    {
        if (!$this->favoriteRecipes->contains($favoriteRecipe)) {
            $this->favoriteRecipes->add($favoriteRecipe);
        }

        return $this;
    }

    public function removeFavoriteRecipe(Recipe $favoriteRecipe): self
    {
        $this->favoriteRecipes->removeElement($favoriteRecipe);

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getFavoritePosts(): Collection
    {
        return $this->favoritePosts;
    }

    public function addFavoritePost(Post $favoritePost): self
    {
        if (!$this->favoritePosts->contains($favoritePost)) {
            $this->favoritePosts->add($favoritePost);
        }

        return $this;
    }

    public function removeFavoritePost(Post $favoritePost): self
    {
        $this->favoritePosts->removeElement($favoritePost);

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
