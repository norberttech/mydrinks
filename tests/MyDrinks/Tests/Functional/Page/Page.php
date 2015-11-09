<?php

namespace MyDrinks\Tests\Functional\Page;

interface Page 
{
    /**
     * @param string $method
     * @return Page
     */
    public function open($method = 'GET');

    /**
     * @return string;
     */
    public function getUrl();

    /**
     * @param string $url
     * @throws \RuntimeException
     * @return Page
     */
    public function shouldBeRedirectedFrom($url);

    /**
     * @param string $url
     * @throws \RuntimeException
     * @return Page
     */
    public function shouldBeRedirectedTo($url);
}