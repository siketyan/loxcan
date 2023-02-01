<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Siketyan\Loxcan\Reporter\EnvironmentTrait;

class GitHubClient
{
    use EnvironmentTrait;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly GitHubUserPool $userPool,
    ) {
    }

    /**
     * @return GitHubComment[]
     *
     * @throws \JsonException
     */
    public function getComments(string $owner, string $repo, int $issueNumber): array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                sprintf('/repos/%s/%s/issues/%d/comments', $owner, $repo, $issueNumber),
                ['headers' => $this->getDefaultHeaders()],
            );
        } catch (GuzzleException $e) {
            throw new GitHubException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }

        $json = $response->getBody()->getContents();
        $assoc = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
        $comments = [];

        foreach ($assoc as $row) {
            $comments[] = new GitHubComment(
                $row['id'],
                $row['body'],
                $this->getOrCreateUser($row['user']),
            );
        }

        return $comments;
    }

    /**
     * @throws \JsonException
     */
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
                    ], \JSON_THROW_ON_ERROR),
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

    /**
     * @throws \JsonException
     */
    public function updateComment(string $owner, string $repo, GitHubComment $comment, string $body): void
    {
        try {
            $this->httpClient->request(
                'PATCH',
                sprintf('/repos/%s/%s/issues/comments/%d', $owner, $repo, $comment->getId()),
                [
                    'headers' => $this->getDefaultHeaders(),
                    'body' => json_encode([
                        'body' => $body,
                    ], \JSON_THROW_ON_ERROR),
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

    /**
     * @return array{Accept: string, Authorization: string}
     */
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

    private function getOrCreateUser(array $assoc): GitHubUser
    {
        $id = $assoc['id'];
        $user = $this->userPool->get($id);

        if (!$user instanceof GitHubUser) {
            $user = new GitHubUser($id, $assoc['login']);
            $this->userPool->add($user);
        }

        return $user;
    }
}
