<?php

namespace App\Tests;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilmControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(2, $crawler->filter('h4')->count());
        $this->assertSelectorTextSame('h4', 'The Shining');
        $this->assertEquals('/show/the-shining', $crawler->filter('div.card-body a')->attr('href'));
    }

    public function testShow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/show/the-shining');

        $client->submitForm('Submit', [
            'comment_form[author]' => $author = 'Sarah',
            'comment_form[text]' => 'Some feedback from an automated functional test',
            'comment_form[email]' => $email = 'me@automat.ed',
            'comment_form[photofile]' => dirname(__DIR__) . '/public/uploads/653babf4bbf5.jpg',
        ]);

        // simulate comment validation
        $comment = self::$container->get(CommentRepository::class)->findOneByEmail($email);
        $comment->setState('published');
        self::$container->get(EntityManagerInterface::class)->flush();

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorTextSame('div.alert', 'Thank you, your comment will be posted after moderation.');
        $this->assertEquals(2, $crawler->filter('h4')->count());
        $this->assertSelectorTextSame('h4', $author);
        $this->assertSelectorTextSame('div.comment-count', 'There are 4 comment(s)');
        $this->assertEquals('/show/the-shining?offset=2', $crawler->filter('a.next')->attr('href'));
    }
}
