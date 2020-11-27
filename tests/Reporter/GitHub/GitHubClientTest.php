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

    private ObjectProphecy $httpClient;
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
    public function testGetMe(): void
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn(<<<'EOS'
{
    "id": 123,
    "login": "foobar"
}
EOS);

        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn($stream->reveal());

        $this->httpClient
            ->request(
                'GET',
                '/user',
                [
                    'headers' => [
                        'Accept' => 'application/vnd.github.v3+json',
                        'Authorization' => 'token dummy_token',
                    ],
                ]
            )
            ->willReturn($response->reveal())
            ->shouldBeCalledOnce()
        ;

        $this->userPool->get(123)->willReturn(null);
        $this->userPool->add(Argument::type(GitHubUser::class))->shouldBeCalledOnce();

        $me = $this->client->getMe();

        $this->assertInstanceOf(GitHubUser::class, $me);
        $this->assertSame(123, $me->getId());
        $this->assertSame('foobar', $me->getLogin());
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

        $user = $this->prophesize(GitHubUser::class)->reveal();

        $this->httpClient
            ->request(
                'GET',
                '/repos/foo/bar/issues/123/comments',
                [
                    'headers' => [
                        'Accept' => 'application/vnd.github.v3+json',
                        'Authorization' => 'token dummy_token',
                    ],
                ]
            )
            ->willReturn($response->reveal())
            ->shouldBeCalledOnce()
        ;

        $pool = $this->userPool;

        $this->userPool->get(111)->willReturn(null)->shouldBeCalledTimes(2);
        $this->userPool
            ->add(Argument::type(GitHubUser::class))
            ->will(function ($args) use ($pool) {
                /** @noinspection PhpUndefinedMethodInspection */
                $pool->get(111)->willReturn($args[0]);
            })
            ->shouldBeCalledOnce();

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
                ]
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
}
