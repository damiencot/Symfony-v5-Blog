<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Article", mappedBy="categories")
     */
    private $asso_article_category;

    public function __construct()
    {
        $this->asso_article_category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getAssoArticleCategory(): Collection
    {
        return $this->asso_article_category;
    }

    public function addAssoArticleCategory(Article $assoArticleCategory): self
    {
        if (!$this->asso_article_category->contains($assoArticleCategory)) {
            $this->asso_article_category[] = $assoArticleCategory;
            $assoArticleCategory->addCategory($this);


        }

        return $this;
    }

    public function removeAssoArticleCategory(Article $assoArticleCategory): self
    {
        if ($this->asso_article_category->contains($assoArticleCategory)) {
            $this->asso_article_category->removeElement($assoArticleCategory);
            $assoArticleCategory->removeCategory($this);

        }

        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->getName();
    }
}
