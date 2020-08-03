<?php

namespace App\Tests;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testReviewComment()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // login to admin system
        $client->submitForm('Sign in', [
            'username' => 'admin',
            'password' => 'admin'
        ]);
        $this->assertResponseRedirects('/admin/');
        $client->followRedirect();
        $this->assertRouteSame('easyadmin');

        // publish comment #5
        $crawler = $client->request('GET', '/admin/comment/publish/5');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('p', "Applied transition: publish");

        // reject comment #6
        $crawler = $client->request('GET', '/admin/comment/reject/6');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('p', "Applied transition: reject");

        // published comment appears on page
        $crawler = $client->request('GET', '/show/the-shining');
        $this->assertSelectorTextSame('div.comment-text', 'A comment to be accepted.');
        $this->assertSelectorTextSame('div.comment-count', 'There are 4 comment(s)');

        // rejected comment state has been updated in the database
        $comment = self::$container->get(CommentRepository::class)->find(6);
        $this->assertEquals('rejected', $comment->getState());
    }
}
