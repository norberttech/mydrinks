<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Twig\Extension;

use MyDrinks\Domain\Recipe\Step;

final class GlassExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $publicDirPath;

    /**
     * @var string
     */
    private $glassAssetsUrlPath;
    
    /**
     * @param string $publicDirPath
     * @param string $glassAssetsUrlPath
     */
    public function __construct($publicDirPath, $glassAssetsUrlPath)
    {
        $this->publicDirPath = rtrim($publicDirPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->glassAssetsUrlPath = trim($glassAssetsUrlPath, DIRECTORY_SEPARATOR);
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'glass';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction("getGlassImage",[$this, 'getGlassImage'],['is_safe' => ['html']]),
        ];
    }

    /**
     * @param $name
     * @return bool
     */
    public function getGlassImage($name, $extension = 'png')
    {
        $filePath = $this->publicDirPath . $name . '.' . $extension;
        
        if (file_exists($filePath)) {
            return DIRECTORY_SEPARATOR . $this->glassAssetsUrlPath . DIRECTORY_SEPARATOR . $name . '.' . $extension;
        }

        return DIRECTORY_SEPARATOR . $this->glassAssetsUrlPath . DIRECTORY_SEPARATOR . 'glass.png';
    }
}