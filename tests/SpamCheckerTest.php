<?php

namespace App\Tests;

use App\Entity\Comment;
use App\Service\SpamChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SpamCheckerTest extends TestCase
{
    public function testSpamWithInvalidRequest()
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();

        $client = new MockHttpClient(
            [new MockResponse(
                'invalid',
                ['response_headers' => ['x-akismet-debug-help: Invalid key']]
            )]
        );
        $checker = new SpamChecker($client, 'abc', 'localhost');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to check for spam: invalid (Invalid key).');
        $checker->isSpam($comment, []);
    }

    /**
     * @dataProvider getComments
     */
    public function testSpam(bool $expected, ResponseInterface $response, Comment $comment)
    {
        $client = new MockHttpClient([$response]);
        $checker = new SpamChecker($client, 'abc', 'localhost');
        $result = $checker->isSpam($comment, []);

        $this->assertSame($expected, $result);
    }

    public function getComments(): iterable
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();

        $response = new MockResponse('true');
        yield 'spam' => [true, $response, $comment];

        $response = new MockResponse('false');
        yield 'notspam' => [false, $response, $comment];
    }
}
