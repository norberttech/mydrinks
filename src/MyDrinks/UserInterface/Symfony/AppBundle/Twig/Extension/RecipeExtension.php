<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Twig\Extension;

use MyDrinks\Application\Recipe\ImageStorage;
use MyDrinks\Application\Recipe\ImageUrlResolver;
use MyDrinks\Domain\Recipe\Step;
use Twig_Environment;

final class RecipeExtension extends \Twig_Extension
{
    /**
     * @var ImageStorage
     */
    private $imageStorage;
    
    
    /**
     * @var ImageUrlResolver
     */
    private $imageUrlResolver;

    /**
     * @param ImageStorage $imageStorage
     * @param ImageUrlResolver $imageUrlResolver
     */
    public function __construct(ImageStorage $imageStorage, ImageUrlResolver $imageUrlResolver)
    {
        $this->imageStorage = $imageStorage;
        $this->imageUrlResolver = $imageUrlResolver;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'recipe';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                "renderStep", 
                [$this, 'renderStep'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true
                ]
            ),
            new \Twig_SimpleFunction("hasImageForRecipe",[$this, 'hasImage'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction("getImagePathForRecipe",[$this, 'getImagePath'],['is_safe' => ['html']])
        ];
    }

    /**
     * @param $slug
     * @return bool
     */
    public function hasImage($slug)
    {
        return $this->imageStorage->hasImageFor($slug);
    }

    /**
     * @param $slug
     * @return bool
     */
    public function getImagePath($slug)
    {
        return $this->imageUrlResolver->resolveUrlFor($this->imageStorage->getPathFor($slug));
    }
    
    /**
     * @param Step $step
     * @return string
     */
    public function renderStep(Twig_Environment $environment, Step $step)
    {
        $reflection = new \ReflectionClass(get_class($step));
        
        return $environment->render(
            sprintf(':recipe/step:%s.html.twig', lcfirst($reflection->getShortName())), 
            ['step' => $step]
        );
    }
}