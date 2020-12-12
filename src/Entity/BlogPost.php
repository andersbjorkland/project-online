<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 * @Vich\Uploadable
 */
class BlogPost
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
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blogPosts")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDraft;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $summary;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, mappedBy="blogPosts", cascade={"persist"})
     */
    private $categories;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $permanentSlug;

	/**
	 * @ORM\Column(type="string", length=100, nullable=true)
	 * @var string
	 */
	private $thumbnail;

	/**
	 * @Vich\UploadableField(mapping="thumbnails", fileNameProperty="thumbnail")
	 */
	private $thumbnailFile;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $updatedAt;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
	    $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt( DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt( DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getPublishAt(): ?DateTimeInterface
    {
        return $this->publishAt;
    }

    public function setPublishAt(?DateTimeInterface $publishAt): self
    {
        $this->publishAt = $publishAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIsDraft(): ?bool
    {
        return $this->isDraft;
    }

    public function setIsDraft(bool $isDraft): self
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
	        $category->addBlogPost($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
	        $category->removeBlogPost($this);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPermanentSlug(): ?string
    {
        return $this->permanentSlug;
    }

    public function setPermanentSlug(string $permanentSlug): self
    {
        $this->permanentSlug = $permanentSlug;

        return $this;
    }

	public function getThumbnail(): ?string
	{
		return $this->thumbnail;
	}

	public function setThumbnail(?string $thumbnail): self
	{
		$this->thumbnail = $thumbnail;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getThumbnailFile() {
		return $this->thumbnailFile;
	}

	/**
	 * @param mixed $imageFile
	 */
	public function setThumbnailFile($thumbnailFile): void
	{
		$this->thumbnailFile = $thumbnailFile;

		if ($thumbnailFile) {
			$this->updatedAt = new DateTime();
		}
	}

	public function getUpdatedAt(): ?DateTimeInterface
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?DateTimeInterface $updatedAt): self
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	public function __toString(): string {
        return $this->title . " - " . $this->getCreatedAt()->format("Y-m-d h:m:s");
    }



}
