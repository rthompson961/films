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
        $crawler = $client->request('GET', '/show/the-shining');

        $client->submitForm('Submit', [
            'comment_form[author]' => 'Sarah',
            'comment_form[text]' => 'Some feedback from an automated functional test',
            'comment_form[email]' => 'me@automat.ed',
            'comment_form[photofile]' => dirname(__DIR__) . '/public/uploads/' . '653babf4bbf5.jpg',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorTextSame('p', 'There are 4 comments.');
        $this->assertEquals(2, $crawler->filter('h4')->count());
        $this->assertEquals('/show/the-shining?offset=2', $crawler->filter('a')->last()->attr('href'));
    }
}
