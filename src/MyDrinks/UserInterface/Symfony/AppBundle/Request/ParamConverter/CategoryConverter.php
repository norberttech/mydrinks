<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Request\ParamConverter;

use MyDrinks\UserInterface\Symfony\AppBundle\Drink\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryConverter implements ParamConverterInterface
{
    /**
     * @var Category[]|array
     */
    private $categories;
    
    /**
     * @var array
     */
    private $categoriesConfiguration;

    /**
     * @param array $categories
     * @param array $categoriesConfiguration
     */
    public function __construct(array $categories = [], $categoriesConfiguration = [])
    {
        foreach ($categories as $category) {
            if (!is_object($category) || !$category instanceof Category) {
                throw new NotFoundHttpException;
            }
        }
        
        $this->categories = $categories;
        $this->categoriesConfiguration = $categoriesConfiguration;
    }
    /**
     * Stores the object in the request.
     *
     * @param Request $request The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $categoryId = $this->getCategoryIdForLocale($request->getLocale(), $request->attributes->get($name, ''));
        
        if (is_null($categoryId)) {
            throw new NotFoundHttpException;
        }

        $requestedCategory = $this->findCategoryById($categoryId);
        
        if (is_null($requestedCategory)) {
            throw new NotFoundHttpException;
        }
        
        $request->attributes->set($name, $requestedCategory);
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }
        
        return $configuration->getClass() === Category::class;
    }

    /**
     * @param $locale
     * @param $requestedCategoryId
     * @return null|string
     */
    private function getCategoryIdForLocale($locale, $requestedCategoryId)
    {
        foreach ($this->categoriesConfiguration as $categoryId => $categoryConfig) {
            if (array_key_exists($locale, $categoryConfig)) {
                if ($categoryConfig[$locale] === $requestedCategoryId) {
                    return $categoryId;
                }
            }
        }
    }

    /**
     * @param $categoryId
     * @return Category|null
     */
    private function findCategoryById($categoryId)
    {
        foreach ($this->categories as $category) {
            if ($category->getId() === $categoryId) {
                return $category;
            }
        }
    }
}
