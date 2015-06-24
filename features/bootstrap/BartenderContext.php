<?php

namespace Feature;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class BartenderContext implements Context, SnippetAcceptingContext
{
    public function __construct()
    {
    }
}
