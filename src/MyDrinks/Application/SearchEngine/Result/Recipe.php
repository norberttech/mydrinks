<?php

namespace MyDrinks\Application\SearchEngine\Result;

final class Recipe 
{
    /**
     * @var string
     */
    private $slug;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $description;

    /**
     * @var null|\DateTimeImmutable
     */
    private $publishedAt;
    
    /**
     * @param string $slug
     * @param string $name
     * @param string $description
     */
    public function __construct($slug, $name, $description)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->description = $description;
        $this->publishedAt = null;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \DateTimeImmutable $dateTime
     */
    public function publishedAt(\DateTimeImmutable $dateTime)
    {
        $this->publishedAt = $dateTime;
    }
    
    /**
     * @return bool
     */
    public function isPublished()
    {
        return !is_null($this->publishedAt);
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getPublicationDate()
    {
        return $this->publishedAt;
    }
}