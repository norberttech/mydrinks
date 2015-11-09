<?php

namespace MyDrinks\Infrastructure\UserInterface\SlugGenerator;

use MyDrinks\Application\SlugGenerator;
use Cocur\Slugify\Slugify;

class SlugifyAdapter implements SlugGenerator
{
    /**
     * @var Slugify
     */
    private $slugify;

    /**
     * @param Slugify $slugify
     */
    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }
    
    /**
     * @param string $string
     * @return string
     */
    public function generateFrom($string)
    {
        return $this->slugify->slugify($string);
    }
}
