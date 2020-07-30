<?php

namespace App\Service;

use App\Entity\Comment;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpamChecker
{
    private $client;
    private $endpoint;
    private $publicDns;

    public function __construct(HttpClientInterface $client, string $akismetKey, string $publicDns)
    {
        $this->client = $client;
        $this->endpoint = sprintf('https://%s.rest.akismet.com/1.1/comment-check', $akismetKey);
        $this->publicDns = $publicDns;
    }

    /**
     * @return bool true if message is flagged as spam
     * email akismet-guaranteed-spam@example.com will always return true
     *
     * @throws \RuntimeException if the call did not work
     */
    public function isSpam(Comment $comment, array $context): bool
    {
        $response = $this->client->request('POST', $this->endpoint, [
            'body' => array_merge($context, [
                'blog' => $this->publicDns,
                'comment_type' => 'comment',
                'comment_author' => $comment->getAuthor(),
                'comment_author_email' => $comment->getEmail(),
                'comment_content' => $comment->getText(),
                'comment_date_gmt' => $comment->getCreatedAt()->format('c'),
                'blog_lang' => 'en',
                'blog_charset' => 'UTF-8',
                'is_test' => true
            ])
        ]);
        $headers = $response->getHeaders();

        // something went wrong
        if (isset($headers['x-akismet-debug-help'][0])) {
            throw new \RuntimeException(sprintf(
                'Unable to check for spam: %s (%s).',
                $response->getContent(),
                $headers['x-akismet-debug-help'][0]
            ));
        }

        if ($response->getContent() === 'true') {
            return true;
        }
        return false;
    }
}
