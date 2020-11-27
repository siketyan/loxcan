<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Siketyan\Loxcan\Reporter\EnvironmentTrait;

class GitHubClient
{
    use EnvironmentTrait;

    private ClientInterface $httpClient;

    public function __construct(
        ClientInterface $httpClient
    ) {
        $this->httpClient = $httpClient;
    }

    public function createComment(string $owner, string $repo, int $issueNumber, string $body): void
    {
        try {
            $this->httpClient->request(
                'POST',
                sprintf('/repos/%s/%s/issues/%d/comments', $owner, $repo, $issueNumber),
                [
                    'headers' => $this->getDefaultHeaders(),
                    'body' => json_encode([
                        'body' => $body,
                    ]),
                ],
            );
        } catch (GuzzleException $e) {
            throw new GitHubException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
    }

    private function getDefaultHeaders(): array
    {
        return [
            'Accept' => 'application/vnd.github.v3+json',
            'Authorization' => sprintf(
                'token %s',
                $this->getEnv('LOXCAN_REPORTER_GITHUB_TOKEN'),
            ),
        ];
    }
}
