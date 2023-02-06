<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePic = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bannerPic = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfilePic(): ?string
    {
        return $this->profilePic;
    }

    public function setProfilePic(?string $profilePic): self
    {
        $this->profilePic = $profilePic;

        return $this;
    }

    public function getBannerPic(): ?string
    {
        return $this->bannerPic;
    }

    public function setBannerPic(?string $bannerPic): self
    {
        $this->bannerPic = $bannerPic;

        return $this;
    }
}
