<?php

namespace MyDrinks\Application\Recipe;

use MyDrinks\Application\Exception\InvalidArgumentException;

class Image
{
    const EXTENSION_JPG = 'jpeg';
    
    /**
     * @var array
     */
    private $forbiddenCharacters = ['<', '>', ':', '|', "\"", "\\", '?', '*'];

    /**
     * @var array
     */
    private $availableExtensions = ['jpg', 'jpeg'];
    
    /**
     * @var
     */
    private $name;

    /**
     * @var string
     */
    private $content;

    /**
     * @param string $name
     * @param string $content
     * @throws InvalidArgumentException
     */
    public function __construct($name, $content)
    {
        $this->validateName($name);
        $this->validateExtension($name);
        
        if (empty($content)) {
            throw new InvalidArgumentException("Image content can't be empty.");
        }
        
        $this->name = $name;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     * @throws InvalidArgumentException
     */
    private function validateName($name)
    {
        foreach ($this->forbiddenCharacters as $character) {
            if (strpos($name, $character) !== false) {
                throw new InvalidArgumentException(sprintf("Image name can't contains \"%s\" character.", $character));
            }
        }
    }

    /**
     * @param $name
     * @throws InvalidArgumentException
     */
    private function validateExtension($name)
    {
        $pathInfo = pathInfo($name);
        if (!array_key_exists('extension', $pathInfo)) {
            throw new InvalidArgumentException("Image extension can't be empty.");
        }

        if (!in_array($pathInfo['extension'], $this->availableExtensions)) {
            throw new InvalidArgumentException("Image needs to have jpeg extension.");
        }
    }
}
