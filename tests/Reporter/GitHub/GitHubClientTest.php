<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;

class GitHubClientTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $httpClient;
    private GitHubClient $client;

    protected function setUp(): void
    {
        $this->httpClient = $this->prophesize(ClientInterface::class);

        $this->client = new GitHubClient(
            $this->httpClient->reveal(),
        );

        putenv('LOXCAN_REPORTER_GITHUB_TOKEN=dummy_token');
    }

    /**
     * @throws GuzzleException
     */
    public function test(): void
    {
        $this->httpClient
            ->request(
                'POST',
                '/repos/foo/bar/issues/123/comments',
                [
                    'body' => '{"body":"dummy_body"}',
                    'headers' => [
                        'Authorization' => 'token dummy_token',
                    ],
                ]
            )
            ->willReturn($this->prophesize(ResponseInterface::class)->reveal())
            ->shouldBeCalledOnce()
        ;

        $this->client->comment(
            'foo',
            'bar',
            123,
            'dummy_body',
        );
    }
}
