<?php

namespace MyDrinks\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase;
use MyDrinks\Application\Recipe\Storage;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WebTestCase extends BaseTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Storage
     */
    protected $storage;
    
    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->storage = $this->client->getContainer()->get('my_drinks.recipe.storage');

        $fs = new Filesystem();
        $fs->remove($this->client->getContainer()->getParameter("recipes_upload_target_dir"));
        $this->authenticateAsAdmin();
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->client->getContainer()->getParameter("recipes_upload_target_dir"));
        parent::tearDown();
    }

    public function authenticateAsAdmin()
    {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken('admin', null, $firewall, array('ROLE_ADMIN'));
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
    
    /**
     * @return bool
     */
    public function isElasticSearchAvailable()
    {
        try {
            $info = $this->client->getContainer()->get('elasticsearch.client')->info();
            
            if (!isset($info['status']) || $info['status'] !== 200) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}