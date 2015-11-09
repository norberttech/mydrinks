<?php

namespace MyDrinks\Tests\Functional\Page;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;

abstract class BasePage implements Page
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Crawler|null
     */
    protected $crawler;
    /**
     * @var Page
     */
    private $previousPage;

    /**
     * @param Client $client
     * @param Page $previousPage
     */
    public function __construct(Client $client, Page $previousPage = null)
    {
        $this->client = $client;
        $this->previousPage = $previousPage;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return Page
     */
    public function open($method = 'GET', $parameters = [])
    {
        $this->crawler = $this->client->request($method, $this->unmaskUrl($parameters));
        
        if (!in_array($this->client->getResponse()->getStatusCode(), [200, 302])) {
            throw new \RuntimeException(sprintf("Can't open \"%s\"", $this->getUrl()));
        }
        
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function shouldBeRedirectedFrom($url) 
    {
        if (is_null($this->previousPage)) {
            throw new \RuntimeException(sprintf("Page \"%s\" was not open by redirection.", $this->getUrl()));
        }
     
        if ($this->previousPage->getUrl() !== $url) {
            throw new \RuntimeException(sprintf("Previous page url was \"%s\".", $this->previousPage->getUrl()));
        }
        
        return $this;
    }

    /**
     * @param $url
     * @return $this
     */
    public function shouldBeRedirectedTo($url) 
    {
        if ($this->getUrl() !== $url) {
            throw new \RuntimeException(sprintf("Current page url is \"%s\".", $this->previousPage->getUrl()));
        }
        
        return $this;
    }

    /**
     * @param array $urlParameters
     * @return mixed|string
     */
    private function unmaskUrl(array $urlParameters)
    {
        $url = $this->getUrl();
        foreach ($urlParameters as $parameter => $value) {
            $url = str_replace(sprintf('{%s}', $parameter), $value, $url);
        }
        
        return $url;
    }
}