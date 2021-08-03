<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1000)
     * @Assert\NotBlank
     * @Assert\Length(max=1000)
     */
    private $name_en;

    /**
     * @ORM\Column(type="string", length=1000)
     * @Assert\NotBlank
     * @Assert\Length(max=1000)
     */
    private $name_ru;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, inversedBy="books")
     */
    private $authors;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameEn(): ?string
    {
        return $this->name_en;
    }

    public function setNameEn(string $name_en): self
    {
        $this->name_en = $name_en;

        return $this;
    }

    public function getNameRu(): ?string
    {
        return $this->name_ru;
    }

    public function setNameRu(string $name_ru): self
    {
        $this->name_ru = $name_ru;

        return $this;
    }

    public function getName(): ?string
    {
        $names = [];

        if ($this->getNameEn())
        {
            $names[] = $this->getNameEn();
        }

        if ($this->getNameRu())
        {
            $names[] = $this->getNameRu();
        }

        return (count($names) > 0)
            ? implode('|', $names)
            : null;
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author))
        {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function jsonSerialize(): array
    {
        $result = [
            'Id' => $this->getId(),
            'Name' => $this->getName(),
            'Author' => [],
            ];

        $authors = $this->getAuthors();

        if ($authors)
        {
            foreach ($authors as $author)
            {
                $result['Author'][] = $author->jsonSerialize();
            }
        }

        return $result;
    }
}
