<?php

namespace App\Entity;

use App\Repository\PuzzleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PuzzleRepository::class)
 */
class Puzzle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sentence;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSentence(): ?string
    {
        return $this->sentence;
    }

    public function setSentence(string $sentence): self
    {
        $this->sentence = $sentence;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    private function getImageFullPath(): ?string
    {
        $domain = $_ENV['BACKEND_URL'];
        $path = $_ENV['PUZZLE_IMAGE_PATH'];

        return $domain . $path . $this->getImage();
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function toArray()
    {
        return [
            "id" => $this->getId(),
            "sentence" => $this->getSentence(),
            "image" => $this->getImageFullPath()
        ];
    }
}
