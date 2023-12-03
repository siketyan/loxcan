<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GitHubClientTest extends TestCase
{
    private ClientInterface&MockObject $httpClient;
    private GitHubUserPool&MockObject $userPool;

    private GitHubClient $client;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->userPool = $this->createMock(GitHubUserPool::class);

        $this->client = new GitHubClient(
            $this->httpClient,
            $this->userPool,
        );

        putenv('LOXCAN_REPORTER_GITHUB_TOKEN=dummy_token');
    }

    public function testGetComments(): void
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('getContents')->willReturn(<<<'EOS'
            [
                {
                    "id": 123,
                    "body": "foo",
                    "user": {
                        "id": 111,
                        "login": "abc"
                    }
                },
                {
                    "id": 456,
                    "body": "bar",
                    "user": {
                        "id": 111,
                        "login": "abc"
                    }
                }
            ]
            EOS);

        $response = $this->createStub(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                '/repos/foo/bar/issues/123/comments',
                [
                    'headers' => [
                        'Accept' => 'application/vnd.github.v3+json',
                        'Authorization' => 'token dummy_token',
                    ],
                ],
            )
            ->willReturn($response)
        ;

        $user = null;

        $this->userPool
            ->expects($this->exactly(2))
            ->method('get')
            ->with(111)
            ->willReturnCallback(static function () use (&$user): ?GitHubUser {
                return $user;
            })
        ;

        $this->userPool
            ->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(GitHubUser::class))
            ->willReturnCallback(static function (GitHubUser $u) use (&$user): void {
                $user = $u;
            })
        ;

        $comments = $this->client->getComments('foo', 'bar', 123);

        $this->assertContainsOnlyInstancesOf(GitHubComment::class, $comments);
        $this->assertCount(2, $comments);
        $this->assertSame(123, $comments[0]->getId());
        $this->assertSame('foo', $comments[0]->getBody());
        $this->assertSame(456, $comments[1]->getId());
        $this->assertSame('bar', $comments[1]->getBody());
        $this->assertSame($comments[0]->getAuthor(), $comments[1]->getAuthor());
    }

    public function testCreateComment(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/repos/foo/bar/issues/123/comments',
                [
                    'body' => '{"body":"dummy_body"}',
                    'headers' => [
                        'Accept' => 'application/vnd.github.v3+json',
                        'Authorization' => 'token dummy_token',
                    ],
                ],
            )
            ->willReturn($this->createStub(ResponseInterface::class))
        ;

        $this->client->createComment(
            'foo',
            'bar',
            123,
            'dummy_body',
        );
    }

    public function testUpdateComment(): void
    {
        $comment = $this->createStub(GitHubComment::class);
        $comment->method('getId')->willReturn(123);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'PATCH',
                '/repos/foo/bar/issues/comments/123',
                [
                    'body' => '{"body":"dummy_body"}',
                    'headers' => [
                        'Accept' => 'application/vnd.github.v3+json',
                        'Authorization' => 'token dummy_token',
                    ],
                ],
            )
            ->willReturn($this->createStub(ResponseInterface::class))
        ;

        $this->client->updateComment(
            'foo',
            'bar',
            $comment,
            'dummy_body',
        );
    }
}
