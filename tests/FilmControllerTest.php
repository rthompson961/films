<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilmControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(2, $crawler->filter('h4')->count());
    }

    public function testShow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/show/1');

        $this->assertSelectorTextSame('p', 'There are 3 comments.');
        $this->assertEquals(2, $crawler->filter('h4')->count());
        $this->assertEquals('/show/1?offset=2', $crawler->filter('a')->attr('href'));
    }
}
