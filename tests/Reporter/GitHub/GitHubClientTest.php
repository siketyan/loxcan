<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GitHubClientTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<ClientInterface>
     */
    private ObjectProphecy $httpClient;

    /**
     * @var ObjectProphecy<GitHubUserPool>
     */
    private ObjectProphecy $userPool;

    private GitHubClient $client;

    protected function setUp(): void
    {
        $this->httpClient = $this->prophesize(ClientInterface::class);
        $this->userPool = $this->prophesize(GitHubUserPool::class);

        $this->client = new GitHubClient(
            $this->httpClient->reveal(),
            $this->userPool->reveal(),
        );

        putenv('LOXCAN_REPORTER_GITHUB_TOKEN=dummy_token');
    }

    /**
     * @throws GuzzleException
     */
    public function testGetComments(): void
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn(<<<'EOS'
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

        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn($stream->reveal());

        $this->httpClient
            ->request(
                'GET',
                '/repos/foo/bar/issues/123/comments',
                [
                    'headers' => [
                        'Accept' => 'application/vnd.github.v3+json',
                        'Authorization' => 'token dummy_token',
                    ],
                ],
            )
            ->willReturn($response->reveal())
            ->shouldBeCalledOnce()
        ;

        $pool = $this->userPool;

        $this->userPool->get(111)->willReturn(null)->shouldBeCalledTimes(2);
        $this->userPool
            ->add(Argument::type(GitHubUser::class))
            ->will(function (array $args) use ($pool): void {
                /* @noinspection PhpUndefinedMethodInspection */
                $pool->get(111)->willReturn($args[0]);
            })
            ->shouldBeCalledOnce()
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

    /**
     * @throws GuzzleException
     */
    public function testCreateComment(): void
    {
        $this->httpClient
            ->request(
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
            ->willReturn($this->prophesize(ResponseInterface::class)->reveal())
            ->shouldBeCalledOnce()
        ;

        $this->client->createComment(
            'foo',
            'bar',
            123,
            'dummy_body',
        );
    }

    /**
     * @throws GuzzleException
     */
    public function testUpdateComment(): void
    {
        $comment = $this->prophesize(GitHubComment::class);
        $comment->getId()->willReturn(123);

        $this->httpClient
            ->request(
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
            ->willReturn($this->prophesize(ResponseInterface::class)->reveal())
            ->shouldBeCalledOnce()
        ;

        $this->client->updateComment(
            'foo',
            'bar',
            $comment->reveal(),
            'dummy_body',
        );
    }
}
