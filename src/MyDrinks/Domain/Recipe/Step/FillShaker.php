<?php

namespace MyDrinks\Domain\Recipe\Step;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Step;

final class FillShaker implements Step
{
    /**
     * @var Name
     */
    private $contentName;

    public function __construct(Name $content)
    {
        $this->contentName = $content;
    }

    /**
     * @return Name
     */
    public function getContentName()
    {
        return $this->contentName;
    }
}